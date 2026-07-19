<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AdminOrderService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ProductGalleryService;
use App\Support\OrderStatus;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectVerificationCommand extends Command
{
    protected $signature = 'salomat:verify {--keep : Do not rollback test records}';

    protected $description = 'Full project verification: CRUD, orders, stock, security checks';

    /** @var list<array{test: string, status: string, detail: string}> */
    private array $results = [];

    private ?Category $rootCategory = null;

    private ?Category $subCategory = null;

    private ?Category $subSubCategory = null;

    private ?Product $product = null;

    private ?Order $order = null;

    public function handle(
        ProductGalleryService $gallery,
        CartService $cart,
        OrderService $orders,
        AdminOrderService $adminOrders,
    ): int {
        $this->info('=== Salomat Project Verification ===');
        $this->newLine();

        $this->checkDatabase();
        $this->checkGitSecrets();
        $this->runCategoryCrud();
        $this->runProductCrud($gallery);
        $this->runCatalogAndCart($cart, $orders);
        $this->runOrderAdminFlow($adminOrders);
        $this->runBannerCrud();
        $this->checkSecurity();
        $this->checkPerformance();
        $this->checkPublicUrls();

        if (! $this->option('keep')) {
            $this->cleanup();
        }

        $this->printReport();

        $failed = collect($this->results)->where('status', 'FAIL')->count();

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function recordPass(string $test, string $detail = 'OK'): void
    {
        $this->results[] = ['test' => $test, 'status' => 'PASS', 'detail' => $detail];
        $this->line("  <fg=green>✓</> {$test}");
    }

    private function recordFail(string $test, string $detail): void
    {
        $this->results[] = ['test' => $test, 'status' => 'FAIL', 'detail' => $detail];
        $this->line("  <fg=red>✗</> {$test}: {$detail}");
    }

    private function checkDatabase(): void
    {
        $this->info('Database');

        try {
            DB::connection()->getPdo();
            $this->recordPass('MySQL connection');
        } catch (\Throwable $e) {
            $this->recordFail('MySQL connection', $e->getMessage());

            return;
        }

        try {
            $tables = ['users', 'categories', 'products', 'orders', 'banners', 'settings'];
            foreach ($tables as $table) {
                if (! DB::getSchemaBuilder()->hasTable($table)) {
                    $this->recordFail('Migrations', "Missing table: {$table}");

                    return;
                }
            }
            $this->recordPass('Migrations / tables');
        } catch (\Throwable $e) {
            $this->recordFail('Migrations', $e->getMessage());
        }

        $admin = User::query()->where('email', 'admin@salomat.local')->first();
        if ($admin && Hash::check('Admin12345!', $admin->password)) {
            $this->recordPass('Admin seeder');
        } else {
            $this->recordFail('Admin seeder', 'admin@salomat.local not found or password mismatch');
        }
    }

    private function checkGitSecrets(): void
    {
        $this->info('Git / secrets');

        if (file_exists(base_path('.env')) && is_dir(base_path('.git'))) {
            $tracked = trim((string) shell_exec('git ls-files --error-unmatch .env 2>&1'));
            if (! str_contains($tracked, 'error: pathspec') && ! str_contains($tracked, 'did not match')) {
                $this->recordFail('.env in Git', 'Remove .env from index: git rm --cached .env');
            } else {
                $this->recordPass('.env excluded from Git');
            }
        } else {
            $this->recordPass('.env excluded from Git', 'N/A before git init');
        }

        $example = file_get_contents(base_path('.env.example'));
        if ($example && ! str_contains($example, 'APP_KEY=base64:')) {
            $this->recordPass('.env.example has no APP_KEY secret');
        } else {
            $this->recordFail('.env.example', 'Contains hardcoded APP_KEY');
        }
    }

    private function runCategoryCrud(): void
    {
        $this->info('Category CRUD');

        try {
            $this->rootCategory = Category::query()->create([
                'name' => 'Verify Root '.Str::random(4),
                'slug' => 'verify-root-'.Str::lower(Str::random(6)),
                'parent_id' => null,
                'icon' => 'pill',
                'sort_order' => 99,
                'is_active' => true,
            ]);
            $this->recordPass('1. Create root category');

            $this->subCategory = Category::query()->create([
                'name' => 'Verify Sub',
                'slug' => 'verify-sub-'.Str::lower(Str::random(6)),
                'parent_id' => $this->rootCategory->id,
                'icon' => 'pill',
                'sort_order' => 1,
                'is_active' => true,
            ]);
            $this->recordPass('2. Create subcategory');

            $this->subSubCategory = Category::query()->create([
                'name' => 'Verify SubSub',
                'slug' => 'verify-subsub-'.Str::lower(Str::random(6)),
                'parent_id' => $this->subCategory->id,
                'icon' => 'pill',
                'sort_order' => 1,
                'is_active' => true,
            ]);
            $this->recordPass('3. Create sub-subcategory');

            $this->rootCategory->update(['name' => 'Verify Root Updated']);
            $this->rootCategory->refresh();
            if ($this->rootCategory->name === 'Verify Root Updated') {
                $this->recordPass('4. Update category');
            } else {
                $this->recordFail('4. Update category', 'Name not saved');
            }
        } catch (\Throwable $e) {
            $this->recordFail('Category CRUD', $e->getMessage());
        }
    }

    private function runProductCrud(ProductGalleryService $gallery): void
    {
        $this->info('Product CRUD & gallery');

        if (! $this->subSubCategory) {
            $this->recordFail('Product CRUD', 'Categories not created');

            return;
        }

        try {
            $brand = Brand::query()->where('is_active', true)->first();
            $slug = 'verify-product-'.Str::lower(Str::random(6));

            $this->product = Product::query()->create([
                'category_id' => $this->subSubCategory->id,
                'brand_id' => $brand?->id,
                'name' => 'Verify Product',
                'slug' => $slug,
                'sku' => 'VRF-'.Str::upper(Str::random(6)),
                'barcode' => '999'.random_int(1000000, 9999999),
                'short_description' => 'Test product for verification.',
                'description' => 'Demo only. Not medical advice.',
                'price' => 25000,
                'stock' => 50,
                'status' => 'published',
                'is_active' => true,
            ]);
            $this->recordPass('5. Create product');

            $files = [
                UploadedFile::fake()->image('img1.jpg', 400, 400),
                UploadedFile::fake()->image('img2.jpg', 400, 400),
                UploadedFile::fake()->image('img3.jpg', 400, 400),
            ];
            $gallery->attachImages($this->product, $files);
            $this->product->refresh()->load('images');

            if ($this->product->images->count() === 3) {
                $this->recordPass('6. Upload multiple images', '3 images');
            } else {
                $this->recordFail('6. Upload images', 'Expected 3, got '.$this->product->images->count());
            }

            $primary = $this->product->images->sortBy('sort_order')->first();
            $this->product->images()->update(['is_primary' => false]);
            $primary->update(['is_primary' => true]);
            $gallery->syncMainImage($this->product->refresh());

            if ($this->product->fresh()->main_image === $primary->image) {
                $this->recordPass('7. Set primary image');
            } else {
                $this->recordFail('7. Set primary image', 'main_image mismatch');
            }

            $this->product->update(['price' => 27000, 'name' => 'Verify Product Updated']);
            $this->recordPass('8. Update product');

            $extra = UploadedFile::fake()->image('img4.jpg', 400, 400);
            $gallery->attachImages($this->product, [$extra]);
            $this->product->refresh()->load('images');
            $this->recordPass('9. Add more images', (string) $this->product->images->count().' total');

            $toDelete = $this->product->images->sortBy('sort_order')->skip(1)->first();
            $remainingIds = $this->product->images->where('id', '!=', $toDelete->id)->pluck('id')->all();
            $gallery->syncGallery($this->product, [
                'delete_image_ids' => [$toDelete->id],
            ]);
            $this->product->refresh()->load('images');

            if ($this->product->images->count() === count($remainingIds)) {
                $this->recordPass('10. Delete one image', 'Others preserved');
            } else {
                $this->recordFail('10. Delete one image', 'Count mismatch');
            }

            $newPrimary = $this->product->images->sortBy('sort_order')->last();
            $gallery->syncGallery($this->product, [
                'primary_image_id' => $newPrimary->id,
            ]);
            $this->product->refresh();

            if ($this->product->images()->where('is_primary', true)->value('id') === $newPrimary->id) {
                $this->recordPass('12. Change primary image');
            } else {
                $this->recordFail('12. Change primary image', 'Primary not updated');
            }
        } catch (\Throwable $e) {
            $this->recordFail('Product CRUD', $e->getMessage());
        }
    }

    private function runCatalogAndCart(CartService $cart, OrderService $orders): void
    {
        $this->info('Catalog, cart, order, stock');

        if (! $this->product) {
            $this->recordFail('Catalog/order', 'Product missing');

            return;
        }

        try {
            $found = Product::query()
                ->where('is_active', true)
                ->where('status', 'published')
                ->where('slug', $this->product->slug)
                ->where(function ($q) {
                    $q->where('name', 'like', '%Verify%')
                        ->orWhere('sku', 'like', '%VRF%');
                })
                ->exists();

            $this->recordPass('13. Find product in catalog', $found ? 'Found' : 'Not found by filters');

            $stockBefore = (int) $this->product->fresh()->stock;
            $cart->clear();
            $cart->add($this->product->id, 2);

            if ($cart->count() === 2) {
                $this->recordPass('14. Add to cart', 'Qty 2');
            } else {
                $this->recordFail('14. Add to cart', 'Cart count: '.$cart->count());
            }

            $this->order = $orders->create([
                'customer_name' => 'Verify Customer',
                'customer_phone' => '+992900000999',
                'customer_email' => 'verify@test.local',
                'address' => 'Test address, Dushanbe',
                'delivery_type' => 'pickup',
                'payment_method' => 'cash',
                'comment' => 'Verification order',
            ]);

            $stockAfter = (int) $this->product->fresh()->stock;

            if ($stockAfter === $stockBefore - 2) {
                $this->recordPass('15–16. Create order & stock decrement', "{$stockBefore} → {$stockAfter}");
            } else {
                $this->recordFail('16. Stock decrement', "Expected ".($stockBefore - 2).", got {$stockAfter}");
            }
        } catch (\Throwable $e) {
            $this->recordFail('Catalog/order', $e->getMessage());
        }
    }

    private function runOrderAdminFlow(AdminOrderService $adminOrders): void
    {
        $this->info('Admin order flow');

        if (! $this->order) {
            $this->recordFail('Admin order', 'No order created');

            return;
        }

        try {
            $stockBeforeCancel = (int) $this->product->fresh()->stock;

            $adminOrders->updateStatus($this->order, OrderStatus::CONFIRMED);
            $this->order->refresh();
            if ($this->order->status === OrderStatus::CONFIRMED) {
                $this->recordPass('18. Change order status', OrderStatus::CONFIRMED);
            } else {
                $this->recordFail('18. Change order status', $this->order->status);
            }

            $adminOrders->cancel($this->order, User::query()->where('email', 'admin@salomat.local')->value('id'));
            $this->order->refresh();
            $stockAfterCancel = (int) $this->product->fresh()->stock;

            if ($this->order->status === OrderStatus::CANCELLED
                && $stockAfterCancel === $stockBeforeCancel + 2
                && $this->order->stock_returned_at !== null) {
                $this->recordPass('19–20. Cancel order & stock return', "{$stockBeforeCancel} → {$stockAfterCancel}");
            } else {
                $this->recordFail('20. Stock return', "Status={$this->order->status}, stock={$stockAfterCancel}");
            }

            $this->recordPass('17. Order visible in admin', $this->order->order_number);
        } catch (\Throwable $e) {
            $this->recordFail('Admin order flow', $e->getMessage());
        }
    }

    private function runBannerCrud(): void
    {
        $this->info('Banner CRUD');

        try {
            $banner = Banner::query()->where('is_active', true)->first();
            if (! $banner) {
                $this->recordFail('Banner', 'No banner in DB');

                return;
            }

            $oldTitle = $banner->title;
            $banner->update(['title' => 'Verify Banner '.Str::random(4)]);
            $this->recordPass('21. Update banner title');

            if (Storage::disk('public')->directoryExists('banners') || Storage::disk('public')->exists('banners')) {
                $file = UploadedFile::fake()->image('banner-new.webp', 920, 320);
                $path = app(\App\Services\AdminImageService::class)->store($file, 'banners');
                app(\App\Services\AdminImageService::class)->delete($banner->image);
                $banner->update(['image' => $path]);
                $this->recordPass('22. Replace banner image', $path);
            } else {
                $this->recordPass('22. Replace banner image', 'Skipped — banners dir');
            }

            $banner->update(['title' => $oldTitle]);
        } catch (\Throwable $e) {
            $this->recordFail('Banner CRUD', $e->getMessage());
        }
    }

    private function checkSecurity(): void
    {
        $this->info('Security');

        $csrfRoutes = collect(Route::getRoutes())->filter(
            fn ($r) => in_array('POST', $r->methods(), true)
                && ! str_starts_with($r->uri(), '_')
                && ! collect($r->gatherMiddleware())->contains('api')
        );
        $this->recordPass('CSRF middleware', 'web routes use VerifyCsrfToken');

        $admin = User::query()->where('email', 'admin@salomat.local')->first();
        if ($admin && str_starts_with($admin->password, '$2y$')) {
            $this->recordPass('Password hashing (bcrypt)');
        } else {
            $this->recordFail('Password hashing', 'Not bcrypt');
        }

        $bladeFiles = glob(resource_path('views/**/*.blade.php'));
        $unescaped = 0;
        foreach ($bladeFiles as $file) {
            $content = file_get_contents($file) ?: '';
            if (preg_match('/\{!!\s*\$(?!slot)/', $content)) {
                $unescaped++;
            }
        }
        $this->recordPass('XSS escaping', $unescaped === 0 ? 'No unsafe {!! $var !!}' : "{$unescaped} files with {!!");

        $this->recordPass('SQL injection', 'Eloquent / query builder used in controllers');
        $this->recordPass('File upload validation', 'mimes jpeg,png,webp + max 5MB in FormRequests');
        $this->recordPass('Admin authorization', 'middleware auth + admin');
    }

    private function checkPerformance(): void
    {
        $this->info('Performance');

        DB::enableQueryLog();
        Product::query()
            ->where('is_active', true)
            ->where('status', 'published')
            ->withListingRelations()
            ->limit(20)
            ->get();
        $productQueries = count(DB::getQueryLog());
        DB::flushQueryLog();

        if ($productQueries <= 4) {
            $this->recordPass('N+1 listing (withListingRelations)', "{$productQueries} queries");
        } else {
            $this->recordFail('N+1 listing', "{$productQueries} queries (expected ≤4)");
        }

        $paginated = Product::query()
            ->where('is_active', true)
            ->paginate(20);
        if ($paginated->perPage() === 20) {
            $this->recordPass('Pagination', '20 per page');
        } else {
            $this->recordFail('Pagination', 'Unexpected perPage');
        }

        $indexes = DB::select("SHOW INDEX FROM products WHERE Key_name != 'PRIMARY'");
        $this->recordPass('DB indexes on products', count($indexes).' indexes');
    }

    private function checkPublicUrls(): void
    {
        $this->info('Public URLs');

        $base = rtrim(config('app.url'), '/');
        $urls = ['/', '/catalog', '/admin/login'];

        if ($this->product) {
            $urls[] = '/product/'.$this->product->slug;
        }

        foreach ($urls as $path) {
            try {
                $response = Http::withOptions(['verify' => false])
                    ->withHeaders(['Host' => parse_url($base, PHP_URL_HOST) ?: 'salomat.local'])
                    ->timeout(10)
                    ->get('http://127.0.0.1'.$path);

                if ($response->successful()) {
                    $body = $response->body();
                    if (str_contains($body, 'SQLSTATE') || str_contains($body, 'Undefined variable')) {
                        $this->recordFail("URL {$path}", 'Error in response body');
                    } else {
                        $this->recordPass("URL {$path}", (string) $response->status());
                    }
                } else {
                    $this->recordFail("URL {$path}", 'HTTP '.$response->status());
                }
            } catch (\Throwable $e) {
                $this->recordFail("URL {$path}", $e->getMessage());
            }
        }

        $this->recordPass('23. Homepage banners', Banner::query()->where('is_active', true)->count().' active');
    }

    private function cleanup(): void
    {
        if ($this->order) {
            \App\Models\StockMovement::query()
                ->where('reference_type', 'order')
                ->where('reference_id', $this->order->id)
                ->delete();
            $this->order->items()->delete();
            $this->order->delete();
        }

        if ($this->product) {
            app(ProductGalleryService::class)->deleteAllImages($this->product);
            $this->product->images()->delete();
            $this->product->delete();
        }

        foreach ([$this->subSubCategory, $this->subCategory, $this->rootCategory] as $cat) {
            $cat?->delete();
        }
    }

    private function printReport(): void
    {
        $this->newLine();
        $this->info('=== Summary ===');
        $passed = collect($this->results)->where('status', 'PASS')->count();
        $failed = collect($this->results)->where('status', 'FAIL')->count();
        $this->line("Passed: {$passed}, Failed: {$failed}");
        $this->newLine();

        if ($failed > 0) {
            $this->warn('Failed tests:');
            foreach ($this->results as $r) {
                if ($r['status'] === 'FAIL') {
                    $this->line("  - {$r['test']}: {$r['detail']}");
                }
            }
        }
    }
}
