<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- PERMISSIONS UNTUK PROJECT ---
        Permission::create(['name' => 'create project']);
        Permission::create(['name' => 'view project']);
        Permission::create(['name' => 'edit project']);
        Permission::create(['name' => 'delete project']);
        Permission::create(['name' => 'update project status']);
        Permission::create(['name' => 'update project progress']);

        // --- PERMISSIONS UNTUK DESAIN & REVISI ---
        Permission::create(['name' => 'upload design']);
        Permission::create(['name' => 'approve design']);
        Permission::create(['name' => 'manage revision']);
        Permission::create(['name' => 'view assigned designs']);
        Permission::create(['name' => 'upload final design']);
        Permission::create(['name' => 'request revision']);

        // --- PERMISSIONS UNTUK KEUANGAN ---
        Permission::create(['name' => 'create invoice']);
        Permission::create(['name' => 'verify payment']);

        // --- PERMISSIONS UNTUK PENGIRIMAN ---
        Permission::create(['name' => 'manage delivery']);

        // --- PERMISSIONS LAINNYA ---
        Permission::create(['name' => 'confirm materials']);
        Permission::create(['name' => 'upload payment proof']);

        // --- AMBIL ROLES YANG SUDAH ADA ---
        $marketingRole = Role::findByName('Marketing');
        $studioRole = Role::findByName('Studio');
        $produksiRole = Role::findByName('Produksi');
        $keuanganRole = Role::findByName('Keuangan');
        $qcRole = Role::findByName('Quality Control');
        $ppicRole = Role::findByName('PPIC');
        $gudangRole = Role::findByName('Gudang');

        // --- BERIKAN PERMISSIONS KE ROLES ---
        $marketingRole->givePermissionTo([
            'create project',
            'view project',
            'edit project',
            'approve design', // Marketing bisa approve atas nama client
            'delete project',
            'request revision',
            'upload payment proof'
        ]);

        $studioRole->givePermissionTo([
            'view project',
            // 'upload design',
            'manage revision',
            'view assigned designs',
            'upload final design',
        ]);

        $produksiRole->givePermissionTo([
            'view project',
            'update project progress',
        ]);

        $keuanganRole->givePermissionTo([
            'view project',
            'create invoice',
            'verify payment',
            'upload payment proof',
        ]);

        $qcRole->givePermissionTo([
            'view project',
            'update project status', // Misal: mengubah status ke 'quality_check'
        ]);

        $ppicRole->givePermissionTo([
            'view project',
            'manage delivery',
        ]);

        $gudangRole->givePermissionTo([
            'view project',
            'update project status', // Misal: mengubah status dari 'pending' ke 'in_progress'
            'confirm materials',
        ]);
    }
}
