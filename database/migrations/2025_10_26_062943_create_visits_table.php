<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visit_number')->unique();
            $table->dateTime('visit_datetime');
            $table->string('clinic_name');
            $table->enum('coverage_type', ['BPJS', 'UMUM'])->default('UMUM');
            $table->string('sep_no', 40)->nullable();
            $table->string('bpjs_reference_no', 40)->nullable();
            $table->string('queue_number', 20)->nullable();
            $table->enum('status', ['SCHEDULED', 'ONGOING', 'COMPLETED', 'CANCELLED'])->default('SCHEDULED');
            $table->text('chief_complaint')->nullable();
            $table->text('triage_notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('visit_datetime');
            $table->index('clinic_name');
            $table->index('status');
            $table->index('coverage_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
