<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Salomat Pharma', 'slug' => 'salomat-pharma'],
            ['name' => 'VitaLine', 'slug' => 'vitaline'],
            ['name' => 'MedCare', 'slug' => 'medcare'],
            ['name' => 'GreenHealth', 'slug' => 'greenhealth'],
            ['name' => 'PureLab', 'slug' => 'purelab'],
        ];

        foreach ($brands as $brand) {
            Brand::query()->create([
                ...$brand,
                'logo' => null,
                'is_active' => true,
            ]);
        }
    }
}
