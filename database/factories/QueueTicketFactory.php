<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<QueueTicket>
 */
class QueueTicketFactory extends Factory
{
    protected $model = QueueTicket::class;

    public function definition(): array
    {
        $patient = Patient::inRandomOrder()->first() ?? Patient::factory()->create();
        $visit = Visit::where('patient_id', $patient->id)->inRandomOrder()->first();
        $date = fake()->dateTimeBetween('today', '+2 days');

        return [
            'patient_id' => $patient->id,
            'visit_id' => $visit?->id,
            'tanggal_antrian' => $date,
            'nomor_antrian' => Str::upper(fake()->randomLetter()).fake()->numerify('##'),
            'status' => fake()->randomElement(QueueTicket::statuses()),
            'meta' => [
                'loket' => fake()->randomElement(['Loket 1', 'Loket 2', 'UGD', 'Laboratorium']),
            ],
        ];
    }
}
