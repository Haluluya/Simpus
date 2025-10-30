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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->enum('gender', ['male', 'female'])->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('license_number')->nullable()->after('date_of_birth');
            $table->string('professional_identifier')->nullable()->after('license_number');
            $table->string('department')->nullable()->after('professional_identifier');
            $table->string('designation')->nullable()->after('department');
            $table->timestamp('last_login_at')->nullable()->after('designation');
            $table->json('profile_meta')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'gender',
                'date_of_birth',
                'license_number',
                'professional_identifier',
                'department',
                'designation',
                'last_login_at',
                'profile_meta',
            ]);
        });
    }
};
