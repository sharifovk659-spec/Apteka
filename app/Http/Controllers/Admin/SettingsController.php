<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Services\AdminImageService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
        private readonly AdminImageService $images,
    ) {}

    public function index(): View
    {
        $values = $this->settings->all();

        return view('admin.settings.index', compact('values'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $logoPath = $this->settings->get('store.logo');

            if ($request->boolean('remove_logo')) {
                $this->images->delete($logoPath);
                $logoPath = null;
            }

            if ($request->hasFile('logo')) {
                $this->images->delete($logoPath);
                $logoPath = $this->images->store($request->file('logo'), 'settings');
            }

            $this->settings->setMany([
                'store.name' => ['value' => $data['store']['name'], 'group' => 'general'],
                'store.phone' => ['value' => $data['store']['phone'], 'group' => 'general'],
                'store.email' => ['value' => $data['store']['email'] ?? null, 'group' => 'general'],
                'store.address' => ['value' => $data['store']['address'] ?? null, 'group' => 'general'],
                'store.tagline' => ['value' => $data['store']['tagline'] ?? null, 'group' => 'general'],
                'store.logo' => ['value' => $logoPath, 'group' => 'general'],
                'delivery.default_price' => ['value' => (string) $data['delivery']['default_price'], 'group' => 'delivery'],
                'order.min_amount' => ['value' => (string) $data['order']['min_amount'], 'group' => 'order'],
                'social.telegram' => ['value' => $data['social']['telegram'] ?? null, 'group' => 'social'],
                'social.instagram' => ['value' => $data['social']['instagram'] ?? null, 'group' => 'social'],
                'social.facebook' => ['value' => $data['social']['facebook'] ?? null, 'group' => 'social'],
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['logo' => $e->getMessage()]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Настройки сохранены.');
    }
}
