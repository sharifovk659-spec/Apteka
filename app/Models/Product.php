<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\MediaUrl;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'short_description',
        'description',
        'composition',
        'usage_instructions',
        'contraindications',
        'manufacturer',
        'country',
        'dosage_form',
        'dosage',
        'requires_prescription',
        'price',
        'old_price',
        'stock',
        'status',
        'main_image',
        'is_active',
        'is_featured',
        'is_daily_product',
        'is_bestseller',
    ];

    protected function casts(): array
    {
        return [
            'requires_prescription' => 'boolean',
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_daily_product' => 'boolean',
            'is_bestseller' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function mainImageUrl(): string
    {
        if ($this->relationLoaded('images')) {
            $primary = $this->images->firstWhere('is_primary', true)
                ?? $this->images->sortBy('sort_order')->first();

            if ($primary) {
                return $primary->imageUrl();
            }
        }

        if ($this->main_image) {
            return MediaUrl::fromStorage($this->main_image, 'images/placeholders/product.svg');
        }

        if (! $this->relationLoaded('images')) {
            $primaryPath = $this->images()
                ->where('is_primary', true)
                ->value('image')
                ?? $this->images()->orderBy('sort_order')->value('image');

            if ($primaryPath) {
                return MediaUrl::fromStorage($primaryPath, 'images/placeholders/product.svg');
            }
        }

        return MediaUrl::fromStorage(null, 'images/placeholders/product.svg');
    }

    public function scopeWithListingRelations(Builder $query): Builder
    {
        return $query->with([
            'brand:id,name,slug',
            'category:id,name,slug',
            'images' => fn ($relation) => $relation
                ->select(['id', 'product_id', 'image', 'alt_text', 'sort_order', 'is_primary'])
                ->where('is_primary', true),
        ]);
    }

    public function galleryImages()
    {
        if ($this->relationLoaded('images')) {
            return $this->images->sortBy('sort_order')->values();
        }

        return $this->images()->orderBy('sort_order')->get();
    }

    public function hasDiscount(): bool
    {
        if ($this->old_price === null || (float) $this->old_price <= (float) $this->price) {
            return false;
        }

        return (($this->old_price - $this->price) / $this->old_price) >= 0.05;
    }

    public function discountPercent(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round((($this->old_price - $this->price) / $this->old_price) * 100);
    }

    public function formattedPrice(): string
    {
        return $this->formatMoney((float) $this->price);
    }

    public function formattedOldPrice(): ?string
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return $this->formatMoney((float) $this->old_price);
    }

    private function formatMoney(float $amount): string
    {
        $decimals = fmod($amount, 1.0) === 0.0 ? 0 : 2;

        return number_format($amount, $decimals, '.', ' ').' смн';
    }
}
