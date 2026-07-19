<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('salomat:audit', function () {
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
    $productId = DB::table('products')->where('is_active', true)->where('stock', '>', 0)->value('id');

    $pages = [
        'Главная' => fn () => Request::create('/', 'GET'),
        'Каталог' => fn () => Request::create('/catalog', 'GET'),
        'Корзина (пустая)' => fn () => Request::create('/cart', 'GET'),
        'Оформление (пустая)' => fn () => Request::create('/checkout', 'GET'),
        'Admin dashboard' => fn () => Request::create('/admin', 'GET'),
        'Admin products' => fn () => Request::create('/admin/products', 'GET'),
    ];

    if ($productId) {
        $pages['Корзина (1 товар)'] = function () use ($productId) {
            $request = Request::create('/cart', 'GET');
            $request->setLaravelSession(app('session.store'));
            $request->session()->put('cart', [(int) $productId => 1]);

            return $request;
        };

        $pages['Оформление (1 товар)'] = function () use ($productId) {
            $request = Request::create('/checkout', 'GET');
            $request->setLaravelSession(app('session.store'));
            $request->session()->put('cart', [(int) $productId => 1]);

            return $request;
        };
    }

    $queryCounts = [];

    foreach ($pages as $label => $factory) {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $request = $factory();
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        $queryCounts[$label] = count(DB::getQueryLog());
        DB::disableQueryLog();
    }

    $this->info('SQL-запросы по страницам:');
    foreach ($queryCounts as $label => $count) {
        $this->line(sprintf('  %-24s %d', $label.':', $count));
    }

    $assetDirs = [
        base_path('resources/css'),
        base_path('resources/js'),
        base_path('public/build'),
    ];

    $files = collect($assetDirs)
        ->flatMap(fn (string $dir) => File::exists($dir) ? File::allFiles($dir) : [])
        ->map(fn ($file) => [
            'path' => str_replace(base_path(DIRECTORY_SEPARATOR), '', $file->getPathname()),
            'size' => $file->getSize(),
        ])
        ->sortByDesc('size')
        ->take(10)
        ->values();

    $this->newLine();
    $this->info('Самые тяжёлые файлы (топ-10):');
    foreach ($files as $file) {
        $this->line(sprintf('  %6.1f KB  %s', $file['size'] / 1024, $file['path']));
    }

    return 0;
})->purpose('Audit Salomat page query counts and asset sizes');
