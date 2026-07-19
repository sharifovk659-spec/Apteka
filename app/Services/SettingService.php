<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private const CACHE_KEY = 'salomat.settings.all';

    public function all(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::query()->pluck('value', 'key');
        });
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->all()->get($key, $default);
    }

    public function set(string $key, ?string $value, ?string $group = null): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group],
        );

        $this->flush();
    }

    public function setMany(array $settings): void
    {
        foreach ($settings as $key => $data) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'] ?? null,
                    'group' => $data['group'] ?? null,
                ],
            );
        }

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function deliveryPrice(): float
    {
        return (float) ($this->get('delivery.default_price', config('store.delivery_price', 15000)) ?? 0);
    }

    public function minOrderAmount(): float
    {
        return (float) ($this->get('order.min_amount', '0') ?? 0);
    }

    public function storeName(): string
    {
        return $this->get('store.name', config('store.name', 'Salomat')) ?? 'Salomat';
    }
}
