<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store.name', 'value' => 'Salomat', 'group' => 'general'],
            ['key' => 'store.tagline', 'value' => 'Интернет-аптека', 'group' => 'general'],
            ['key' => 'store.email', 'value' => 'info@salomat.tj', 'group' => 'general'],
            ['key' => 'store.phone', 'value' => '+992 44 600 00 00', 'group' => 'general'],
            ['key' => 'store.address', 'value' => 'г. Душанбе, пр. Рудаки 95', 'group' => 'general'],
            ['key' => 'store.logo', 'value' => null, 'group' => 'general'],
            ['key' => 'delivery.default_price', 'value' => '15000', 'group' => 'delivery'],
            ['key' => 'order.min_amount', 'value' => '0', 'group' => 'order'],
            ['key' => 'social.telegram', 'value' => 'https://t.me/salomat', 'group' => 'social'],
            ['key' => 'social.instagram', 'value' => 'https://instagram.com/salomat', 'group' => 'social'],
            ['key' => 'social.facebook', 'value' => null, 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']],
            );
        }
    }
}
