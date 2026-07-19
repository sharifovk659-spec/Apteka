<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Salomat — забота о вашем здоровье',
                'subtitle' => 'Демонстрационный баннер. Не является медицинской рекомендацией.',
                'image' => 'banners/home-left.webp',
                'button_text' => 'Перейти в каталог',
                'button_url' => '/catalog',
                'position' => 'home_left',
                'sort_order' => 1,
            ],
            [
                'title' => 'Витамины и поддержка иммунитета',
                'subtitle' => 'Тестовая подборка для разработки интерфейса',
                'image' => 'banners/home-slider-1.webp',
                'button_text' => 'Смотреть витамины',
                'button_url' => '/catalog?category=vitaminy-i-bady',
                'position' => 'home_slider',
                'sort_order' => 1,
            ],
            [
                'title' => 'Товары для всей семьи',
                'subtitle' => 'Демонстрационные предложения без медицинских рекомендаций',
                'image' => 'banners/home-right.webp',
                'button_text' => 'Открыть каталог',
                'button_url' => '/catalog',
                'position' => 'home_right',
                'sort_order' => 1,
            ],
            [
                'title' => 'Акции недели',
                'subtitle' => 'Тестовый промо-баннер каталога',
                'image' => 'banners/catalog-top.webp',
                'button_text' => 'Все акции',
                'button_url' => '/catalog?discount=1',
                'position' => 'promo',
                'sort_order' => 1,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::query()->create([
                ...$banner,
                'is_active' => true,
            ]);
        }
    }
}
