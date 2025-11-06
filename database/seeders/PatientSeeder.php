<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete random patients (keep BPJS seeded patients with NIK prefix 1111)
        Patient::where('nik', 'not like', '1111%')->delete();

        $userIds = User::pluck('id')->all();

        Patient::factory()
            ->count(50)
            ->create()
            ->each(function (Patient $patient) use ($userIds) {
                $patient->updateQuietly([
                    'created_by' => $userIds ? Arr::random($userIds) : null,
                    'updated_by' => $userIds ? Arr::random($userIds) : null,
                ]);
            });
    }
}
