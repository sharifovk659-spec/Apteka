<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $roots = [
            ['name' => 'Лекарственные препараты', 'slug' => 'lekarstvennye-preparaty', 'icon' => 'pill', 'sort_order' => 1],
            ['name' => 'Витамины и БАДы', 'slug' => 'vitaminy-i-bady', 'icon' => 'vitamin', 'sort_order' => 2],
            ['name' => 'Средства гигиены', 'slug' => 'sredstva-gigieny', 'icon' => 'hygiene', 'sort_order' => 3],
            ['name' => 'Медицинские изделия', 'slug' => 'medicinskie-izdeliya', 'icon' => 'medical', 'sort_order' => 4],
            ['name' => 'Мать и дребёнок', 'slug' => 'mat-i-ditya', 'icon' => 'baby', 'sort_order' => 5],
            ['name' => 'Оптика и уход за глазами', 'slug' => 'optika-i-uhod-za-glazami', 'icon' => 'eye', 'sort_order' => 6],
            ['name' => 'Косметика и уход за кожей', 'slug' => 'kosmetika-i-uhod-za-kozhey', 'icon' => 'cosmetic', 'sort_order' => 7],
            ['name' => 'Аптечка и перевязочные материалы', 'slug' => 'aptechka-i-perevyazochnye', 'icon' => 'first-aid', 'sort_order' => 8],
        ];

        $createdRoots = [];

        foreach ($roots as $root) {
            $createdRoots[$root['slug']] = Category::query()->create([
                ...$root,
                'parent_id' => null,
                'description' => 'Демонстрационная категория для тестовой среды Salomat.',
                'image' => null,
                'is_active' => true,
            ]);
        }

        $this->seedMedicineTree($createdRoots['lekarstvennye-preparaty']);
        $this->seedVitaminTree($createdRoots['vitaminy-i-bady']);
        $this->seedHygieneTree($createdRoots['sredstva-gigieny']);
        $this->seedMedicalTree($createdRoots['medicinskie-izdeliya']);
        $this->seedBabyTree($createdRoots['mat-i-ditya']);
        $this->seedOpticsTree($createdRoots['optika-i-uhod-za-glazami']);
        $this->seedCosmeticTree($createdRoots['kosmetika-i-uhod-za-kozhey']);
        $this->seedFirstAidTree($createdRoots['aptechka-i-perevyazochnye']);
    }

    private function seedMedicineTree(Category $root): void
    {
        $pain = $this->child($root, 'Обезболивающие', 'obezbolivayushchie', 'pill', 1);
        $this->child($pain, 'Для детей', 'obezbolivayushchie-dlya-detey', 'baby', 1);
        $this->child($pain, 'Для взрослых', 'obezbolivayushchie-dlya-vzroslyh', 'pill', 2);
        $cold = $this->child($root, 'Простуда и грипп', 'prostuda-i-grip', 'pill', 2);
        $this->child($cold, 'Сиропы', 'prostuda-siropy', 'pill', 1);
        $this->child($root, 'Желудок и пищеварение', 'zheludok-i-pishchevarenie', 'pill', 3);
        $this->child($root, 'Сердце и сосуды', 'serdce-i-sosudy', 'pill', 4);
    }

    private function seedVitaminTree(Category $root): void
    {
        $this->child($root, 'Витамин D', 'vitamin-d', 'vitamin', 1);
        $this->child($root, 'Витамин C', 'vitamin-c', 'vitamin', 2);
        $this->child($root, 'Минералы', 'mineraly', 'vitamin', 3);
    }

    private function seedHygieneTree(Category $root): void
    {
        $this->child($root, 'Уход за полостью рта', 'uhod-za-polostyu-rta', 'hygiene', 1);
        $this->child($root, 'Уход за волосами', 'uhod-za-volosami', 'hygiene', 2);
    }

    private function seedMedicalTree(Category $root): void
    {
        $this->child($root, 'Тонометры', 'tonometry', 'medical', 1);
        $this->child($root, 'Перевязочные материалы', 'perevyazochnye-med', 'medical', 2);
    }

    private function seedBabyTree(Category $root): void
    {
        $this->child($root, 'Питание', 'detskoe-pitanie', 'baby', 1);
        $this->child($root, 'Уход за кожей', 'uhod-za-kozhey-detyam', 'baby', 2);
    }

    private function seedOpticsTree(Category $root): void
    {
        $this->child($root, 'Капли для глаз', 'kapli-dlya-glaz', 'eye', 1);
        $this->child($root, 'Растворы для линз', 'rastvory-dlya-linz', 'eye', 2);
    }

    private function seedCosmeticTree(Category $root): void
    {
        $this->child($root, 'Увлажнение', 'uvlazhnenie', 'cosmetic', 1);
        $this->child($root, 'Защита от солнца', 'zashchita-ot-solnca', 'cosmetic', 2);
    }

    private function seedFirstAidTree(Category $root): void
    {
        $this->child($root, 'Аптечки', 'aptechki-nabory', 'first-aid', 1);
        $this->child($root, 'Антисептики', 'antiseptiki', 'first-aid', 2);
    }

    private function child(Category $parent, string $name, string $slug, string $icon, int $sortOrder): Category
    {
        return Category::query()->create([
            'parent_id' => $parent->id,
            'name' => $name,
            'slug' => $slug,
            'icon' => $icon,
            'sort_order' => $sortOrder,
            'description' => 'Тестовая подкатегория. Не является медицинской рекомендацией.',
            'is_active' => true,
        ]);
    }
}
