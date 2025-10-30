<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $coverageType = fake()->randomElement(['BPJS', 'UMUM']);
        $status = fake()->randomElement(['SCHEDULED', 'ONGOING', 'COMPLETED']);
        $visitDate = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'patient_id' => null,
            'provider_id' => null,
            'visit_number' => 'VIS'.fake()->unique()->numerify('########'),
            'visit_datetime' => $visitDate,
            'clinic_name' => fake()->randomElement(['Poli Umum', 'Poli Gigi', 'Poli Anak', 'UGD']),
            'coverage_type' => $coverageType,
            'sep_no' => $coverageType === 'BPJS' ? 'SEP'.fake()->unique()->numerify('##########') : null,
            'bpjs_reference_no' => $coverageType === 'BPJS' ? 'REF'.fake()->unique()->numerify('##########') : null,
            'queue_number' => fake()->bothify('A##'),
            'status' => $status,
            'chief_complaint' => fake()->sentence(),
            'triage_notes' => fake()->optional()->sentence(),
            'meta' => [
                'blood_pressure' => fake()->numberBetween(100, 140).'/'.fake()->numberBetween(70, 90),
                'temperature' => fake()->randomFloat(1, 36, 39),
            ],
        ];
    }
}
