<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            PatientSeeder::class,
            VisitSeeder::class,
            ReferralSeeder::class,
            QueueTicketSeeder::class,
            MedicineSeeder::class,
        ]);
    }
}
