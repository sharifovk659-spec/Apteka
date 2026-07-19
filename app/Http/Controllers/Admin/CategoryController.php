<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryDeleteService;
use App\Services\CategoryImageService;
use App\Services\CategoryTreeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryTreeService $treeService,
        private readonly CategoryImageService $imageService,
        private readonly CategoryDeleteService $deleteService,
    ) {}

    public function index(): View
    {
        $categories = $this->treeService->flattenForAdminTable();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', $this->formOptions());
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image']);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageService->store($request->file('image'));
            }

            Category::query()->create($data);
            $this->treeService->resetCache();
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['image' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Категория успешно создана.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            ...$this->formOptions($category->id),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image'], $data['remove_image']);

        try {
            if ($request->boolean('remove_image')) {
                $this->imageService->delete($category->image);
                $data['image'] = null;
            }

            if ($request->hasFile('image')) {
                $this->imageService->delete($category->image);
                $data['image'] = $this->imageService->store($request->file('image'));
            }

            $category->update($data);
            $this->treeService->resetCache();
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['image' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', 'Категория успешно обновлена.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        try {
            $this->deleteService->delete($category);
            $this->treeService->resetCache();
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Категория удалена.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);
        $this->treeService->resetCache();

        return back()->with('success', $category->is_active ? 'Категория активирована.' : 'Категория деактивирована.');
    }

    private function formOptions(?int $excludeId = null): array
    {
        return [
            'parentOptions' => $this->treeService->optionsForSelect($excludeId),
            'iconOptions' => [
                'pill' => 'Лекарства',
                'vitamin' => 'Витамины',
                'hygiene' => 'Гигиена',
                'medical' => 'Медизделия',
                'baby' => 'Детские товары',
                'cosmetic' => 'Косметика',
                'first-aid' => 'Аптечка',
                'eye' => 'Оптика',
            ],
        ];
    }
}
