<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'subtitle' => fake()->optional()->sentence(6),
            'image' => 'banners/demo-'.fake()->numberBetween(1, 4).'.webp',
            'button_text' => fake()->optional()->words(2, true),
            'button_url' => fake()->optional()->url(),
            'position' => fake()->randomElement(['home_main', 'home_secondary', 'catalog_top']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
