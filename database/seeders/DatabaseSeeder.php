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
    // public function run(): void
    // {
    //     $this->call(RoleSeeder::class);
    //     $this->call(PermissionSeeder::class);

    //     // Opsional: Buat satu user super-admin untuk testing
    //     $user = User::factory()->create([
    //         'name' => 'Admin User',
    //         'email' => 'admin@example.com',
    //     ]);
    //     $user->assignRole('Super-Admin');

    //     // Opsional: Buat user marketing untuk testing
    //     $marketingUser = User::factory()->create([
    //         'name' => 'Marketing User',
    //         'email' => 'marketing@example.com',
    //     ]);
    //     $marketingUser->assignRole('Marketing');

    //     // Buat user studio untuk testing
    //     $studioUser = User::factory()->create([
    //         'name' => 'Studio User',
    //         'email' => 'studio@example.com',
    //     ]);
    //     $studioUser->assignRole('Studio');

    //     // Buat user gudang untuk testing
    //     $gudangUser = User::factory()->create([
    //         'name' => 'Gudang User',
    //         'email' => 'gudang@example.com',
    //     ]);
    //     $gudangUser->assignRole('Gudang');

    //     // Buat user produksi untuk testing
    //     $produksiUser = User::factory()->create([
    //         'name' => 'Produksi User',
    //         'email' => 'produksi@example.com',
    //     ]);
    //     $produksiUser->assignRole('Produksi');

    //     // Buat user keuangan untuk testing
    //     $keuanganUser = User::factory()->create([
    //         'name' => 'Keuangan User',
    //         'email' => 'keuangan@example.com',
    //     ]);
    //     $keuanganUser->assignRole('Keuangan');

    //     // Buat user QC untuk testing
    //     $qcUser = User::factory()->create([
    //         'name' => 'QC User',
    //         'email' => 'qc@example.com',
    //     ]);
    //     $qcUser->assignRole('Quality Control');

    //     // Buat user PPIC untuk testing <-- TAMBAHKAN INI
    //     $ppicUser = User::factory()->create([
    //         'name' => 'PPIC User',
    //         'email' => 'ppic@example.com',
    //     ]);
    //     $ppicUser->assignRole('PPIC');
    // }

    public function run(): void
    {
        // 1. Buat Roles dan Permissions terlebih dahulu
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // 2. Jalankan seeder untuk mengisi data dummy
        $this->call(DummyDataSeeder::class);
    }
}
