<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->where('slug', 'admin')->first();

        User::query()->updateOrCreate(
            ['email' => 'admin@salomat.local'],
            [
                'role_id' => $adminRole?->id,
                'name' => 'Администратор',
                'phone' => '+992 44 600 00 00',
                'password' => Hash::make('Admin12345!'),
                'is_active' => true,
                'must_change_password' => true,
            ],
        );
    }
}
