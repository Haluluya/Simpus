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
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->enum('target', ['SATUSEHAT', 'BPJS']);
            $table->enum('status', ['PENDING', 'PROCESSING', 'SENT', 'ERROR'])->default('PENDING');
            $table->unsignedInteger('attempts')->default(0);
            $table->string('correlation_id')->nullable();
            $table->json('payload')->nullable();
            $table->text('last_error')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['target', 'status']);
            $table->index('available_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_queue');
    }
};
