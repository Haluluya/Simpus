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
        Schema::table('lab_order_results', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('nilai_rujukan');
            $table->foreignId('petugas_lab_id')->nullable()->after('catatan')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_order_results', function (Blueprint $table) {
            $table->dropForeign(['petugas_lab_id']);
            $table->dropColumn(['catatan', 'petugas_lab_id']);
        });
    }
};
