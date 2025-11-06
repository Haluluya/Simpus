<?php

namespace Database\Seeders;

use App\Models\Referral;
use Illuminate\Database\Seeder;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all records to avoid duplicates
        Referral::query()->delete();

        Referral::factory()
            ->count(25)
            ->create();
    }
}
