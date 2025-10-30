<?php

namespace Database\Seeders;

use App\Models\LabOrder;
use App\Models\LabOrderResult;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class LabWorkQueueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil atau buat dokter
        $dokter = User::whereHas('roles', function ($q) {
            $q->where('name', 'dokter');
        })->first();

        if (!$dokter) {
            echo "⚠️ Tidak ada user dengan role 'dokter'. Silakan buat user dokter terlebih dahulu.\n";
            return;
        }

        // Ambil atau buat beberapa pasien
        $patients = Patient::take(5)->get();
        
        if ($patients->count() < 5) {
            echo "⚠️ Kurang dari 5 pasien di database. Membuat pasien dummy...\n";
            $patients = Patient::factory(5)->create();
        }

        $testNames = [
            ['Darah Lengkap', 'Hemoglobin', 'Leukosit'],
            ['Gula Darah Sewaktu'],
            ['Urin Lengkap', 'Protein Urin', 'Glukosa Urin'],
            ['Kolesterol Total', 'Trigliserida', 'HDL', 'LDL'],
            ['Fungsi Hati (SGOT)', 'Fungsi Hati (SGPT)'],
        ];

        $clinics = ['Poli Umum', 'Poli KIA', 'Poli Gigi', 'IGD', 'Poli Anak'];

        foreach ($patients as $index => $patient) {
            // Buat visit
            $visit = Visit::create([
                'patient_id' => $patient->id,
                'provider_id' => $dokter->id,
                'visit_datetime' => now()->subMinutes(rand(10, 300)),
                'clinic_name' => $clinics[$index],
                'queue_number' => $index + 1,
                'coverage_type' => rand(0, 1) ? 'BPJS' : 'Umum',
                'status' => 'ONGOING',
            ]);

            // Buat lab order
            $labOrder = LabOrder::create([
                'visit_id' => $visit->id,
                'ordered_by' => $dokter->id,
                'order_number' => 'LAB-' . now()->subMinutes(rand(5, 250))->format('YmdHis') . rand(100, 999),
                'status' => $index < 3 ? 'PENDING' : 'COMPLETED',
                'priority' => 'normal',
                'requested_at' => now()->subMinutes(rand(5, 250)),
                'completed_at' => $index >= 3 ? now()->subMinutes(rand(1, 30)) : null,
            ]);

            // Tambahkan test results
            foreach ($testNames[$index] as $testName) {
                LabOrderResult::create([
                    'lab_order_id' => $labOrder->id,
                    'nama_tes' => $testName,
                    'hasil' => $labOrder->status === 'COMPLETED' ? rand(10, 150) . ' mg/dL' : null,
                    'nilai_rujukan' => '10-100 mg/dL',
                    'catatan' => $labOrder->status === 'COMPLETED' ? 'Normal' : null,
                ]);
            }
        }

        echo "✅ Berhasil membuat " . $patients->count() . " lab orders dengan berbagai status!\n";
        echo "📊 3 order dengan status PENDING (menunggu)\n";
        echo "✅ 2 order dengan status COMPLETED (selesai)\n";
    }
}
