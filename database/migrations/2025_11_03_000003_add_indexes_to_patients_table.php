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
        Schema::table('patients', function (Blueprint $table) {
            // Note: 'name', 'date_of_birth', 'gender' already indexed in create_patients_table migration

            // Index for NIK lookups (unique identifier)
            $table->index('nik', 'idx_patients_nik');

            // Index for BPJS card number lookups
            $table->index('bpjs_card_no', 'idx_patients_bpjs');

            // Index for medical record number lookups
            $table->index('medical_record_number', 'idx_patients_mrn');

            // Composite index for payment type filtering with name
            $table->index(['bpjs_card_no', 'name'], 'idx_patients_payment_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Only drop indexes that we added in up()
            $table->dropIndex('idx_patients_nik');
            $table->dropIndex('idx_patients_bpjs');
            $table->dropIndex('idx_patients_mrn');
            $table->dropIndex('idx_patients_payment_name');
        });
    }
};
