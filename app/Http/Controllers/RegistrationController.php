<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\QueueTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('tanggal', Carbon::now('Asia/Jakarta')->toDateString());
        $tanggal = Carbon::parse($selectedDate)->toDateString();
        $search = trim((string) $request->query('search'));

        $queueTickets = QueueTicket::query()
            ->with(['patient' => function ($query) {
                $query->select('id', 'name', 'medical_record_number', 'nik');
            }])
            ->whereDate('tanggal_antrian', $tanggal)
            ->orderBy('nomor_antrian')
            ->get();

        $queueStats = [
            'total' => $queueTickets->count(),
            'waiting' => $queueTickets->where('status', QueueTicket::STATUS_WAITING)->count(),
            'called' => $queueTickets->where('status', QueueTicket::STATUS_CALLING)->count(),
            'done' => $queueTickets->where('status', QueueTicket::STATUS_DONE)->count(),
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
        $poliOptions = [
            'Umum' => 'Poli Umum',
            'Gigi' => 'Poli Gigi',
            'KIA' => 'Poli KIA (Kesehatan Ibu dan Anak)',
            'Anak' => 'Poli Anak',
            'Penyakit Dalam' => 'Poli Penyakit Dalam',
            'Bedah' => 'Poli Bedah',
            'Mata' => 'Poli Mata',
            'THT' => 'Poli THT',
            'Kulit dan Kelamin' => 'Poli Kulit dan Kelamin',
            'Saraf' => 'Poli Saraf',
            'Jantung' => 'Poli Jantung',
            'Paru' => 'Poli Paru',
            'Jiwa' => 'Poli Jiwa',
            'Rehabilitasi Medik' => 'Poli Rehabilitasi Medik',
        ];

        return view('registration.index', [
            'selectedDate' => $tanggal,
            'queueTickets' => $queueTickets,
            'queueStats' => $queueStats,
            'patients' => $patients,
            'search' => $search,
            'nextQueueNumber' => QueueTicket::nextNumberForDate($tanggal),
            'poliOptions' => $poliOptions,
        ]);
    }

    public function storeExistingQueue(Request $request)
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
            'department' => $data['department'] ?? 'Poli Umum',
            'status' => QueueTicket::STATUS_WAITING,
            'meta' => [
                'created_by' => Auth::id(),
                'source' => 'registration',
            ],
        ]);

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
