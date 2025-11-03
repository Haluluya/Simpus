<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class QueueTicketController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['tanggal', 'status', 'search']);
        $tanggalInput = $filters['tanggal'] ?? null;
        $tanggal = $tanggalInput ? Carbon::parse($tanggalInput, 'Asia/Jakarta') : Carbon::today('Asia/Jakarta');

        $tickets = QueueTicket::query()
            ->with(['patient:id,name,medical_record_number,nik'])
            ->whereDate('tanggal_antrian', $tanggal)
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['search'] ?? null, function ($query, $search) {
                // Use where with closure to properly group OR conditions
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('medical_record_number', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    })->orWhere('nomor_antrian', 'like', "%{$search}%");
                });
            })
            ->orderBy('nomor_antrian')
            ->paginate(20)
            ->withQueryString();

        $canCreate = $request->user()?->can('queue.create');
        $patients = $canCreate
            ? Patient::orderBy('name')->limit(50)->get(['id', 'name', 'medical_record_number'])
            : collect();

        $queueDate = $tanggal->toDateString();
        $poliOptions = config('queue_ticket.departments', [
            config('queue_ticket.default_department', 'Poli Umum'),
        ]);

        $queueNumberMap = collect($poliOptions)
            ->mapWithKeys(function (string $department) use ($queueDate) {
                return [$department => QueueTicket::nextNumberForDate($queueDate, $department)];
            })
            ->all();

        $defaultDepartment = $poliOptions[0] ?? config('queue_ticket.default_department', 'Poli Umum');
        $selectedDepartment = $request->old('department', $defaultDepartment);

        if ($selectedDepartment && ! in_array($selectedDepartment, $poliOptions, true)) {
            $selectedDepartment = $defaultDepartment;
        }

        return view('queues.index', [
            'tickets' => $tickets,
            'filters' => $filters + ['tanggal' => $tanggal->toDateString()],
            'statuses' => QueueTicket::statuses(),
            'patients' => $patients,
            'poliOptions' => $poliOptions,
            'queueNumberMap' => $queueNumberMap,
            'selectedDepartment' => $selectedDepartment,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'tanggal_antrian' => ['required', 'date', 'after_or_equal:today'],
            'nomor_antrian' => ['nullable', 'string', 'max:10'],
            'department' => ['nullable', 'string', 'max:100', Rule::in(config('queue_ticket.departments', []))],
        ]);

        $tanggal = Carbon::parse($data['tanggal_antrian'])->toDateString();
        $department = $data['department'] ?? config('queue_ticket.default_department', 'Poli Umum');

        // Use transaction because nextNumberForDate uses lockForUpdate
        DB::transaction(function () use ($data, $tanggal, $department) {
            $nomor = $data['nomor_antrian'] ?: QueueTicket::nextNumberForDate($tanggal, $department);

            QueueTicket::create([
                'patient_id' => $data['patient_id'],
                'tanggal_antrian' => $tanggal,
                'nomor_antrian' => $nomor,
                'department' => $department,
                'status' => QueueTicket::STATUS_WAITING,
                'meta' => [
                    'created_by' => Auth::id(),
                ],
            ]);
        });

        $redirect = $request->input('redirect_to');

        if ($redirect === 'registrations.index') {
            return redirect()
                ->route('registrations.index', ['tanggal' => $tanggal])
            ->with('status', 'Nomor antrian berhasil ditambahkan.');
        }

        return redirect()
            ->route('queues.index', ['tanggal' => $tanggal])
            ->with('status', 'Antrian berhasil ditambahkan.');
    }

    public function update(Request $request, QueueTicket $queue)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(QueueTicket::statuses())],
        ]);

        $queue->update($data);

        $redirect = $request->input('redirect_to');
        $routeParams = ['tanggal' => $queue->tanggal_antrian->toDateString()];

        if ($redirect === 'registrations.index') {
            return redirect()
                ->route('registrations.index', $routeParams)
                ->with('status', 'Status antrian diperbarui.');
        }

        return redirect()
            ->route('queues.index', $routeParams)
            ->with('status', 'Status antrian diperbarui.');
    }

    /**
     * Dokter mulai melayani pasien dari antrian.
     * Jika belum ada Visit terkait, buat otomatis dan arahkan ke halaman RME kunjungan.
     */
    public function serve(Request $request, QueueTicket $queue)
    {
        // Authorization check: user must have permission to create visits
        if (! $request->user()?->can('visit.create')) {
            abort(403, 'Anda tidak memiliki izin untuk melayani antrian.');
        }

        $queue->loadMissing('patient');

        if ($queue->visit_id) {
            return redirect()->route('visits.show', $queue->visit_id);
        }

        $user = $request->user();
        $clinicName = (string) ($queue->department ?: ($user->department ?? ''));
        $coverage = $queue->patient?->bpjs_card_no ? 'BPJS' : 'UMUM';

        // Use database transaction to ensure atomicity
        $visit = DB::transaction(function () use ($queue, $user, $clinicName, $coverage) {
            // Generate unique visit number using UUID-like approach
            $visitNumber = 'VIS' . now('Asia/Jakarta')->format('ymd') . strtoupper(Str::random(8));

            // Ensure uniqueness (extremely unlikely to collide with UUID-like random)
            $maxAttempts = 5;
            $attempts = 0;

            while ($attempts < $maxAttempts && Visit::where('visit_number', $visitNumber)->exists()) {
                $visitNumber = 'VIS' . now('Asia/Jakarta')->format('ymd') . strtoupper(Str::random(8));
                $attempts++;
            }

            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('Failed to generate unique visit number after multiple attempts.');
            }

            $visit = Visit::create([
                'patient_id' => $queue->patient_id,
                'provider_id' => $user?->id,
                'visit_number' => $visitNumber,
                'visit_datetime' => now('Asia/Jakarta'),
                'clinic_name' => $clinicName !== '' ? $clinicName : 'Poli',
                'coverage_type' => $coverage,
                'queue_number' => $queue->nomor_antrian,
                'status' => 'ONGOING',
                'meta' => [],
            ]);

            $queue->visit_id = $visit->id;
            $queue->status = QueueTicket::STATUS_CALLING;
            $queue->save();

            return $visit;
        });

        return redirect()
            ->route('emr.show', $visit)
            ->with('status', 'Mulai melayani pasien. Silakan isi rekam medis elektronik.');
    }
}
