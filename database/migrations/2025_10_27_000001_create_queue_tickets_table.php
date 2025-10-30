<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal_antrian');
            $table->string('nomor_antrian', 10);
            $table->enum('status', ['MENUNGGU', 'DIPANGGIL', 'SELESAI', 'BATAL'])->default('MENUNGGU');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['tanggal_antrian', 'nomor_antrian']);
            $table->index(['patient_id', 'tanggal_antrian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};
