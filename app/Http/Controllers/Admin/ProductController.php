<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductIndexRequest;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Product;
use App\Services\CategoryTreeService;
use App\Services\ProductDeleteService;
use App\Services\ProductGalleryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductGalleryService $galleryService,
        private readonly ProductDeleteService $deleteService,
        private readonly CategoryTreeService $categoryTreeService,
    ) {}

    public function index(ProductIndexRequest $request): View
    {
        $filters = $request->filters();

        $productsQuery = Product::query()
            ->select([
                'id', 'name', 'slug', 'sku', 'price', 'stock',
                'main_image', 'is_active', 'category_id', 'brand_id',
            ])
            ->with([
                'category:id,name',
                'brand:id,name',
            ]);

        $this->applyFilters($productsQuery, $filters);

        $products = $productsQuery
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $totalProducts = Product::query()->count();

        $categories = collect($this->categoryTreeService->optionsForSelect(activeOnly: true));

        $brands = Brand::query()
            ->select(['id', 'name'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.index', compact(
            'products',
            'filters',
            'totalProducts',
            'categories',
            'brands',
        ));
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formOptions());
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'images']);

        return view('admin.products.show', compact('product'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['gallery_images']);

        try {
            $product = Product::query()->create($data);

            $files = array_filter($request->file('gallery_images', []) ?? []);

            if ($files !== []) {
                $this->galleryService->attachImages($product, array_values($files));
            }
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['gallery_images' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Товар успешно создан.');
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('admin.products.edit', [
            'product' => $product,
            ...$this->formOptions(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        unset($data['gallery_images'], $data['delete_image_ids'], $data['primary_image_id'], $data['image_order']);

        try {
            $product->update($data);

            $this->galleryService->syncGallery($product, [
                'new_images' => $request->file('gallery_images', []),
                'delete_image_ids' => $request->input('delete_image_ids', []),
                'primary_image_id' => $request->input('primary_image_id'),
                'image_order' => $request->input('image_order', []),
            ]);
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['gallery_images' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Товар успешно обновлён.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $this->deleteService->delete($product);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Не удалось удалить товар. Попробуйте снова.');
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Товар удалён.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', $product->is_active ? 'Товар активирован.' : 'Товар деактивирован.');
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if ($filters['search']) {
            $search = $filters['search'];
            $query->where(function (Builder $searchQuery) use ($search) {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        if ($filters['brand_id']) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        }

        if ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        if ($filters['in_stock'] === 'yes') {
            $query->where('stock', '>', 0);
        }

        if ($filters['in_stock'] === 'no') {
            $query->where('stock', '<=', 0);
        }
    }

    private function formOptions(): array
    {
        return [
            'categories' => collect($this->categoryTreeService->optionsForSelect(activeOnly: true)),
            'brands' => Brand::query()
                ->select(['id', 'name'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ];
    }
}
