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
        Schema::create('bpjs_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('interaction_type');
            $table->string('request_method', 10);
            $table->string('endpoint');
            $table->string('external_reference')->nullable();
            $table->integer('status_code')->nullable();
            $table->string('status_message')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->json('headers')->nullable();
            $table->longText('raw_request')->nullable();
            $table->longText('raw_response')->nullable();
            $table->timestamp('performed_at')->nullable();
            $table->string('signature')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['interaction_type', 'endpoint']);
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpjs_claims');
    }
};
