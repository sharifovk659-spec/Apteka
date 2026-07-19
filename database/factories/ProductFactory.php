<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = 'Тестовый препарат '.fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 15, 450);
        $hasOldPrice = fake()->boolean(30);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'sku' => 'SB-'.fake()->unique()->numerify('######'),
            'barcode' => fake()->unique()->ean13(),
            'short_description' => 'Демонстрационная карточка товара для тестирования каталога Sabth.',
            'description' => 'Это тестовый товар интернет-аптеки Sabth. Не является медицинской рекомендацией и не предназначен для самолечения.',
            'composition' => 'Тестовый состав для демонстрации карточки товара.',
            'usage_instructions' => 'Информация носит демонстрационный характер. Перед применением проконсультируйтесь с врачом.',
            'contraindications' => 'Демонстрационное поле. Уточняйте у специалиста.',
            'manufacturer' => fake()->company(),
            'country' => fake()->randomElement(['Узбекистан', 'Россия', 'Германия', 'Индия', 'Турция']),
            'dosage_form' => fake()->randomElement(['таблетки', 'капсулы', 'сироп', 'спрей', 'мазь']),
            'dosage' => fake()->randomElement(['250 мг', '500 мг', '10 мл', '100 мл']),
            'requires_prescription' => fake()->boolean(20),
            'price' => $price,
            'old_price' => $hasOldPrice ? round($price * 1.15, 2) : null,
            'stock' => fake()->numberBetween(0, 200),
            'main_image' => 'products/demo-'.fake()->numberBetween(1, 10).'.webp',
            'is_active' => true,
            'is_featured' => fake()->boolean(25),
            'is_daily_product' => fake()->boolean(20),
            'is_bestseller' => fake()->boolean(15),
        ];
    }
}
