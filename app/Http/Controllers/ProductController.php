<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'published')
            ->with([
                'category.parent.parent',
                'brand:id,name,slug',
                'images' => fn ($query) => $query->orderBy('sort_order'),
            ])
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'old_price', 'main_image', 'manufacturer', 'brand_id', 'category_id', 'stock', 'requires_prescription'])
            ->where('is_active', true)
            ->where('status', 'published')
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->withListingRelations()
            ->limit(4)
            ->get();

        return view('product.show', compact('product', 'relatedProducts'));
    }
}
