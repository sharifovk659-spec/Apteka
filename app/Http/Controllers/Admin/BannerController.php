<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
use App\Services\AdminImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class BannerController extends Controller
{
    public function __construct(
        private readonly AdminImageService $images,
    ) {}

    public function index(): View
    {
        $banners = Banner::query()->orderBy('position')->orderBy('sort_order')->paginate(20);

        return view('admin.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banners.create', $this->formOptions());
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image']);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = $this->images->store($request->file('image'), 'banners');
            } else {
                return back()->withInput()->withErrors(['image' => 'Загрузите изображение баннера.']);
            }

            Banner::query()->create($data);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['image' => $e->getMessage()]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Баннер создан.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', [
            'banner' => $banner,
            ...$this->formOptions(),
        ]);
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image']);

        try {
            if ($request->hasFile('image')) {
                $this->images->delete($banner->image);
                $data['image'] = $this->images->store($request->file('image'), 'banners');
            }

            $banner->update($data);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['image' => $e->getMessage()]);
        }

        return redirect()->route('admin.banners.edit', $banner)->with('success', 'Баннер обновлён.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->images->delete($banner->image);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Баннер удалён.');
    }

    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        return back()->with('success', $banner->is_active ? 'Баннер активирован.' : 'Баннер деактивирован.');
    }

    private function formOptions(): array
    {
        return [
            'positions' => [
                'home_left' => 'Главная — левый',
                'home_slider' => 'Главная — слайдер',
                'home_right' => 'Главная — правый',
                'promo' => 'Промо-блок',
            ],
        ];
    }
}
