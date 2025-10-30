<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QueueTicketController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['tanggal', 'status', 'search']);
        $tanggalInput = $filters['tanggal'] ?? null;
        $tanggal = $tanggalInput ? Carbon::parse($tanggalInput) : Carbon::today();

        $tickets = QueueTicket::query()
            ->with(['patient:id,name,medical_record_number'])
            ->whereDate('tanggal_antrian', $tanggal)
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('patient', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('medical_record_number', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                })->orWhere('nomor_antrian', 'like', "%{$search}%");
            })
            ->orderBy('nomor_antrian')
            ->paginate(20)
            ->withQueryString();

        $canCreate = $request->user()?->can('queue.create');
        $patients = $canCreate
            ? Patient::orderBy('name')->limit(50)->get(['id', 'name', 'medical_record_number'])
            : collect();

        return view('queues.index', [
            'tickets' => $tickets,
            'filters' => $filters + ['tanggal' => $tanggal->toDateString()],
            'statuses' => QueueTicket::statuses(),
            'patients' => $patients,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'tanggal_antrian' => ['required', 'date'],
            'nomor_antrian' => ['nullable', 'string', 'max:10'],
            'department' => ['nullable', 'string', 'max:100'],
        ]);

        $tanggal = Carbon::parse($data['tanggal_antrian'])->toDateString();
        $nomor = $data['nomor_antrian'] ?: QueueTicket::nextNumberForDate($tanggal);

        QueueTicket::create([
            'patient_id' => $data['patient_id'],
            'tanggal_antrian' => $tanggal,
            'nomor_antrian' => $nomor,
            'department' => $data['department'] ?? null,
            'status' => QueueTicket::STATUS_WAITING,
            'meta' => [
                'created_by' => Auth::id(),
            ],
        ]);

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
        $queue->loadMissing('patient');

        if ($queue->visit_id) {
            return redirect()->route('visits.show', $queue->visit_id);
        }

        // Generate nomor kunjungan sederhana (hindari duplikasi)
        do {
            $visitNumber = 'VIS' . now('Asia/Jakarta')->format('ymdHisv');
        } while (Visit::where('visit_number', $visitNumber)->exists());

        $user = $request->user();
        $clinicName = (string) ($user->department ?? '');
        $coverage = $queue->patient?->bpjs_card_no ? 'BPJS' : 'UMUM';

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

        return redirect()
            ->route('emr.show', $visit)
            ->with('status', 'Mulai melayani pasien. Silakan isi rekam medis elektronik.');
    }
}
