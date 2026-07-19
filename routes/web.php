<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\PasswordChangeController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/favorites', [FavoritesController::class, 'index'])->name('favorites.index');
Route::post('/favorites/{product}', [FavoritesController::class, 'toggle'])->name('favorites.toggle');
Route::delete('/favorites/{product}', [FavoritesController::class, 'remove'])->name('favorites.remove');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'store'])->name('admin.login.store');
});

Route::post('/admin/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/password', [PasswordChangeController::class, 'edit'])->name('admin.password.edit');
    Route::put('/admin/password', [PasswordChangeController::class, 'update'])->name('admin.password.update');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'password.changed'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}/toggle', [AdminProductController::class, 'toggle'])->name('products.toggle');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::patch('/categories/{category}/toggle', [AdminCategoryController::class, 'toggle'])->name('categories.toggle');

    Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [AdminBrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [AdminBrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [AdminBrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [AdminBrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [AdminBrandController::class, 'destroy'])->name('brands.destroy');
    Route::patch('/brands/{brand}/toggle', [AdminBrandController::class, 'toggle'])->name('brands.toggle');

    Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [AdminBannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [AdminBannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');
    Route::patch('/banners/{banner}/toggle', [AdminBannerController::class, 'toggle'])->name('banners.toggle');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

    Route::view('/customers', 'admin.placeholder', ['title' => 'Покупатели'])->name('customers.index');
    Route::view('/warehouse', 'admin.placeholder', ['title' => 'Склад'])->name('warehouse.index');
    Route::view('/reviews', 'admin.placeholder', ['title' => 'Отзывы'])->name('reviews.index');
    Route::view('/reports', 'admin.placeholder', ['title' => 'Отчёты'])->name('reports.index');
    Route::view('/users', 'admin.placeholder', ['title' => 'Пользователи'])->name('users.index');
});
