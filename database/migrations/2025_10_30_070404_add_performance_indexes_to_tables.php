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
            $table->index('visit_datetime', 'idx_visit_datetime');
            $table->index('patient_id', 'idx_visits_patient');
            $table->index('coverage_type', 'idx_coverage_type');
            $table->index('status', 'idx_visits_status');
            $table->index('provider_id', 'idx_visits_provider');
            $table->index(['visit_datetime', 'coverage_type'], 'idx_datetime_coverage');
        });

        // Patients Table Indexes
        Schema::table('patients', function (Blueprint $table) {
            $table->index('name', 'idx_patients_name');
            $table->index('medical_record_number', 'idx_mr_number');
            $table->index('nik', 'idx_patients_nik');
            $table->index('bpjs_card_no', 'idx_bpjs_card');
            $table->index('created_at', 'idx_patients_created');
        });

        // Queue Tickets Table Indexes
        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->index('tanggal_antrian', 'idx_tanggal_antrian');
            $table->index('status', 'idx_queue_status');
            $table->index('patient_id', 'idx_queue_patient');
            $table->index('nomor_antrian', 'idx_nomor_antrian');
            $table->index(['tanggal_antrian', 'status'], 'idx_date_status');
        });

        // BPJS Claims Table Indexes
        Schema::table('bpjs_claims', function (Blueprint $table) {
            $table->index('patient_id', 'idx_bpjs_patient');
            $table->index('performed_at', 'idx_bpjs_performed');
            $table->index('interaction_type', 'idx_interaction_type');
            $table->index('status_code', 'idx_bpjs_status_code');
        });

        // Sync Queue Table Indexes
        Schema::table('sync_queue', function (Blueprint $table) {
            $table->index(['entity_type', 'entity_id'], 'idx_entity_type_id');
            $table->index(['target', 'status'], 'idx_target_status');
            $table->index('available_at', 'idx_available_at');
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
                $table->index('ordered_at', 'idx_lab_ordered');
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
                $table->index(['auditable_type', 'auditable_id'], 'idx_auditable');
                $table->index('user_id', 'idx_audit_user');
                $table->index('event', 'idx_audit_event');
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
            $table->dropIndex('idx_visit_datetime');
            $table->dropIndex('idx_visits_patient');
            $table->dropIndex('idx_coverage_type');
            $table->dropIndex('idx_visits_status');
            $table->dropIndex('idx_visits_provider');
            $table->dropIndex('idx_datetime_coverage');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('idx_patients_name');
            $table->dropIndex('idx_mr_number');
            $table->dropIndex('idx_patients_nik');
            $table->dropIndex('idx_bpjs_card');
            $table->dropIndex('idx_patients_created');
        });

        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tanggal_antrian');
            $table->dropIndex('idx_queue_status');
            $table->dropIndex('idx_queue_patient');
            $table->dropIndex('idx_nomor_antrian');
            $table->dropIndex('idx_date_status');
        });

        Schema::table('bpjs_claims', function (Blueprint $table) {
            $table->dropIndex('idx_bpjs_patient');
            $table->dropIndex('idx_bpjs_performed');
            $table->dropIndex('idx_interaction_type');
            $table->dropIndex('idx_bpjs_status_code');
        });

        Schema::table('sync_queue', function (Blueprint $table) {
            $table->dropIndex('idx_entity_type_id');
            $table->dropIndex('idx_target_status');
            $table->dropIndex('idx_available_at');
            $table->dropIndex('idx_last_synced');
        });

        if (Schema::hasTable('medicines')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->dropIndex('idx_medicines_name');
                $table->dropIndex('idx_stok_minimal');
                $table->dropIndex('idx_stok_tersedia');
            });
        }

        if (Schema::hasTable('lab_orders')) {
            Schema::table('lab_orders', function (Blueprint $table) {
                $table->dropIndex('idx_lab_visit');
                $table->dropIndex('idx_lab_status');
                $table->dropIndex('idx_lab_ordered');
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
                $table->dropIndex('idx_auditable');
                $table->dropIndex('idx_audit_user');
                $table->dropIndex('idx_audit_event');
                $table->dropIndex('idx_audit_created');
            });
        }
    }
};
