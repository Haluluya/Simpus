<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This seeder is safe to run multiple times as each seeder
     * deletes existing data before inserting new records.
     *
     * Run with: php artisan db:seed
     * Or reset everything: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,      // Roles & permissions
            UserSeeder::class,             // Default users
            DoctorSeeder::class,           // Doctors
            BpjsPatientSeeder::class,      // BPJS test patients (NIK prefix 1111)
            PatientSeeder::class,          // Random patients (non-1111 NIK)
            VisitSeeder::class,            // Patient visits
            ReferralSeeder::class,         // Referrals
            QueueTicketSeeder::class,      // Queue tickets
            MedicineSeeder::class,         // Medicines
        ]);
    }
}
