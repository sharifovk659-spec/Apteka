<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function coverProduct(): HasOne
    {
        return $this->hasOne(Product::class)
            ->select(['id', 'category_id', 'main_image', 'name', 'is_featured'])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderByDesc('is_featured')
            ->orderByDesc('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getFullPathAttribute(): string
    {
        $parts = [$this->name];
        $parent = $this->relationLoaded('parent') ? $this->parent : $this->parent()->first();

        while ($parent) {
            array_unshift($parts, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' → ', $parts);
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            return MediaUrl::fromStorage($this->image, 'images/placeholders/product.svg');
        }

        return $this->coverImageUrl();
    }

    public function coverImageUrl(): string
    {
        if ($this->relationLoaded('coverProduct') && $this->coverProduct) {
            return $this->coverProduct->mainImageUrl();
        }

        return asset('images/placeholders/product.svg');
    }

    public function iconName(): string
    {
        return match ($this->icon) {
            'vitamin' => 'vitamin',
            'hygiene' => 'hygiene',
            'medical' => 'medical',
            'baby' => 'baby',
            'cosmetic' => 'beauty',
            'first-aid' => 'shield',
            'eye' => 'medical',
            default => 'pill',
        };
    }
}
