<?php

namespace Database\Seeders;

use App\Models\EmrNote;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete related data to avoid duplicates
        \DB::table('lab_order_items')->delete();
        \DB::table('lab_orders')->delete();
        \DB::table('emr_notes')->delete();
        Visit::query()->delete();

        $doctor = User::role('doctor')->first();
        $labUser = User::role('lab')->first();

        if (! $doctor) {
            $doctor = User::factory()->create()->assignRole('doctor');
        }

        if (! $labUser) {
            $labUser = User::factory()->create()->assignRole('lab');
        }

        Patient::all()->each(function (Patient $patient) use ($doctor, $labUser) {
            $visitTotal = random_int(1, 3);

            for ($i = 0; $i < $visitTotal; $i++) {
                $visit = Visit::factory()->create([
                    'patient_id' => $patient->id,
                    'provider_id' => $doctor->id,
                ]);

                EmrNote::factory()->create([
                    'visit_id' => $visit->id,
                    'author_id' => $doctor->id,
                ]);

                if (random_int(0, 1) === 1) {
                    $labOrder = LabOrder::factory()->create([
                        'visit_id' => $visit->id,
                        'ordered_by' => $doctor->id,
                        'verified_by' => $labUser->id,
                    ]);

                    LabOrderItem::factory()
                        ->count(random_int(1, 3))
                        ->create([
                            'lab_order_id' => $labOrder->id,
                            'result_status' => $labOrder->status === 'COMPLETED' ? 'FINAL' : 'PRELIMINARY',
                        ]);
                }
            }
        });
    }
}
