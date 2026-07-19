<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'slug');
        $brands = Brand::query()->pluck('id', 'slug');

        $products = [
            ['name' => 'Тестовый парацетамол Salomat 500 мг', 'slug' => 'test-paracetamol-500', 'sku' => 'SM-100001', 'category' => 'lekarstvennye-preparaty', 'brand' => 'salomat-pharma', 'price' => 12000, 'featured' => true],
            ['name' => 'Тестовый ибупрофен MedCare 200 мг', 'slug' => 'test-ibuprofen-200', 'sku' => 'SM-100002', 'category' => 'lekarstvennye-preparaty', 'brand' => 'medcare', 'price' => 18500, 'bestseller' => true],
            ['name' => 'Тестовый антигриppин Salomat', 'slug' => 'test-antigrippin', 'sku' => 'SM-100003', 'category' => 'lekarstvennye-preparaty', 'brand' => 'salomat-pharma', 'price' => 22000],
            ['name' => 'Тестовый сироп от кашля PureLab', 'slug' => 'test-cough-syrup', 'sku' => 'SM-100004', 'category' => 'lekarstvennye-preparaty', 'brand' => 'purelab', 'price' => 35000, 'daily' => true],
            ['name' => 'Тестовый спрей для горла GreenHealth', 'slug' => 'test-throat-spray', 'sku' => 'SM-100005', 'category' => 'lekarstvennye-preparaty', 'brand' => 'greenhealth', 'price' => 28000],
            ['name' => 'Тестовый витамин C VitaLine 1000 мг', 'slug' => 'test-vitamin-c-1000', 'sku' => 'SM-100006', 'category' => 'vitaminy-i-bady', 'brand' => 'vitaline', 'price' => 45000, 'featured' => true, 'daily' => true],
            ['name' => 'Тестовый витамин D3 VitaLine', 'slug' => 'test-vitamin-d3', 'sku' => 'SM-100007', 'category' => 'vitaminy-i-bady', 'brand' => 'vitaline', 'price' => 52000],
            ['name' => 'Тестовый комплекс B GreenHealth', 'slug' => 'test-vitamin-b-complex', 'sku' => 'SM-100008', 'category' => 'vitaminy-i-bady', 'brand' => 'greenhealth', 'price' => 61000, 'bestseller' => true],
            ['name' => 'Тестовый омега-3 PureLab', 'slug' => 'test-omega-3', 'sku' => 'SM-100009', 'category' => 'vitaminy-i-bady', 'brand' => 'purelab', 'price' => 78000],
            ['name' => 'Тестовый магний MedCare', 'slug' => 'test-magnesium', 'sku' => 'SM-100010', 'category' => 'vitaminy-i-bady', 'brand' => 'medcare', 'price' => 43000],
            ['name' => 'Тестовая зубная паста Salomat Fresh', 'slug' => 'test-toothpaste-fresh', 'sku' => 'SM-100011', 'category' => 'sredstva-gigieny', 'brand' => 'salomat-pharma', 'price' => 15000, 'daily' => true],
            ['name' => 'Тестовый шампунь MedCare Soft', 'slug' => 'test-shampoo-soft', 'sku' => 'SM-100012', 'category' => 'sredstva-gigieny', 'brand' => 'medcare', 'price' => 32000],
            ['name' => 'Тестовое мыло GreenHealth', 'slug' => 'test-soap-green', 'sku' => 'SM-100013', 'category' => 'sredstva-gigieny', 'brand' => 'greenhealth', 'price' => 8000],
            ['name' => 'Тестовый антибактериальный гель PureLab', 'slug' => 'test-hand-gel', 'sku' => 'SM-100014', 'category' => 'sredstva-gigieny', 'brand' => 'purelab', 'price' => 12000, 'featured' => true],
            ['name' => 'Тестовый термометр электронный Salomat', 'slug' => 'test-thermometer', 'sku' => 'SM-100015', 'category' => 'medicinskie-izdeliya', 'brand' => 'salomat-pharma', 'price' => 55000, 'bestseller' => true],
            ['name' => 'Тестовый тонометр MedCare', 'slug' => 'test-tonometer', 'sku' => 'SM-100016', 'category' => 'medicinskie-izdeliya', 'brand' => 'medcare', 'price' => 320000],
            ['name' => 'Тестовые бинты стерильные PureLab', 'slug' => 'test-sterile-bandage', 'sku' => 'SM-100017', 'category' => 'medicinskie-izdeliya', 'brand' => 'purelab', 'price' => 9000],
            ['name' => 'Тестовые перчатки медицинские GreenHealth', 'slug' => 'test-medical-gloves', 'sku' => 'SM-100018', 'category' => 'medicinskie-izdeliya', 'brand' => 'greenhealth', 'price' => 25000],
            ['name' => 'Тестовая детская присыпка Salomat Baby', 'slug' => 'test-baby-powder', 'sku' => 'SM-100019', 'category' => 'mat-i-ditya', 'brand' => 'salomat-pharma', 'price' => 18000, 'daily' => true],
            ['name' => 'Тестовые детские витамины VitaLine Kids', 'slug' => 'test-kids-vitamins', 'sku' => 'SM-100020', 'category' => 'mat-i-ditya', 'brand' => 'vitaline', 'price' => 48000, 'featured' => true],
            ['name' => 'Тестовый детский шампунь MedCare Baby', 'slug' => 'test-baby-shampoo', 'sku' => 'SM-100021', 'category' => 'mat-i-ditya', 'brand' => 'medcare', 'price' => 27000],
            ['name' => 'Тестовые капли для глаз Salomat Vision', 'slug' => 'test-eye-drops', 'sku' => 'SM-100022', 'category' => 'optika-i-uhod-za-glazami', 'brand' => 'salomat-pharma', 'price' => 34000],
            ['name' => 'Тестовый раствор для линз PureLab', 'slug' => 'test-lens-solution', 'sku' => 'SM-100023', 'category' => 'optika-i-uhod-za-glazami', 'brand' => 'purelab', 'price' => 41000],
            ['name' => 'Тестовый увлажняющий крем GreenHealth', 'slug' => 'test-moisturizer', 'sku' => 'SM-100024', 'category' => 'kosmetika-i-uhod-za-kozhey', 'brand' => 'greenhealth', 'price' => 56000, 'bestseller' => true],
            ['name' => 'Тестовый солнцезащитный крем VitaLine SPF', 'slug' => 'test-sunscreen', 'sku' => 'SM-100025', 'category' => 'kosmetika-i-uhod-za-kozhey', 'brand' => 'vitaline', 'price' => 72000],
            ['name' => 'Тестовый бальзам для губ MedCare', 'slug' => 'test-lip-balm', 'sku' => 'SM-100026', 'category' => 'kosmetika-i-uhod-za-kozhey', 'brand' => 'medcare', 'price' => 14000, 'daily' => true],
            ['name' => 'Тестовый набор аптечки Salomat Home', 'slug' => 'test-first-aid-kit', 'sku' => 'SM-100027', 'category' => 'aptechka-i-perevyazochnye', 'brand' => 'salomat-pharma', 'price' => 95000, 'featured' => true],
            ['name' => 'Тестовый пластырь MedCare 20 шт', 'slug' => 'test-plaster-pack', 'sku' => 'SM-100028', 'category' => 'aptechka-i-perevyazochnye', 'brand' => 'medcare', 'price' => 11000],
            ['name' => 'Тестовый антисептик PureLab 100 мл', 'slug' => 'test-antiseptic', 'sku' => 'SM-100029', 'category' => 'aptechka-i-perevyazochnye', 'brand' => 'purelab', 'price' => 16000, 'bestseller' => true],
            ['name' => 'Тестовый перевязочный набор GreenHealth', 'slug' => 'test-dressing-set', 'sku' => 'SM-100030', 'category' => 'aptechka-i-perevyazochnye', 'brand' => 'greenhealth', 'price' => 38000],
        ];

        $dosageForms = ['таблетки', 'капсулы', 'сироп', 'спрей', 'мазь', 'раствор', 'капли', 'порошок'];
        $dosages = ['500 мг', '200 мг', '100 мл', '50 мл', '30 г', '10 мл', '1 уп.', '250 мг'];

        foreach ($products as $index => $item) {
            $product = Product::query()->create([
                'category_id' => $categories[$item['category']],
                'brand_id' => $brands[$item['brand']],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'sku' => $item['sku'],
                'barcode' => '4600000'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                'short_description' => 'Демонстрационная карточка товара для тестирования каталога Salomat.',
                'description' => 'Тестовый товар интернет-аптеки Salomat. Не является медицинской рекомендацией и не предназначен для самолечения.',
                'composition' => 'Демонстрационный состав для тестовой среды.',
                'usage_instructions' => 'Информация носит демонстрационный характер. Перед применением проконсультируйтесь с врачом.',
                'contraindications' => 'Демонстрационное поле. Уточняйте у специалиста.',
                'manufacturer' => 'Test Manufacturer Ltd.',
                'country' => 'Узбекистан',
                'dosage_form' => $dosageForms[$index % count($dosageForms)],
                'dosage' => $dosages[$index % count($dosages)],
                'requires_prescription' => $index % 7 === 0,
                'price' => $item['price'],
                'old_price' => $index % 4 === 0 ? round($item['price'] * 1.12, 2) : null,
                'stock' => random_int(10, 150),
                'status' => 'published',
                'main_image' => 'products/'.$item['slug'].'.webp',
                'is_active' => true,
                'is_featured' => $item['featured'] ?? false,
                'is_daily_product' => $item['daily'] ?? false,
                'is_bestseller' => $item['bestseller'] ?? false,
            ]);

            $imageCount = ($index % 4) + 1;

            for ($imageIndex = 1; $imageIndex <= $imageCount; $imageIndex++) {
                $suffix = $imageIndex === 1 ? '' : '-'.$imageIndex;
                $imagePath = 'products/'.$item['slug'].$suffix.'.webp';

                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image' => $imagePath,
                    'alt_text' => $imageIndex === 1 ? $item['name'] : $item['name'].' — вид '.$imageIndex,
                    'sort_order' => $imageIndex,
                    'is_primary' => $imageIndex === 1,
                ]);
            }
        }
    }
}
