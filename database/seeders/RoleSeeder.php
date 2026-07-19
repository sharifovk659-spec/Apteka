<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Администратор', 'slug' => 'admin'],
            ['name' => 'Менеджер', 'slug' => 'manager'],
            ['name' => 'Оператор', 'slug' => 'operator'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
