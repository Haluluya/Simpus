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
        Schema::create('lab_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained('lab_orders')->cascadeOnDelete();
            $table->string('test_name');
            $table->string('loinc_code', 20)->nullable();
            $table->string('specimen_type')->nullable();
            $table->text('result')->nullable();
            $table->string('unit', 20)->nullable();
            $table->string('reference_range')->nullable();
            $table->enum('abnormal_flag', ['-', 'N', 'L', 'H', 'A'])->default('-');
            $table->enum('result_status', ['PRELIMINARY', 'FINAL', 'AMENDED', 'CANCELLED'])->default('PRELIMINARY');
            $table->timestamp('observed_at')->nullable();
            $table->timestamp('resulted_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('loinc_code');
            $table->index('result_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_order_items');
    }
};
