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

        return view('visits.create', [
            'patient' => $patient,
            'patients' => $patients,
            'providers' => $providers,
            'nextQueueNumber' => QueueTicket::nextNumberForDate(now('Asia/Jakarta')),
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
            'queue_number' => ['nullable', 'string', 'max:20'],
            'chief_complaint' => ['nullable', 'string'],
            'triage_notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['SCHEDULED', 'ONGOING', 'COMPLETED', 'CANCELLED'])],
            'meta' => ['nullable', 'array'],
        ]);

        $visit = Visit::create([
            'patient_id' => $data['patient_id'],
            'provider_id' => $data['provider_id'] ?? Auth::id(),
            'visit_number' => $this->generateVisitNumber(),
            'visit_datetime' => $data['visit_datetime'],
            'clinic_name' => $data['clinic_name'],
            'coverage_type' => $data['coverage_type'],
            'sep_no' => $data['sep_no'] ?? null,
            'bpjs_reference_no' => $data['bpjs_reference_no'] ?? null,
            'queue_number' => $data['queue_number'] ?? null,
            'status' => $data['status'] ?? 'ONGOING',
            'chief_complaint' => $data['chief_complaint'] ?? null,
            'triage_notes' => $data['triage_notes'] ?? null,
            'meta' => $data['meta'] ?? [],
        ]);

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
