<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Rules\DoctorAvailableInDepartment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('tanggal', Carbon::now('Asia/Jakarta')->toDateString());
        $tanggal = Carbon::parse($selectedDate, 'Asia/Jakarta')->toDateString();
        $search = trim((string) $request->query('search'));

        // Use pagination to prevent loading all tickets at once for busy days
        $perPage = (int) $request->query('per_page', 50);
        $perPage = min(max($perPage, 10), 100); // Limit between 10-100

        $queueTickets = QueueTicket::query()
            ->with(['patient' => function ($query) {
                $query->select('id', 'name', 'medical_record_number', 'nik');
            }])
            ->whereDate('tanggal_antrian', $tanggal)
            ->orderBy('nomor_antrian')
            ->paginate($perPage)
            ->withQueryString();

        // Calculate stats from all tickets (not paginated) for accurate counts
        $allTicketsForStats = QueueTicket::query()
            ->whereDate('tanggal_antrian', $tanggal)
            ->select('status')
            ->get();

        $queueStats = [
            'total' => $allTicketsForStats->count(),
            'waiting' => $allTicketsForStats->where('status', QueueTicket::STATUS_WAITING)->count(),
            'called' => $allTicketsForStats->where('status', QueueTicket::STATUS_CALLING)->count(),
            'done' => $allTicketsForStats->where('status', QueueTicket::STATUS_DONE)->count(),
        ];

        $patients = Patient::query()
            ->select('id', 'name', 'medical_record_number', 'nik', 'bpjs_card_no')
            ->withCount('visits')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('medical_record_number', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('bpjs_card_no', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(25)
            ->get();

        // Daftar poli yang tersedia
        $poliOptions = config('queue_ticket.departments', [
            'Poli Umum',
            'Poli Gigi',
            'Poli KIA (Kesehatan Ibu dan Anak)',
            'Poli Anak',
            'Poli Penyakit Dalam',
            'Poli Bedah',
            'Poli Mata',
            'Poli THT',
            'Poli Kulit dan Kelamin',
            'Poli Saraf',
            'Poli Jantung',
            'Poli Paru',
            'Poli Jiwa',
            'Poli Rehabilitasi Medik',
        ]);

        $defaultDepartment = $poliOptions[0] ?? config('queue_ticket.default_department', 'Poli Umum');
        $selectedDepartment = $request->old('department', $defaultDepartment);

        if ($selectedDepartment && ! in_array($selectedDepartment, $poliOptions, true)) {
            $selectedDepartment = $defaultDepartment;
        }

        $queueNumberMap = collect($poliOptions)
            ->mapWithKeys(function (string $department) use ($tanggal) {
                return [$department => QueueTicket::nextNumberForDate($tanggal, $department)];
            })
            ->all();

        $initialQueueNumber = $selectedDepartment
            ? ($queueNumberMap[$selectedDepartment] ?? QueueTicket::nextNumberForDate($tanggal, $selectedDepartment))
            : QueueTicket::nextNumberForDate($tanggal, $defaultDepartment);

        // Daftar dokter per poli (from database with caching, fallback to config)
        $doctorsByPoli = Doctor::getByDepartment() ?: config('doctors.by_department', []);

        return view('registration.index', [
            'selectedDate' => $tanggal,
            'queueTickets' => $queueTickets,
            'queueStats' => $queueStats,
            'patients' => $patients,
            'search' => $search,
            'nextQueueNumber' => $initialQueueNumber,
            'poliOptions' => $poliOptions,
            'queueNumberMap' => $queueNumberMap,
            'selectedDepartment' => $selectedDepartment,
            'doctorsByPoli' => $doctorsByPoli,
        ]);
    }

    public function storeExistingQueue(Request $request)
    {
        $validDepartments = config('queue_ticket.departments', []);
        $validPaymentMethods = ['BPJS', 'UMUM'];

        $department = $request->input('department');

        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'tanggal_antrian' => ['required', 'date', 'after_or_equal:today'],
            'nomor_antrian' => ['nullable', 'string', 'max:10'],
            'department' => ['required', 'string', 'max:100', Rule::in($validDepartments)],
            'doctor' => ['nullable', 'string', 'max:100', new DoctorAvailableInDepartment($department)],
            'payment_method' => ['nullable', 'string', 'max:50', Rule::in($validPaymentMethods)],
        ]);

        $tanggal = Carbon::parse($data['tanggal_antrian'])->toDateString();

        // Use transaction because nextNumberForDate uses lockForUpdate
        DB::transaction(function () use ($data, $tanggal) {
            $nomor = $data['nomor_antrian'] ?: QueueTicket::nextNumberForDate($tanggal, $data['department']);

            QueueTicket::create([
                'patient_id' => $data['patient_id'],
                'tanggal_antrian' => $tanggal,
                'nomor_antrian' => $nomor,
                'department' => $data['department'],
                'doctor' => $data['doctor'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'status' => QueueTicket::STATUS_WAITING,
                'meta' => [
                    'created_by' => Auth::id(),
                    'source' => 'registration',
                ],
            ]);
        });

        return redirect()
            ->route('registrations.index', ['tanggal' => $tanggal])
            ->with('status', 'Nomor antrian berhasil dibuat.');
    }

    public function printSep(Patient $patient)
    {
        // Generate nomor SEP (dummy untuk contoh)
        $sepNumber = 'SEP/' . now()->format('Ymd') . '/' . str_pad($patient->id, 6, '0', STR_PAD_LEFT);

        return view('registration.sep-print', [
            'patient' => $patient,
            'sepNumber' => $sepNumber,
            'printDate' => now('Asia/Jakarta'),
            'facilityName' => config('app.name', 'SIMPUS'),
        ]);
    }

}
