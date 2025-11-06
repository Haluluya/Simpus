<?php

namespace App\Http\Controllers;

use App\Jobs\SyncEncounterJob;
use App\Models\EmrNote;
use App\Models\LabOrder;
use App\Models\LabOrderResult;
use App\Models\MasterMedicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Referral;
use App\Models\Visit;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmrController extends Controller
{
    /**
     * Menampilkan halaman RME untuk dokter
     */
    public function show(Visit $visit)
    {
        // Check and ensure queue number consistency before loading EMR
        $wasCorrected = $visit->ensureQueueNumberConsistency();
        
        if ($wasCorrected) {
            \Log::info('Queue number corrected in EMR show', [
                'visit_id' => $visit->id,
                'queue_number' => $visit->queue_number,
                'patient_name' => $visit->patient?->name
            ]);
        }
        
        $visit->load([
            'patient',
            'provider',
            'emrNotes' => fn ($q) => $q->with('author')->latest(),
            'labOrders' => fn ($q) => $q->with('results')->latest(),
            'prescriptions' => fn ($q) => $q->with('items.medicine')->latest(),
            'referrals' => fn ($q) => $q->latest(),
        ]);

        // Log untuk melihat nomor antrian ketika RME dibuka
        \Log::info("EMR Show - Visit loaded", [
            'visit_id' => $visit->id,
            'queue_number' => $visit->queue_number,
            'patient_name' => $visit->patient?->name
        ]);

        $medicines = MasterMedicine::orderBy('nama_obat')->get();

        return view('emr.show', [
            'visit' => $visit,
            'medicines' => $medicines,
        ]);
    }

    /**
     * Simpan SOAP, order lab, resep, rujukan dan finalisasi kunjungan
     */
    public function store(Request $request, Visit $visit)
    {
        $data = $request->validate([
            'subjective' => ['required', 'string'],
            'objective' => ['required', 'string'],
            'assessment' => ['required', 'string'],
            'plan' => ['required', 'string'],
            'icd10_code' => ['required', 'string', 'max:10'],
            'icd10_description' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'prescription_notes' => ['nullable', 'string'],
            // Tanda Vital
            'tekanan_darah' => ['nullable', 'string', 'max:20'],
            'nadi' => ['nullable', 'integer', 'min:0'],
            'suhu' => ['nullable', 'numeric', 'min:0'],
            'pernafasan' => ['nullable', 'integer', 'min:0'],
            // Order Lab
            'lab_tests' => ['nullable', 'array'],
            'lab_tests.*' => ['required', 'string'],
            // Resep
            'prescriptions' => ['nullable', 'array'],
            'prescriptions.*.medicine_id' => ['required', 'exists:master_medicines,id'],
            'prescriptions.*.jumlah' => ['required', 'integer', 'min:1'],
            'prescriptions.*.dosis' => ['nullable', 'string'],
            // Rujukan
            'referral_faskes' => ['nullable', 'string'],
            'referral_poli' => ['nullable', 'string'],
            'referral_catatan' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan/Update EMR Note (SOAP)
            $note = $visit->emrNotes()->create([
                'author_id' => Auth::id(),
                'subjective' => $data['subjective'] ?? null,
                'objective' => $data['objective'] ?? null,
                'assessment' => $data['assessment'] ?? null,
                'plan' => $data['plan'] ?? null,
                'icd10_code' => $data['icd10_code'] ?? null,
                'icd10_description' => $data['icd10_description'] ?? null,
                'notes' => $data['notes'] ?? null,
                'meta' => [
                    'vital_signs' => [
                        'tekanan_darah' => $data['tekanan_darah'] ?? null,
                        'nadi' => $data['nadi'] ?? null,
                        'suhu' => $data['suhu'] ?? null,
                        'pernafasan' => $data['pernafasan'] ?? null,
                    ],
                ],
            ]);

            Audit::log('emr_note_created', EmrNote::class, $note->id, [
                'new' => $note->only(['subjective', 'objective', 'assessment', 'plan', 'icd10_code']),
            ], ['visit_id' => $visit->id]);

            // 2. Buat Lab Order jika ada
            if (! empty($data['lab_tests'])) {
                $labOrder = LabOrder::create([
                    'visit_id' => $visit->id,
                    'ordered_by' => Auth::id(),
                    'order_number' => 'LAB-'.now()->format('YmdHis'),
                    'status' => 'Pending',
                    'priority' => 'normal',
                    'requested_at' => now(),
                ]);

                foreach ($data['lab_tests'] as $testName) {
                    if (filled($testName)) {
                        LabOrderResult::create([
                            'lab_order_id' => $labOrder->id,
                            'nama_tes' => $testName,
                            'hasil' => null,
                            'nilai_rujukan' => null,
                        ]);
                    }
                }

                Audit::log('lab_order_created', LabOrder::class, $labOrder->id, [
                    'new' => ['order_number' => $labOrder->order_number],
                ], ['visit_id' => $visit->id]);
            }

            // 3. Buat Prescription jika ada
            if (! empty($data['prescriptions'])) {
                $prescription = Prescription::create([
                    'visit_id' => $visit->id,
                    'user_id_doctor' => Auth::id(),
                    'status' => 'Baru',
                    'catatan' => $data['prescription_notes'] ?? null,
                ]);

                foreach ($data['prescriptions'] as $item) {
                    if (filled($item['medicine_id'])) {
                        PrescriptionItem::create([
                            'prescription_id' => $prescription->id,
                            'master_medicine_id' => $item['medicine_id'],
                            'jumlah' => $item['jumlah'],
                            'dosis' => $item['dosis'] ?? null,
                        ]);
                    }
                }

                Audit::log('prescription_created', Prescription::class, $prescription->id, [
                    'new' => ['status' => 'Baru'],
                ], ['visit_id' => $visit->id]);
            }

            // 4. Buat Rujukan & panggil Mock BPJS jika ada
            if (filled($data['referral_faskes'])) {
                $nomorRujukan = null;
                try {
                    $response = Http::timeout(5)
                        ->post(url('/api/mock/bpjs/vclaim/rujukan'), [
                            'faskes_tujuan' => $data['referral_faskes'],
                            'poli_tujuan' => $data['referral_poli'],
                            'catatan' => $data['referral_catatan'],
                        ])
                        ->json();
                    $nomorRujukan = $response['nomor_rujukan'] ?? null;
                } catch (\Exception $e) {
                    Log::error('Mock BPJS rujukan failed', ['error' => $e->getMessage()]);
                }

                $referral = Referral::create([
                    'visit_id' => $visit->id,
                    'referral_number' => 'REF-'.now()->format('YmdHis'),
                    'referred_to' => $data['referral_faskes'],
                    'department' => $data['referral_poli'] ?? null,
                    'diagnosis' => $data['icd10_description'] ?? null,
                    'scheduled_at' => now()->addDays(1),
                    'status' => 'pending',
                    'notes' => $data['referral_catatan'] ?? null,
                    'nomor_rujukan_bpjs' => $nomorRujukan,
                ]);

                Audit::log('referral_created', Referral::class, $referral->id, [
                    'new' => ['referred_to' => $referral->referred_to, 'nomor_rujukan_bpjs' => $nomorRujukan],
                ], ['visit_id' => $visit->id]);
            }

            // 5. Update Visit status jadi Selesai
            $visit->update(['status' => 'COMPLETED']);

            // 6. Dispatch Job untuk sync ke SatuSehat (Mock)
            SyncEncounterJob::dispatch($visit->id);

            DB::commit();

            return redirect()
                ->route('emr.show', $visit)
                ->with('status', 'RME berhasil disimpan dan kunjungan selesai.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EMR store failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan RME: '.$e->getMessage());
        }
    }
}
