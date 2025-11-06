<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\SyncQueue;
use App\Models\User;
use App\Models\Visit;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'coverage_type', 'clinic_name', 'date_from', 'date_to']);
        $perPage = (int) $request->query('per_page', 15);

        $visits = Visit::query()
            ->with(['patient:id,name,medical_record_number', 'provider:id,name'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('medical_record_number', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->when($filters['coverage_type'] ?? null, function ($query, $coverageType) {
                $query->where('coverage_type', $coverageType);
            })
            ->when($filters['clinic_name'] ?? null, function ($query, $clinic) {
                $query->where('clinic_name', 'like', "%{$clinic}%");
            })
            ->when($filters['date_from'] ?? null, function ($query, $dateFrom) {
                $query->whereDate('visit_datetime', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function ($query, $dateTo) {
                $query->whereDate('visit_datetime', '<=', $dateTo);
            })
            ->latest('visit_datetime')
            ->paginate(max(5, $perPage <= 50 ? $perPage : 15))
            ->withQueryString();

        return view('visits.index', [
            'visits' => $visits,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patientId = $request->integer('patient_id');
        $patient = $patientId ? Patient::find($patientId) : null;
        $patients = Patient::query()
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'medical_record_number']);

        $providers = User::role('doctor')
            ->orderBy('name')
            ->get(['id', 'name']);

        // Generate queue number map untuk semua poli
        $queueDate = now('Asia/Jakarta')->toDateString();
        $poliOptions = config('queue_ticket.departments', [
            config('queue_ticket.default_department', 'Poli Umum'),
        ]);

        $queueNumberMap = collect($poliOptions)
            ->mapWithKeys(function (string $department) use ($queueDate) {
                return [$department => QueueTicket::nextNumberForDate($queueDate, $department)];
            })
            ->all();

        $defaultDepartment = $poliOptions[0] ?? config('queue_ticket.default_department', 'Poli Umum');
        $defaultClinic = $request->old('clinic_name',
            $request->get('clinic_name',
                $patient?->meta['default_clinic'] ??
                (auth()->user()?->department ?? $defaultDepartment)
            )
        );

        return view('visits.create', [
            'patient' => $patient,
            'patients' => $patients,
            'providers' => $providers,
            'poliOptions' => $poliOptions,
            'queueNumberMap' => $queueNumberMap,
            'defaultClinic' => $defaultClinic,
            'nextQueueNumber' => $queueNumberMap[$defaultClinic] ?? QueueTicket::nextNumberForDate($queueDate),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'provider_id' => ['nullable', 'exists:users,id'],
            'visit_datetime' => ['required', 'date'],
            'clinic_name' => ['required', 'string', 'max:255'],
            'coverage_type' => ['required', Rule::in(['BPJS', 'UMUM'])],
            'sep_no' => ['nullable', 'string', 'max:40'],
            'bpjs_reference_no' => ['nullable', 'string', 'max:40'],
            'queue_number' => ['nullable', 'string', 'max:20', 'regex:/^[A-Z]{1,3}\d{3,4}$/'],
            'chief_complaint' => ['nullable', 'string'],
            'triage_notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['SCHEDULED', 'ONGOING', 'COMPLETED', 'CANCELLED'])],
            'meta' => ['nullable', 'array'],
        ]);

        // Use database transaction to ensure atomicity
        $visit = \DB::transaction(function () use ($data) {
            $visitDate = \Carbon\Carbon::parse($data['visit_datetime'])->toDateString();
            $department = $data['clinic_name'];

            // Generate queue number sesuai poli/department
            $queueNumber = QueueTicket::nextNumberForDate($visitDate, $department);

            // Validate prefix jika user input manual queue_number
            if (!empty($data['queue_number'])) {
                $expectedPrefix = \Illuminate\Support\Str::upper(
                    preg_replace('/\d+$/', '', $queueNumber)
                );
                $actualPrefix = \Illuminate\Support\Str::upper(
                    preg_replace('/\d+$/', '', $data['queue_number'])
                );

                if ($expectedPrefix !== $actualPrefix) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'queue_number' => "Nomor antrian harus dimulai dengan prefix {$expectedPrefix} untuk {$department}. Anda memasukkan: {$data['queue_number']}",
                    ]);
                }

                // Check duplikasi di visits
                $existingVisit = Visit::where('queue_number', $data['queue_number'])
                    ->whereNull('deleted_at')
                    ->first();

                if ($existingVisit) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'queue_number' => "Nomor antrian {$data['queue_number']} sudah digunakan pada kunjungan lain (Visit #{$existingVisit->visit_number}).",
                    ]);
                }

                $queueNumber = $data['queue_number'];
            }

            // Auto-create QueueTicket jika belum ada
            $queueTicket = QueueTicket::create([
                'patient_id' => $data['patient_id'],
                'tanggal_antrian' => $visitDate,
                'nomor_antrian' => $queueNumber,
                'department' => $department,
                'status' => QueueTicket::STATUS_CALLING,
                'meta' => [
                    'created_by' => Auth::id(),
                    'auto_created_from_visit' => true,
                ],
            ]);

            // Create Visit dengan queue_number yang SAMA
            $visit = Visit::create([
                'patient_id' => $data['patient_id'],
                'provider_id' => $data['provider_id'] ?? Auth::id(),
                'visit_number' => $this->generateVisitNumber(),
                'visit_datetime' => $data['visit_datetime'],
                'clinic_name' => $department,
                'coverage_type' => $data['coverage_type'],
                'sep_no' => $data['sep_no'] ?? null,
                'bpjs_reference_no' => $data['bpjs_reference_no'] ?? null,
                'queue_number' => $queueNumber,
                'status' => $data['status'] ?? 'ONGOING',
                'chief_complaint' => $data['chief_complaint'] ?? null,
                'triage_notes' => $data['triage_notes'] ?? null,
                'meta' => $data['meta'] ?? [],
            ]);

            // Link QueueTicket ke Visit
            $queueTicket->visit_id = $visit->id;
            $queueTicket->save();

            return $visit;
        });

        Audit::log('visit_created', Visit::class, $visit->id, [
            'new' => $this->auditableVisitData($visit),
        ], [
            'patient_id' => $visit->patient_id,
        ]);

        return redirect()
            ->route('visits.show', $visit)
            ->with('status', 'Kunjungan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit)
    {
        // Check if queue number matches with related queue ticket
        if ($visit->queueTicket) {
            $expectedQueueNumber = $visit->queueTicket->nomor_antrian;
            
            if ($visit->queue_number !== $expectedQueueNumber) {
                // If there's a mismatch, log it and potentially correct it
                \Log::warning('Visit queue_number does not match QueueTicket nomor_antrian', [
                    'visit_id' => $visit->id,
                    'visit_queue_number' => $visit->queue_number,
                    'queue_ticket_number' => $expectedQueueNumber,
                    'patient_id' => $visit->patient_id
                ]);
                
                // Optionally correct the queue number to match the QueueTicket
                // Only do this if queue number is empty or A-prefixed (likely auto-generated)
                if (empty($visit->queue_number) || (preg_match('/^A\d+$/', $visit->queue_number) && !preg_match('/^A\d+$/', $expectedQueueNumber))) {
                    $visit->queue_number = $expectedQueueNumber;
                    $visit->save();
                    
                    \Log::info('Visit queue_number corrected to match QueueTicket', [
                        'visit_id' => $visit->id,
                        'corrected_queue_number' => $expectedQueueNumber
                    ]);
                }
            }
        }
        
        // Redirect to EMR page for unified experience
        return redirect()->route('emr.show', $visit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visit $visit)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['SCHEDULED', 'ONGOING', 'COMPLETED', 'CANCELLED'])],
            'sep_no' => ['nullable', 'string', 'max:40'],
            'bpjs_reference_no' => ['nullable', 'string', 'max:40'],
            'queue_number' => ['nullable', 'string', 'max:20'],
            'chief_complaint' => ['nullable', 'string'],
            'triage_notes' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ]);

        $visit->fill($data);

        $dirty = collect($visit->getDirty())
            ->except(['updated_at'])
            ->map(function ($value, string $field) use ($visit) {
                return [
                    'old' => $visit->getOriginal($field),
                    'new' => $value,
                ];
            })
            ->toArray();

        \Log::info('Visit update method called', [
            'visit_id' => $visit->id,
            'updated_fields' => array_keys($visit->getDirty()),
            'user_id' => auth()->id()
        ]);
        
        if (isset($data['queue_number']) && $visit->isDirty('queue_number')) {
            \Log::warning('Visit queue_number being updated in VisitController@update', [
                'visit_id' => $visit->id,
                'old_queue_number' => $visit->getOriginal('queue_number'),
                'new_queue_number' => $data['queue_number'],
                'user_id' => auth()->id()
            ]);
        }

        $visit->save();

        if (! empty($dirty)) {
            Audit::log('visit_updated', Visit::class, $visit->id, $dirty, [
                'patient_id' => $visit->patient_id,
            ]);
        }

        return redirect()
            ->route('visits.show', $visit)
            ->with('status', 'Kunjungan berhasil diperbarui.');
    }

    private function generateVisitNumber(): string
    {
        do {
            $candidate = 'VIS'.now('Asia/Jakarta')->format('ymdHis');
        } while (Visit::where('visit_number', $candidate)->exists());

        return $candidate;
    }

    private function auditableVisitData(Visit $visit): array
    {
        return $visit->only([
            'patient_id',
            'provider_id',
            'visit_number',
            'visit_datetime',
            'clinic_name',
            'coverage_type',
            'sep_no',
            'bpjs_reference_no',
            'queue_number',
            'status',
        ]);
    }
}
