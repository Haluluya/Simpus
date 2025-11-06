<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all records to avoid duplicates
        Medicine::query()->delete();

        Medicine::factory()->count(12)->create();
    }
}
