<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Referral;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Referral>
 */
class ReferralFactory extends Factory
{
    protected $model = Referral::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $patient = Patient::inRandomOrder()->first() ?? Patient::factory()->create();
        $visit = Visit::where('patient_id', $patient->id)->inRandomOrder()->first();
        $creator = User::inRandomOrder()->first();

        $statuses = Referral::statuses();
        $status = fake()->randomElement($statuses);
        $scheduledAt = fake()->dateTimeBetween('-3 days', '+3 days');

        return [
            'patient_id' => $patient->id,
            'visit_id' => $visit?->id,
            'created_by' => $creator?->id,
            'referral_number' => 'REF'.Str::upper(Str::random(8)),
            'referred_to' => fake()->randomElement(['RSUD Kota', 'RSUP Nasional', 'Klinik Spesialis Jantung']),
            'referred_department' => fake()->randomElement(['Spesialis Jantung', 'Bedah Umum', 'Ginekologi', null]),
            'contact_person' => fake()->name(),
            'contact_phone' => '08'.fake()->numerify('##########'),
            'status' => $status,
            'scheduled_at' => $scheduledAt,
            'sent_at' => $status !== Referral::STATUS_PENDING ? fake()->dateTimeBetween('-2 days', 'now') : null,
            'responded_at' => $status === Referral::STATUS_COMPLETED ? fake()->dateTimeBetween('-1 days', 'now') : null,
            'reason' => fake()->sentence(12),
            'notes' => fake()->optional()->paragraph(),
            'meta' => [
                'transport' => fake()->randomElement(['Ambulance', 'Mandiri', 'Lainnya']),
            ],
        ];
    }
}
