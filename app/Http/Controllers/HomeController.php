<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Product;
use App\Services\CategoryTreeService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly CategoryTreeService $categories,
    ) {}

    public function index(): View
    {
        $listingSelect = [
            'id', 'name', 'slug', 'price', 'old_price',
            'main_image', 'manufacturer', 'brand_id', 'category_id',
        ];

        $dailyProducts = Product::query()
            ->select($listingSelect)
            ->where('is_active', true)
            ->where('status', 'published')
            ->where('is_daily_product', true)
            ->withListingRelations()
            ->orderByDesc('is_featured')
            ->limit(6)
            ->get();

        $bestsellers = Product::query()
            ->select($listingSelect)
            ->where('is_active', true)
            ->where('status', 'published')
            ->where('is_bestseller', true)
            ->withListingRelations()
            ->orderByDesc('is_featured')
            ->limit(6)
            ->get();

        $promoProducts = Product::query()
            ->select($listingSelect)
            ->where('is_active', true)
            ->where('status', 'published')
            ->whereNotNull('old_price')
            ->whereColumn('old_price', '>', 'price')
            ->whereRaw('((old_price - price) / old_price) >= 0.05')
            ->withListingRelations()
            ->orderByDesc('is_featured')
            ->limit(4)
            ->get();

        if ($promoProducts->count() < 4) {
            $extra = Product::query()
                ->select($listingSelect)
                ->where('is_active', true)
                ->where('status', 'published')
                ->whereNotIn('id', $promoProducts->pluck('id'))
                ->withListingRelations()
                ->orderByDesc('is_featured')
                ->orderByDesc('is_bestseller')
                ->limit(4 - $promoProducts->count())
                ->get();

            $promoProducts = $promoProducts->merge($extra);
        }

        $leftBanner = $this->resolveBanner('home_left');
        $sliderBanners = $this->resolveBanners('home_slider');
        $rightBanner = $this->resolveBanner('home_right');
        $homeCategories = $this->categories->homeCategories();
        $homeSubcategories = $this->categories->homeSubcategories();

        return view('home.index', compact(
            'dailyProducts',
            'bestsellers',
            'promoProducts',
            'leftBanner',
            'sliderBanners',
            'rightBanner',
            'homeCategories',
            'homeSubcategories',
        ));
    }

    private function resolveBanner(string $position): ?Banner
    {
        return Banner::query()
            ->select(['id', 'title', 'subtitle', 'image', 'button_text', 'button_url'])
            ->where('is_active', true)
            ->where('position', $position)
            ->orderBy('sort_order')
            ->first();
    }

    private function resolveBanners(string $position)
    {
        return Banner::query()
            ->select(['id', 'title', 'subtitle', 'image', 'button_text', 'button_url'])
            ->where('is_active', true)
            ->where('position', $position)
            ->orderBy('sort_order')
            ->get();
    }
}
