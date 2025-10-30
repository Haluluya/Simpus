<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Medicine>
 */
class MedicineFactory extends Factory
{
    protected $model = Medicine::class;

    public function definition(): array
    {
        return [
            'kode' => 'OBT'.Str::upper(Str::random(5)),
            'nama' => fake()->randomElement([
                'Paracetamol 500mg',
                'Amoxicillin 500mg',
                'Metformin 500mg',
                'Ibuprofen 400mg',
                'Omeprazole 20mg',
            ]),
            'satuan' => fake()->randomElement(['tablet', 'kapsul', 'botol', 'tube']),
            'stok' => fake()->numberBetween(20, 200),
            'stok_minimal' => fake()->numberBetween(10, 50),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }
}
