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
        // Visits Table Indexes
        Schema::table('visits', function (Blueprint $table) {
            // Note: visit_datetime, status, coverage_type already indexed in create migration
            // Only add new indexes that don't exist yet
            $table->index('patient_id', 'idx_visits_patient');
            $table->index('provider_id', 'idx_visits_provider');
            $table->index(['visit_datetime', 'coverage_type'], 'idx_datetime_coverage');
        });

        // Patients Table Indexes
        // Note: Indexes for patients are handled by 2025_11_03_000003_add_indexes_to_patients_table.php
        // Skipping to avoid duplicate index errors

        // Queue Tickets Table Indexes
        // Note: Indexes for queue_tickets are handled by 2025_11_03_000002_add_indexes_to_queue_tickets_table.php
        // Skipping to avoid duplicate index errors

        // BPJS Claims Table Indexes
        Schema::table('bpjs_claims', function (Blueprint $table) {
            // Note: [interaction_type, endpoint] and performed_at already indexed in create migration
            // Only add new indexes
            $table->index('patient_id', 'idx_bpjs_patient');
            $table->index('status_code', 'idx_bpjs_status_code');
        });

        // Sync Queue Table Indexes
        Schema::table('sync_queue', function (Blueprint $table) {
            // Note: [entity_type, entity_id], [target, status], and available_at already indexed in create migration
            // Only add new indexes
            $table->index('last_synced_at', 'idx_last_synced');
        });

        // Medicines Table Indexes
        if (Schema::hasTable('medicines')) {
            Schema::table('medicines', function (Blueprint $table) {
                // Column name is 'nama' not 'name'
                $table->index('nama', 'idx_medicines_nama');
                $table->index('stok_minimal', 'idx_stok_minimal');
                // Column name is 'stok' not 'stok_tersedia'
                $table->index('stok', 'idx_stok');
            });
        }

        // Lab Orders Table Indexes
        if (Schema::hasTable('lab_orders')) {
            Schema::table('lab_orders', function (Blueprint $table) {
                $table->index('visit_id', 'idx_lab_visit');
                $table->index('status', 'idx_lab_status');
                $table->index('requested_at', 'idx_lab_requested');
            });
        }

        // Prescriptions Table Indexes
        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->index('visit_id', 'idx_prescription_visit');
                $table->index('status', 'idx_prescription_status');
                $table->index('created_at', 'idx_prescription_created');
            });
        }

        // Audit Logs Table Indexes
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                // Note: entity_type + entity_id index already exists from create migration
                // $table->index(['entity_type', 'entity_id'], 'idx_entity');
                $table->index('user_id', 'idx_audit_user');
                $table->index('action', 'idx_audit_action');
                $table->index('created_at', 'idx_audit_created');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Only drop indexes that we added in up()
            $table->dropIndex('idx_visits_patient');
            $table->dropIndex('idx_visits_provider');
            $table->dropIndex('idx_datetime_coverage');
        });

        // Patients indexes are handled by dedicated migration
        // No need to drop here

        // Queue Tickets indexes are handled by dedicated migration
        // No need to drop here

        Schema::table('bpjs_claims', function (Blueprint $table) {
            // Only drop indexes that we added in up()
            $table->dropIndex('idx_bpjs_patient');
            $table->dropIndex('idx_bpjs_status_code');
        });

        Schema::table('sync_queue', function (Blueprint $table) {
            // Only drop indexes that we added in up()
            $table->dropIndex('idx_last_synced');
        });

        if (Schema::hasTable('medicines')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->dropIndex('idx_medicines_nama');
                $table->dropIndex('idx_stok_minimal');
                $table->dropIndex('idx_stok');
            });
        }

        if (Schema::hasTable('lab_orders')) {
            Schema::table('lab_orders', function (Blueprint $table) {
                $table->dropIndex('idx_lab_visit');
                $table->dropIndex('idx_lab_status');
                $table->dropIndex('idx_lab_requested');
            });
        }

        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->dropIndex('idx_prescription_visit');
                $table->dropIndex('idx_prescription_status');
                $table->dropIndex('idx_prescription_created');
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                // $table->dropIndex('idx_entity');
                $table->dropIndex('idx_audit_user');
                $table->dropIndex('idx_audit_action');
                $table->dropIndex('idx_audit_created');
            });
        }
    }
};
