<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\AdminImageService;
use App\Services\BrandDeleteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class BrandController extends Controller
{
    public function __construct(
        private readonly AdminImageService $images,
        private readonly BrandDeleteService $deleteService,
    ) {}

    public function index(): View
    {
        $brands = Brand::query()
            ->withCount('products')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['logo']);

        try {
            if ($request->hasFile('logo')) {
                $data['logo'] = $this->images->store($request->file('logo'), 'brands');
            }

            Brand::query()->create($data);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['logo' => $e->getMessage()]);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Бренд создан.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->validated();
        unset($data['logo'], $data['remove_logo']);

        try {
            if ($request->boolean('remove_logo')) {
                $this->images->delete($brand->logo);
                $data['logo'] = null;
            }

            if ($request->hasFile('logo')) {
                $this->images->delete($brand->logo);
                $data['logo'] = $this->images->store($request->file('logo'), 'brands');
            }

            $brand->update($data);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['logo' => $e->getMessage()]);
        }

        return redirect()->route('admin.brands.edit', $brand)->with('success', 'Бренд обновлён.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        try {
            $this->deleteService->delete($brand);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.brands.index')->with('success', 'Бренд удалён.');
    }

    public function toggle(Brand $brand): RedirectResponse
    {
        $brand->update(['is_active' => ! $brand->is_active]);

        return back()->with('success', $brand->is_active ? 'Бренд активирован.' : 'Бренд деактивирован.');
    }
}
