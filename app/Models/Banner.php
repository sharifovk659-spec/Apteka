<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\MediaUrl;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'button_text',
        'button_url',
        'position',
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

    public function imageUrl(): string
    {
        return MediaUrl::fromStorage($this->image, 'images/placeholders/product.svg');
    }

    public function linkUrl(): string
    {
        if ($this->button_url) {
            return str_starts_with($this->button_url, 'http')
                ? $this->button_url
                : url($this->button_url);
        }

        return route('catalog.index');
    }
}
