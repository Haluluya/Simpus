<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmrNote>
 */
class EmrNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $icdCodes = [
            ['code' => 'J06.9', 'description' => 'Acute upper respiratory infection, unspecified'],
            ['code' => 'I10', 'description' => 'Essential (primary) hypertension'],
            ['code' => 'E11.9', 'description' => 'Type 2 diabetes mellitus without complications'],
            ['code' => 'K29.7', 'description' => 'Gastritis, unspecified'],
        ];
        $icd = fake()->randomElement($icdCodes);

        return [
            'visit_id' => null,
            'author_id' => null,
            'subjective' => fake()->paragraph(),
            'objective' => fake()->paragraph(),
            'assessment' => fake()->sentence(),
            'plan' => fake()->sentence(),
            'icd10_code' => $icd['code'],
            'icd10_description' => $icd['description'],
            'notes' => fake()->optional()->paragraph(),
            'meta' => [
                'vital_signs' => [
                    'blood_pressure' => fake()->numberBetween(100, 140).'/'.fake()->numberBetween(70, 90),
                    'pulse' => fake()->numberBetween(60, 100),
                ],
            ],
        ];
    }
}
