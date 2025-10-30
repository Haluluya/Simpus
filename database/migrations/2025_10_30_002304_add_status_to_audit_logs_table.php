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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('status')->default('success')->after('performed_at');
            $table->text('error_message')->nullable()->after('status');
            $table->json('old_values')->nullable()->after('error_message');
            $table->json('new_values')->nullable()->after('old_values');
            $table->text('description')->nullable()->after('new_values');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['status', 'error_message', 'old_values', 'new_values', 'description']);
        });
    }
};
