<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LabOrderItem>
 */
class LabOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tests = [
            ['name' => 'Hemoglobin', 'loinc' => '718-7', 'unit' => 'g/dL', 'range' => '13.0-17.0', 'specimen' => 'Whole Blood'],
            ['name' => 'Leukosit', 'loinc' => '6690-2', 'unit' => 'x10^3/uL', 'range' => '4.0-11.0', 'specimen' => 'Whole Blood'],
            ['name' => 'Gula Darah Puasa', 'loinc' => '1558-6', 'unit' => 'mg/dL', 'range' => '70-100', 'specimen' => 'Serum'],
            ['name' => 'Kolesterol Total', 'loinc' => '2093-3', 'unit' => 'mg/dL', 'range' => '< 200', 'specimen' => 'Serum'],
        ];
        $test = fake()->randomElement($tests);
        $status = fake()->randomElement(['PRELIMINARY', 'FINAL']);

        return [
            'lab_order_id' => null,
            'test_name' => $test['name'],
            'loinc_code' => $test['loinc'],
            'specimen_type' => $test['specimen'],
            'result' => fake()->randomFloat(1, 3, 18),
            'unit' => $test['unit'],
            'reference_range' => $test['range'],
            'abnormal_flag' => fake()->randomElement(['-', 'N', 'L', 'H']),
            'result_status' => $status,
            'observed_at' => Carbon::instance(fake()->dateTimeBetween('-7 days', 'now')),
            'resulted_at' => Carbon::instance(fake()->dateTimeBetween('-3 days', 'now')),
            'meta' => [
                'method' => fake()->randomElement(['Spectrophotometry', 'Immunoassay', 'Microscopy']),
            ],
        ];
    }
}
