<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Design;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('design_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Design::class)->constrained()->cascadeOnDelete();
            $table->integer('revision_number');
            $table->text('client_notes')->nullable();
            $table->string('file_path')->comment('File revisi dari studio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_revisions');
    }
};
