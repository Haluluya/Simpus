<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctorsByDepartment = config('doctors.by_department', []);

        foreach ($doctorsByDepartment as $department => $doctors) {
            foreach ($doctors as $doctorName) {
                // Extract specialization from name if present
                $specialization = null;
                if (preg_match('/, (Sp\.|drg)/', $doctorName, $matches)) {
                    // Extract everything after the comma
                    $parts = explode(', ', $doctorName);
                    if (count($parts) > 1) {
                        $specialization = trim($parts[1]);
                    }
                }

                Doctor::create([
                    'name' => $doctorName,
                    'department' => $department,
                    'specialization' => $specialization,
                    'is_active' => true,
                    'meta' => [
                        'seeded_from_config' => true,
                        'seeded_at' => now()->toDateTimeString(),
                    ],
                ]);
            }
        }

        $this->command->info('Seeded ' . Doctor::count() . ' doctors from config.');
    }
}
