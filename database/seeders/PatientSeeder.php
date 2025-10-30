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
