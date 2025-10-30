<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LabOrder;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Referral;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchSuggestionController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', 'global');
        $limit = (int) $request->query('limit', 8);
        $limit = max(1, min($limit, 15));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'data' => [],
                'meta' => [
                    'count' => 0,
                    'type' => $type,
                    'query' => $query,
                ],
            ]);
        }

        $resolvers = [
            'global' => fn () => $this->globalSuggestions($request, $query, $limit),
            'patients' => fn () => $this->patientSuggestions($request, $query, $limit),
            'queues' => fn () => $this->queueSuggestions($request, $query, $limit),
            'visits' => fn () => $this->visitSuggestions($request, $query, $limit),
            'medicines' => fn () => $this->medicineSuggestions($request, $query, $limit),
            'referrals' => fn () => $this->referralSuggestions($request, $query, $limit),
            'lab_orders' => fn () => $this->labOrderSuggestions($request, $query, $limit),
            'users' => fn () => $this->userSuggestions($request, $query, $limit),
            'audit' => fn () => $this->auditSuggestions($request, $query, $limit),
        ];

        $resolver = $resolvers[$type] ?? $resolvers['global'];
        $suggestions = $resolver();

        return response()->json([
            'data' => $suggestions->values(),
            'meta' => [
                'count' => $suggestions->count(),
                'type' => $type,
                'query' => $query,
            ],
        ]);
    }

    private function globalSuggestions(Request $request, string $query, int $limit): Collection
    {
        $buckets = collect([
            $this->patientSuggestions($request, $query, min($limit, 4)),
            $this->queueSuggestions($request, $query, min($limit, 3)),
            $this->visitSuggestions($request, $query, min($limit, 3)),
        ]);

        if ($request->user()->can('medicine.view')) {
            $buckets->push($this->medicineSuggestions($request, $query, 2));
        }

        if ($request->user()->can('referral.view')) {
            $buckets->push($this->referralSuggestions($request, $query, 2));
        }

        if ($request->user()->can('lab.view')) {
            $buckets->push($this->labOrderSuggestions($request, $query, 2));
        }

        if ($request->user()->can('user.manage')) {
            $buckets->push($this->userSuggestions($request, $query, 2));
        }

        if ($request->user()->can('audit.view')) {
            $buckets->push($this->auditSuggestions($request, $query, 1));
        }

        return $buckets->flatten(1)->take($limit);
    }

    private function patientSuggestions(Request $request, string $query, int $limit): Collection
    {
        if ($request->user()->cannot('patient.view')) {
            return collect();
        }

        $patients = Patient::query()
            ->select(['id', 'name', 'medical_record_number', 'nik'])
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('medical_record_number', 'like', "%{$query}%")
                    ->orWhere('nik', 'like', "%{$query}%")
                    ->orWhere('bpjs_card_no', 'like', "%{$query}%");
            })
            ->orderByRaw('CASE WHEN medical_record_number LIKE ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END', ["{$query}%", "{$query}%"])
            ->limit($limit)
            ->get();

        return $patients->map(function (Patient $patient) {
            $description = collect([
                $patient->medical_record_number ? 'RM: '.$patient->medical_record_number : null,
                $patient->nik ? 'NIK: '.$patient->nik : null,
            ])->filter()->implode(' • ');

            return [
                'id' => $patient->id,
                'type' => 'patient',
                'label' => $patient->name,
                'value' => $patient->medical_record_number ?: ($patient->nik ?: $patient->name),
                'description' => $description,
                'url' => route('patients.show', $patient),
            ];
        });
    }

    private function queueSuggestions(Request $request, string $query, int $limit): Collection
    {
        if ($request->user()->cannot('queue.view')) {
            return collect();
        }

        $queues = QueueTicket::query()
            ->with(['patient:id,name,medical_record_number,nik'])
            ->where(function ($builder) use ($query) {
                $builder->where('nomor_antrian', 'like', "%{$query}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($query) {
                        $patientQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('medical_record_number', 'like', "%{$query}%")
                            ->orWhere('nik', 'like', "%{$query}%");
                    });
            })
            ->latest('tanggal_antrian')
            ->limit($limit)
            ->get();

        return $queues->map(function (QueueTicket $queue) use ($query) {
            $patient = $queue->patient;
            $description = collect([
                $patient?->name,
                $patient?->medical_record_number ? 'RM: '.$patient->medical_record_number : null,
                $queue->tanggal_antrian ? $queue->tanggal_antrian->format('d/m/Y') : null,
            ])->filter()->implode(' • ');

            return [
                'id' => $queue->id,
                'type' => 'queue',
                'label' => $queue->nomor_antrian ?? 'Antrian',
                'value' => $queue->nomor_antrian ?? $patient?->medical_record_number ?? $patient?->name ?? '',
                'description' => $description,
                'url' => route('queues.index', ['search' => $queue->nomor_antrian ?? $patient?->name ?? $query]),
            ];
        });
    }

    private function visitSuggestions(Request $request, string $query, int $limit): Collection
    {
        if ($request->user()->cannot('visit.view')) {
            return collect();
        }

        $visits = Visit::query()
            ->with(['patient:id,name,medical_record_number'])
            ->where(function ($builder) use ($query) {
                $builder->where('visit_number', 'like', "%{$query}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($query) {
                        $patientQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('medical_record_number', 'like', "%{$query}%");
                    });
            })
            ->latest('visit_datetime')
            ->limit($limit)
            ->get();

        return $visits->map(function (Visit $visit) {
            $patient = $visit->patient;
            $patientName = $patient?->name;
            $patientMrn = $patient?->medical_record_number;
            $description = collect([
                $patientName,
                $visit->clinic_name,
                $visit->coverage_type ? 'Pembiayaan: '.$visit->coverage_type : null,
            ])->filter()->implode(' • ');

            return [
                'id' => $visit->id,
                'type' => 'visit',
                'label' => $visit->visit_number ?? ($patientName ? 'Kunjungan '.$patientName : 'Kunjungan'),
                'value' => $visit->visit_number ?? $patientMrn ?? $patientName ?? '',
                'description' => $description,
                'url' => route('visits.show', $visit),
            ];
        });
    }

    private function medicineSuggestions(Request $request, string $query, int $limit): Collection
    {
        if ($request->user()->cannot('medicine.view')) {
            return collect();
        }

        $medicines = Medicine::query()
            ->select(['id', 'kode', 'nama', 'stok'])
            ->where(function ($builder) use ($query) {
                $builder->where('nama', 'like', "%{$query}%")
                    ->orWhere('kode', 'like', "%{$query}%");
            })
            ->orderByRaw('CASE WHEN kode LIKE ? THEN 0 WHEN nama LIKE ? THEN 1 ELSE 2 END', ["{$query}%", "{$query}%"])
            ->limit($limit)
            ->get();

        return $medicines->map(function (Medicine $medicine) {
            $description = collect([
                $medicine->kode,
                'Stok: '.$medicine->stok,
            ])->filter()->implode(' • ');

            return [
                'id' => $medicine->id,
                'type' => 'medicine',
                'label' => $medicine->nama,
                'value' => $medicine->kode ?? $medicine->nama,
                'description' => $description,
                'url' => route('medicines.index', ['search' => $medicine->nama]),
            ];
        });
    }

    private function referralSuggestions(Request $request, string $query, int $limit): Collection
    {
        if ($request->user()->cannot('referral.view')) {
            return collect();
        }

        $referrals = Referral::query()
            ->with(['patient:id,name,medical_record_number'])
            ->where(function ($builder) use ($query) {
                $builder->where('referral_number', 'like', "%{$query}%")
                    ->orWhere('referred_to', 'like', "%{$query}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($query) {
                        $patientQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('medical_record_number', 'like', "%{$query}%");
                    });
            })
            ->latest()
            ->limit($limit)
            ->get();

        return $referrals->map(function (Referral $referral) {
            $patient = $referral->patient;
            $description = collect([
                $patient?->name,
                $referral->referred_to,
                $referral->scheduled_at ? $referral->scheduled_at->format('d/m/Y') : null,
            ])->filter()->implode(' • ');

            return [
                'id' => $referral->id,
                'type' => 'referral',
                'label' => $referral->referral_number,
                'value' => $referral->referral_number,
                'description' => $description,
                'url' => route('referrals.index', ['search' => $referral->referral_number]),
            ];
        });
    }

    private function labOrderSuggestions(Request $request, string $query, int $limit): Collection
    {
        if ($request->user()->cannot('lab.view')) {
            return collect();
        }

        $orders = LabOrder::query()
            ->with(['visit.patient:id,name,medical_record_number'])
            ->where(function ($builder) use ($query) {
                $builder->where('order_number', 'like', "%{$query}%")
                    ->orWhereHas('visit.patient', function ($patientQuery) use ($query) {
                        $patientQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('medical_record_number', 'like', "%{$query}%");
                    });
            })
            ->latest('requested_at')
            ->limit($limit)
            ->get();

        return $orders->map(function (LabOrder $order) {
            $patient = $order->visit?->patient;
            $description = collect([
                $patient?->name,
                $order->requested_at ? $order->requested_at->format('d/m/Y') : null,
                $order->status,
            ])->filter()->implode(' • ');

            return [
                'id' => $order->id,
                'type' => 'lab_order',
                'label' => $order->order_number,
                'value' => $order->order_number,
                'description' => $description,
                'url' => route('lab-orders.index', ['search' => $order->order_number]),
            ];
        });
    }

    private function userSuggestions(Request $request, string $query, int $limit): Collection
    {
        if ($request->user()->cannot('user.manage')) {
            return collect();
        }

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('professional_identifier', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return $users->map(function (User $user) {
            $description = collect([
                $user->email,
                $user->professional_identifier ? 'NIP: '.$user->professional_identifier : null,
            ])->filter()->implode(' • ');

            return [
                'id' => $user->id,
                'type' => 'user',
                'label' => $user->name,
                'value' => $user->email ?? $user->name,
                'description' => $description,
                'url' => route('users.index', ['search' => $user->name]),
            ];
        });
    }

    private function auditSuggestions(Request $request, string $query, int $limit): Collection
    {
        if ($request->user()->cannot('audit.view')) {
            return collect();
        }

        $logs = AuditLog::query()
            ->select(['id', 'action', 'entity_type', 'entity_id', 'description', 'created_at'])
            ->where(function ($builder) use ($query) {
                $builder->where('action', 'like', "%{$query}%")
                    ->orWhere('entity_type', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('entity_id', 'like', "%{$query}%");
            })
            ->latest('created_at')
            ->limit($limit)
            ->get();

        return $logs->map(function (AuditLog $log) {
            $description = collect([
                $log->entity_type,
                $log->entity_id ? 'ID: '.$log->entity_id : null,
                $log->created_at ? $log->created_at->format('d/m/Y H:i') : null,
            ])->filter()->implode(' • ');

            return [
                'id' => $log->id,
                'type' => 'audit',
                'label' => strtoupper($log->action),
                'value' => $log->entity_type ?? $log->action,
                'description' => $description,
                'url' => route('audit.logs', ['search' => $log->entity_id ?: $log->action]),
            ];
        });
    }
}
