<?php

namespace Database\Seeders;

use App\Enums\UserGender;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SetupSeeder::class,
        ]);

        User::factory()->create([
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'phone' => '0123456789',
            'role' => UserRole::ADMIN->value,
            'gender' => UserGender::MALE->value,
        ]);
    }
}
