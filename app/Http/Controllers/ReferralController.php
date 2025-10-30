<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralStatusRequest;
use App\Models\Patient;
use App\Models\Referral;
use App\Models\Visit;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search', 'date_from', 'date_to']);

        $referrals = Referral::query()
            ->with(['patient:id,name,medical_record_number', 'visit:id,visit_number,clinic_name,visit_datetime'])
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('medical_record_number', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                })->orWhere('referral_number', 'like', "%{$search}%")
                    ->orWhere('referred_to', 'like', "%{$search}%");
            })
            ->when($filters['date_from'] ?? null, function ($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($filters['date_to'] ?? null, function ($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('referrals.index', [
            'referrals' => $referrals,
            'filters' => $filters,
            'statuses' => Referral::statuses(),
        ]);
    }

    public function create(Request $request)
    {
        $selectedPatientId = $request->integer('patient_id');
        $selectedVisitId = $request->integer('visit_id');

        $patients = Patient::query()
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'medical_record_number', 'nik']);

        $visits = Visit::query()
            ->with(['patient:id,name'])
            ->latest('visit_datetime')
            ->limit(30)
            ->get(['id', 'patient_id', 'visit_number', 'clinic_name', 'visit_datetime']);

        return view('referrals.create', [
            'patients' => $patients,
            'visits' => $visits,
            'selectedPatientId' => $selectedPatientId,
            'selectedVisitId' => $selectedVisitId,
            'statuses' => Referral::statuses(),
        ]);
    }

    public function store(StoreReferralRequest $request)
    {
        $data = $request->validated();

        $referral = Referral::create([
            'patient_id' => $data['patient_id'],
            'visit_id' => $data['visit_id'] ?? null,
            'created_by' => Auth::id(),
            'referral_number' => $this->generateReferralNumber(),
            'referred_to' => $data['referred_to'],
            'referred_department' => $data['referred_department'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'status' => $data['status'],
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'meta' => [],
        ]);

        if ($referral->status !== Referral::STATUS_PENDING) {
            $referral->sent_at = now();
            $referral->save();
        }

        Audit::log('referral_created', Referral::class, $referral->id, [
            'new' => $referral->only(['referral_number', 'patient_id', 'referred_to', 'status']),
        ]);

        return redirect()
            ->route('referrals.show', $referral)
            ->with('status', __('Referral created successfully.'));
    }

    public function show(Referral $referral)
    {
        $referral->load(['patient', 'visit', 'creator:id,name']);

        return view('referrals.show', [
            'referral' => $referral,
            'statuses' => Referral::statuses(),
        ]);
    }

    public function update(UpdateReferralStatusRequest $request, Referral $referral)
    {
        $data = $request->validated();
        $originalStatus = $referral->status;

        $referral->fill([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? $referral->notes,
        ]);

        if (in_array($data['status'], [Referral::STATUS_SENT, Referral::STATUS_COMPLETED], true)) {
            $referral->sent_at = $referral->sent_at ?? now();
        }

        if ($data['status'] === Referral::STATUS_COMPLETED) {
            $referral->responded_at = $data['responded_at'] ?? now();
        } elseif ($data['responded_at'] ?? null) {
            $referral->responded_at = $data['responded_at'];
        }

        $referral->save();

        Audit::log('referral_updated', Referral::class, $referral->id, [
            'status' => [
                'old' => $originalStatus,
                'new' => $referral->status,
            ],
        ]);

        return redirect()
            ->route('referrals.show', $referral)
            ->with('status', __('Referral status updated.'));
    }

    private function generateReferralNumber(): string
    {
        do {
            $number = 'REF'.Str::upper(Str::random(8));
        } while (Referral::where('referral_number', $number)->exists());

        return $number;
    }
}
