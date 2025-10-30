<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genders = ['male', 'female'];
        $gender = fake()->randomElement($genders);

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => '08'.fake()->numerify('##########'),
            'gender' => $gender,
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-25 years'),
            'license_number' => strtoupper('STR'.fake()->unique()->bothify('########')),
            'professional_identifier' => strtoupper(fake()->bothify('PRAC#######')),
            'department' => fake()->randomElement(['Poli Umum', 'Poli Gigi', 'Poli Anak', 'Laboratorium']),
            'designation' => fake()->randomElement(['Administrator', 'Dokter Umum', 'Dokter Gigi', 'Analis Lab']),
            'last_login_at' => now()->subDays(fake()->numberBetween(0, 15)),
            'profile_meta' => [
                'language' => fake()->randomElement(['id', 'en']),
                'signature' => Str::upper(fake()->bothify('SIG#####')),
            ],
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
