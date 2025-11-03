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
        Schema::table('queue_tickets', function (Blueprint $table) {
            // Composite index for common queries: filter by date and department
            $table->index(['tanggal_antrian', 'department'], 'idx_queue_date_dept');

            // Index for status filtering
            $table->index('status', 'idx_queue_status');

            // Index for visit_id lookups
            $table->index('visit_id', 'idx_queue_visit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_tickets', function (Blueprint $table) {
            $table->dropIndex('idx_queue_date_dept');
            $table->dropIndex('idx_queue_status');
            $table->dropIndex('idx_queue_visit');
        });
    }
};
