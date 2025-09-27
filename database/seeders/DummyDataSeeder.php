<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Delivery;
use App\Models\Design;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // === 1. BUAT USERS UNTUK SETIAP ROLE ===
        $users = [
            'Super-Admin' => User::factory()->create(['name' => 'Admin User', 'email' => 'admin@example.com']),
            'Marketing' => User::factory()->create(['name' => 'Marketing User', 'email' => 'marketing@example.com']),
            'Studio' => User::factory()->create(['name' => 'Studio User', 'email' => 'studio@example.com']),
            'Keuangan' => User::factory()->create(['name' => 'Keuangan User', 'email' => 'keuangan@example.com']),
            'Gudang' => User::factory()->create(['name' => 'Gudang User', 'email' => 'gudang@example.com']),
            'Produksi' => User::factory()->create(['name' => 'Produksi User', 'email' => 'produksi@example.com']),
            'Quality Control' => User::factory()->create(['name' => 'QC User', 'email' => 'qc@example.com']),
            'PPIC' => User::factory()->create(['name' => 'PPIC User', 'email' => 'ppic@example.com']),
        ];

        foreach ($users as $role => $user) {
            $user->assignRole($role);
        }

        // === 2. BUAT CLIENTS ===
        $clients = Client::factory()->count(5)->create();

        // === 3. BUAT PROJECTS DENGAN BERBAGAI STATUS ===

        // --- Proyek 1: Baru Mulai (Tahap Negosiasi Desain) ---
        $project1 = Project::factory()->create([
            'client_id' => $clients->random()->id,
            'marketing_id' => $users['Marketing']->id,
            'status' => 'negotiation',
        ]);
        ProjectItem::factory()->count(2)->create(['project_id' => $project1->id]);

        // --- Proyek 2: Desain Disetujui (Menunggu Cek Gudang) ---
        $project2 = Project::factory()->create([
            'client_id' => $clients->random()->id,
            'marketing_id' => $users['Marketing']->id,
            'status' => 'pending',
            'progress' => 5,
        ]);
        ProjectItem::factory()->count(3)->create(['project_id' => $project2->id]);
        Design::create([
            'project_id' => $project2->id,
            'studio_id' => $users['Studio']->id,
            'status' => 'approved',
            'initial_file_path' => 'https://example.com/file/initial2.zip',
            'final_file_path' => 'https://example.com/file/final2.zip',
            'approved_at' => now(),
        ]);

        // --- Proyek 3: Sedang Dikerjakan (In Progress) ---
        $project3 = Project::factory()->create([
            'client_id' => $clients->random()->id,
            'marketing_id' => $users['Marketing']->id,
            'status' => 'in_progress',
            'progress' => 45,
        ]);
        ProjectItem::factory()->count(4)->create(['project_id' => $project3->id, 'progress' => 45, 'status' => 'Assembling']);
        Design::create([
            'project_id' => $project3->id,
            'studio_id' => $users['Studio']->id,
            'status' => 'approved',
            'initial_file_path' => 'https://example.com/file/initial3.zip',
            'final_file_path' => 'https://example.com/file/final3.zip',
            'approved_at' => now()->subDays(5),
        ]);
        Invoice::create([
            'project_id' => $project3->id,
            'finance_id' => $users['Keuangan']->id,
            'invoice_number' => 'INV-DUMMY-001',
            'description' => 'Pembayaran Termin 1 (30%)',
            'amount' => 30000000,
            'status' => 'paid',
            'paid_at' => now()->subDays(3),
            'due_date' => now()->subDays(3)
        ]);

        // --- Proyek 4: Siap Kirim (Tahap Delivery) ---
        $project4 = Project::factory()->create([
            'client_id' => $clients->random()->id,
            'marketing_id' => $users['Marketing']->id,
            'status' => 'delivery',
            'progress' => 100,
        ]);
        ProjectItem::factory()->count(2)->create(['project_id' => $project4->id, 'progress' => 100, 'status' => 'Packed']);
        Design::create([ // DIPERBAIKI
            'project_id' => $project4->id,
            'studio_id' => $users['Studio']->id,
            'status' => 'approved',
            'initial_file_path' => 'https://example.com/file/initial4.zip',
            'final_file_path' => 'https://example.com/file/final4.zip',
        ]);
        Invoice::create(['project_id' => $project4->id, 'finance_id' => $users['Keuangan']->id, 'status' => 'paid', 'invoice_number' => 'INV-DUMMY-002', 'description' => 'DP 30%', 'amount' => 25000000, 'due_date' => now()]);
        Invoice::create(['project_id' => $project4->id, 'finance_id' => $users['Keuangan']->id, 'status' => 'paid', 'invoice_number' => 'INV-DUMMY-003', 'description' => 'Termin 2 40%', 'amount' => 35000000, 'due_date' => now()]);
        Delivery::create([
            'project_id' => $project4->id,
            'ppic_id' => $users['PPIC']->id,
            'status' => 'on_the_way',
            'shipping_address' => 'Alamat Pengiriman Proyek 4'
        ]);

        // --- Proyek 5: Selesai (Completed) ---
        $project5 = Project::factory()->create([
            'client_id' => $clients->random()->id,
            'marketing_id' => $users['Marketing']->id,
            'status' => 'completed',
            'progress' => 100,
        ]);
        ProjectItem::factory()->count(3)->create(['project_id' => $project5->id, 'progress' => 100, 'status' => 'Installed']);
        Design::create([ // DIPERBAIKI
            'project_id' => $project5->id,
            'studio_id' => $users['Studio']->id,
            'status' => 'approved',
            'initial_file_path' => 'https://example.com/file/initial5.zip',
            'final_file_path' => 'https://example.com/file/final5.zip',
        ]);
        Invoice::create(['project_id' => $project5->id, 'finance_id' => $users['Keuangan']->id, 'status' => 'paid', 'invoice_number' => 'INV-DUMMY-004', 'description' => 'DP 30%', 'amount' => 40000000, 'due_date' => now()]);
        Invoice::create(['project_id' => $project5->id, 'finance_id' => $users['Keuangan']->id, 'status' => 'paid', 'invoice_number' => 'INV-DUMMY-005', 'description' => 'Termin 2 40%', 'amount' => 50000000, 'due_date' => now()]);
        Invoice::create(['project_id' => $project5->id, 'finance_id' => $users['Keuangan']->id, 'status' => 'paid', 'invoice_number' => 'INV-DUMMY-006', 'description' => 'Pelunasan 30%', 'amount' => 40000000, 'due_date' => now()]);
        Delivery::create([
            'project_id' => $project5->id,
            'ppic_id' => $users['PPIC']->id,
            'status' => 'handover_completed',
            'handover_document_path' => 'https://example.com/file/handover.pdf',
            'delivered_at' => now(),
            'shipping_address' => 'Alamat Pengiriman Proyek 5'
        ]);
    }
}
