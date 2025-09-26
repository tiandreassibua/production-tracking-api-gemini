<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class); // <-- TAMBAHKAN INI

        // Opsional: Buat satu user super-admin untuk testing
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $user->assignRole('Super-Admin');

        // Opsional: Buat user marketing untuk testing
        $marketingUser = User::factory()->create([
            'name' => 'Marketing User',
            'email' => 'marketing@example.com',
        ]);
        $marketingUser->assignRole('Marketing');
    }
}
