<?php

namespace App\Http\Controllers;

use App\Models\BpjsClaim;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\SyncQueue;
use App\Rules\DoctorAvailableInDepartment;
use App\Support\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * API endpoint for patient search autocomplete
     */
    public function searchApi(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = Patient::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('nik', 'like', "%{$query}%")
                  ->orWhere('bpjs_card_no', 'like', "%{$query}%")
                  ->orWhere('medical_record_number', 'like', "%{$query}%");
            })
            ->select(['id', 'name', 'medical_record_number', 'nik'])
            ->limit(10)
            ->get();

        return response()->json($patients);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $perPage = (int) $request->query('per_page', 5);
        $paymentType = $request->query('payment_type');

        $patients = Patient::query()
            ->withCount('visits')
            ->with(['latestVisit' => function ($query) {
                $query->latest('visit_datetime')->limit(1);
            }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('bpjs_card_no', 'like', "%{$search}%")
                        ->orWhere('medical_record_number', 'like', "%{$search}%");
                });
            })
            ->when($paymentType === 'bpjs', function ($query) {
                $query->whereNotNull('bpjs_card_no');
            })
            ->when($paymentType === 'umum', function ($query) {
                $query->whereNull('bpjs_card_no');
            })
            ->latest('created_at')
            ->paginate(max(5, $perPage <= 50 ? $perPage : 5))
            ->withQueryString();

        return view('patients.index', [
            'patients' => $patients,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $poliOptions = config('queue_ticket.departments', [
            'Poli Umum',
            'Poli Gigi',
            'Poli KIA (Kesehatan Ibu dan Anak)',
            'Poli Anak',
        ]);

        $defaultDepartment = $poliOptions[0] ?? config('queue_ticket.default_department', 'Poli Umum');
        $queueDate = Carbon::now('Asia/Jakarta')->toDateString();

        $queueNumberMap = collect($poliOptions)
            ->mapWithKeys(function (string $department) use ($queueDate) {
                return [$department => QueueTicket::nextNumberForDate($queueDate, $department)];
            })
            ->all();

        // Daftar dokter per poli (from database with caching, fallback to config)
        $doctorsByPoli = Doctor::getByDepartment() ?: config('doctors.by_department', []);

        return view('patients.create', [
            'poliOptions' => $poliOptions,
            'queueNumberMap' => $queueNumberMap,
            'defaultDepartment' => $defaultDepartment,
            'queueDate' => $queueDate,
            'doctorsByPoli' => $doctorsByPoli,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        // Validate queue-related fields if enqueue_after is true
        if ($request->boolean('enqueue_after')) {
            $validDepartments = config('queue_ticket.departments', []);
            $validPaymentMethods = ['BPJS', 'UMUM'];

            $queueDepartment = $request->input('queue_department');

            $request->validate([
                'queue_date' => ['nullable', 'date', 'after_or_equal:today'],
                'queue_department' => ['nullable', 'string', Rule::in($validDepartments)],
                'queue_doctor' => ['nullable', 'string', new DoctorAvailableInDepartment($queueDepartment)],
                'queue_payment_method' => ['nullable', 'string', Rule::in($validPaymentMethods)],
            ]);
        }

        $data['medical_record_number'] = $data['medical_record_number'] ?? $this->generateMedicalRecordNumber();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Use transaction for patient creation and optional queue enrollment
        $patient = DB::transaction(function () use ($data, $request) {
            $patient = Patient::create($data);

            Audit::log('patient_created', Patient::class, $patient->id, [
                'new' => $this->auditablePatientData($patient),
            ]);

            if ($request->boolean('enqueue_after')) {
                $queueDate = $request->input('queue_date')
                    ? Carbon::parse($request->input('queue_date'), 'Asia/Jakarta')->toDateString()
                    : Carbon::now('Asia/Jakarta')->toDateString();

                $queueDepartment = $request->input('queue_department', config('queue_ticket.default_department', 'Poli Umum'));

                QueueTicket::create([
                    'patient_id' => $patient->id,
                    'tanggal_antrian' => $queueDate,
                    'nomor_antrian' => QueueTicket::nextNumberForDate($queueDate, $queueDepartment),
                    'department' => $queueDepartment,
                    'doctor' => $request->input('queue_doctor'),
                    'payment_method' => $request->input('queue_payment_method'),
                    'status' => QueueTicket::STATUS_WAITING,
                    'meta' => [
                        'created_by' => Auth::id(),
                        'source' => 'registration',
                    ],
                ]);
            }

            return $patient;
        });

        $redirectTo = $request->input('redirect_to');

        if ($redirectTo === 'registrations.index') {
            return redirect()
                ->route('registrations.index', [
                    'tanggal' => $request->input('queue_date', Carbon::now('Asia/Jakarta')->toDateString()),
                ])
                ->with('status', 'Pasien berhasil ditambahkan.');
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Pasien berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->load([
            'visits' => function ($query) {
                $query->latest('visit_datetime')
                    ->with(['provider:id,name', 'emrNotes' => function ($sub) {
                        $sub->latest('created_at')->limit(5);
                    }, 'labOrders' => function ($labQuery) {
                        $labQuery->latest('requested_at')->limit(5);
                    }]);
            },
            'referrals' => function ($query) {
                $query->latest()
                    ->with(['visit:id,visit_number,clinic_name,visit_datetime'])
                    ->limit(10);
            },
        ]);

        $latestBpjsClaim = BpjsClaim::where('patient_id', $patient->id)->latest()->first();
        $latestPatientSync = SyncQueue::where('entity_type', Patient::class)
            ->where('entity_id', $patient->id)
            ->where('target', 'SATUSEHAT')
            ->latest()
            ->first();

        return view('patients.show', [
            'patient' => $patient,
            'latestBpjsClaim' => $latestBpjsClaim,
            'latestPatientSync' => $latestPatientSync,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', [
            'patient' => $patient,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $data = $this->validatedData($request, $patient);
        $data['updated_by'] = Auth::id();

        $patient->fill($data);

        $dirty = collect($patient->getDirty())
            ->except(['updated_at'])
            ->map(function ($value, string $field) use ($patient) {
                return [
                    'old' => $patient->getOriginal($field),
                    'new' => $value,
                ];
            })
            ->toArray();

        $patient->save();

        if (! empty($dirty)) {
            Audit::log('patient_updated', Patient::class, $patient->id, $dirty);
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $snapshot = $this->auditablePatientData($patient);

        $patient->delete();

        Audit::log('patient_deleted', Patient::class, $patient->id, null, [
            'snapshot' => $snapshot,
        ]);

        return redirect()
            ->route('patients.index')
            ->with('status', 'Pasien berhasil diarsipkan.');
    }

    private function validatedData(Request $request, ?Patient $patient = null): array
    {
        return $request->validate([
            'medical_record_number' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('patients', 'medical_record_number')->ignore($patient?->id),
            ],
            'nik' => [
                'required',
                'string',
                'max:20',
                Rule::unique('patients', 'nik')->ignore($patient?->id),
            ],
            'bpjs_card_no' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('patients', 'bpjs_card_no')->ignore($patient?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'blood_type' => ['nullable', 'string', 'max:3'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
            'meta' => ['nullable', 'array'],
        ]);
    }

    private function generateMedicalRecordNumber(): string
    {
        do {
            $candidate = 'RM'.now('Asia/Jakarta')->format('ymd').strtoupper(Str::random(3));
        } while (Patient::withTrashed()->where('medical_record_number', $candidate)->exists());

        return $candidate;
    }

    private function auditablePatientData(Patient $patient): array
    {
        return $patient->only([
            'medical_record_number',
            'nik',
            'bpjs_card_no',
            'name',
            'date_of_birth',
            'gender',
            'phone',
            'email',
            'address',
            'city',
            'province',
        ]);
    }

}
