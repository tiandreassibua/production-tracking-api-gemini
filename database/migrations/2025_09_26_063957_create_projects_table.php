<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignIdFor(Client::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'marketing_id')->comment('User dari dep. Marketing')->constrained('users');
            $table->enum('status', [
                'negotiation', // Penawaran & persetujuan desain
                'pending', // Menunggu bahan dari gudang
                'in_progress', // Produksi berjalan
                'quality_check', // Pengecekan oleh QC
                'delivery', // Proses pengiriman oleh PPIC
                'completed', // Selesai & serah terima
                'cancelled' // Dibatalkan
            ])->default('negotiation');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
