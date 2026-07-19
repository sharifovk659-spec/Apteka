<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use App\Services\CategoryTreeService;
use App\Services\FavoritesService;
use App\Services\SettingService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(FavoritesService::class);
        $this->app->singleton(CategoryTreeService::class);
        $this->app->singleton(SettingService::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer(['partials.public.footer', 'partials.public.header'], function ($view) {
            static $headerCategories = null;

            if ($headerCategories === null) {
                $headerCategories = app(CategoryTreeService::class)->rootsWithChildren();
            }

            $view->with('headerCategories', $headerCategories);
            $view->with('footerCategories', $headerCategories->take(6));
        });

        View::composer('partials.public.header', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
            $view->with('favoritesCount', app(FavoritesService::class)->count());
        });

        View::composer(['components.product-card', 'product.show'], function ($view) {
            static $favoriteProductIds = null;

            if ($favoriteProductIds === null) {
                $favoriteProductIds = app(FavoritesService::class)->ids();
            }

            $view->with('favoriteProductIds', $favoriteProductIds);
        });

        View::composer('*', function ($view) {
            $settings = app(SettingService::class);

            $view->with('storeName', $settings->storeName());
            $view->with('storeTagline', $settings->get('store.tagline', config('store.tagline')));
            $view->with('storeEmail', $settings->get('store.email', config('store.email')));
            $view->with('storePhone', $settings->get('store.phone', config('store.phone')));
        });
    }
}
