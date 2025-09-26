<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Marketing']);
        Role::create(['name' => 'Studio']);
        Role::create(['name' => 'Keuangan']);
        Role::create(['name' => 'Gudang']);
        Role::create(['name' => 'Produksi']);
        Role::create(['name' => 'Quality Control']);
        Role::create(['name' => 'PPIC']);
        // Tambahkan peran super-admin untuk manajemen sistem
        Role::create(['name' => 'Super-Admin']);
    }
}
