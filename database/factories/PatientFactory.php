<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = $this->faker ?? fake();
        $gender = $faker->randomElement(['male', 'female']);
        $bloodTypes = ['A', 'B', 'AB', 'O'];

        // Generate NIK 16 digit dan BPJS 13 digit
        $nik = $faker->unique()->numerify('################');
        $bpjs = $faker->unique()->numerify('#############');

        // Randomly assign BPJS status (10% chance inactive)
        $isInactive = $faker->boolean(10);
        $bpjsStatus = $isInactive ? 'TIDAK AKTIF' : 'AKTIF';
        $bpjsClass = $isInactive ? 'KELAS TIDAK BERLAKU' : $faker->randomElement(['KELAS I', 'KELAS II', 'KELAS III']);
        $participantType = $faker->randomElement(['PNS', 'PPU', 'PBPU']);

        return [
            'medical_record_number' => 'RM'.$faker->unique()->numerify('#####'),
            'nik' => $nik,
            'bpjs_card_no' => $faker->boolean(90) ? $bpjs : null, // 90% have BPJS
            'name' => strtoupper($faker->name()),
            'date_of_birth' => $faker->dateTimeBetween('-80 years', '-1 years'),
            'gender' => $gender,
            'blood_type' => $faker->boolean(50) ? $faker->randomElement($bloodTypes) : null,
            'phone' => '08'.$faker->numerify('##########'),
            'email' => $faker->boolean(50) ? $faker->unique()->safeEmail() : null,
            'address' => $faker->streetAddress(),
            'village' => $faker->citySuffix(),
            'district' => $faker->city(),
            'city' => $faker->city(),
            'province' => $faker->state(),
            'postal_code' => $faker->postcode(),
            'occupation' => $faker->boolean(40) ? $faker->jobTitle() : null,
            'allergies' => $faker->boolean(40) ? $faker->sentence() : null,
            'emergency_contact_name' => $faker->name(),
            'emergency_contact_phone' => '08'.$faker->numerify('##########'),
            'emergency_contact_relation' => $faker->randomElement(['Orang Tua', 'Suami/Istri', 'Anak', 'Saudara']),
            'meta' => [
                'marital_status' => $faker->randomElement(['single', 'married', 'widowed']),
                'education' => $faker->randomElement(['SMA', 'D3', 'S1', 'S2']),
                'bpjs_status' => $bpjsStatus,
                'bpjs_class' => $bpjsClass,
                'participant_type' => $participantType,
            ],
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
