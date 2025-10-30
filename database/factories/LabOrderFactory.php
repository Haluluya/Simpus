<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LabOrder>
 */
class LabOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['REQUESTED', 'IN_PROGRESS', 'COMPLETED']);
        $requestedAt = Carbon::instance(fake()->dateTimeBetween('-14 days', 'now'));
        $processedAt = fake()->boolean(60)
            ? (clone $requestedAt)->addHours(fake()->numberBetween(1, 4))
            : null;
        $completedAt = $status === 'COMPLETED'
            ? (clone ($processedAt ?? $requestedAt))->addHours(fake()->numberBetween(1, 6))
            : null;

        return [
            'visit_id' => null,
            'ordered_by' => null,
            'verified_by' => null,
            'order_number' => 'LAB'.fake()->unique()->numerify('########'),
            'status' => $status,
            'priority' => fake()->randomElement(['ROUTINE', 'STAT']),
            'requested_at' => $requestedAt,
            'processed_at' => $processedAt,
            'completed_at' => $completedAt,
            'clinical_notes' => fake()->optional()->sentence(),
            'bpjs_order_reference' => fake()->optional(0.3)->numerify('##########'),
            'fhir_service_request_id' => fake()->optional(0.3)->uuid(),
            'meta' => [
                'specimen' => fake()->randomElement(['Serum', 'Plasma', 'Whole Blood']),
            ],
        ];
    }
}
