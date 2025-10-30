<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\Visit;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LabOrderController extends Controller
{
    /**
     * Display a listing of lab orders.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));

        $query = LabOrder::query()
            ->with(['visit.patient', 'orderedByUser', 'items'])
            ->latest('requested_at');

        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('visit.patient', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('medical_record_number', 'like', "%{$search}%");
                  });
            });
        }

        $labOrders = $query->paginate(20);

        return view('lab.orders.index', [
            'labOrders' => $labOrders,
            'status' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Display the order creation form.
     */
    public function create(Request $request)
    {
        $visitId = $request->integer('visit_id');
        $visit = $visitId ? Visit::with('patient')->find($visitId) : null;
        $recentVisits = Visit::with('patient:id,name,medical_record_number')
            ->latest('visit_datetime')
            ->limit(20)
            ->get(['id', 'patient_id', 'visit_number', 'visit_datetime', 'clinic_name']);

        $defaultPanels = [
            'Hematologi Lengkap',
            'Kimia Darah',
            'Urinalisa',
            'Lipid Panel',
            'Gula Darah Puasa',
        ];

        return view('lab.orders.create', [
            'visit' => $visit,
            'recentVisits' => $recentVisits,
            'defaultPanels' => $defaultPanels,
        ]);
    }

    /**
     * Store a newly created lab order.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'visit_id' => ['required', 'exists:visits,id'],
            'clinical_notes' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(['ROUTINE', 'STAT'])],
            'tests' => ['required', 'array', 'min:1'],
            'tests.*.test_name' => ['required', 'string', 'max:255'],
            'tests.*.loinc_code' => ['nullable', 'string', 'max:20'],
            'tests.*.specimen_type' => ['nullable', 'string', 'max:100'],
        ]);

        $visit = Visit::findOrFail($data['visit_id']);

        $labOrder = DB::transaction(function () use ($data, $visit) {
            $labOrder = LabOrder::create([
                'visit_id' => $visit->id,
                'ordered_by' => Auth::id(),
                'order_number' => $this->generateOrderNumber(),
                'status' => 'REQUESTED',
                'priority' => $data['priority'],
                'requested_at' => now(),
                'clinical_notes' => $data['clinical_notes'] ?? null,
                'meta' => [],
            ]);

            foreach ($data['tests'] as $test) {
                $labOrder->items()->create([
                    'test_name' => $test['test_name'],
                    'loinc_code' => $test['loinc_code'] ?? null,
                    'specimen_type' => $test['specimen_type'] ?? null,
                    'result_status' => 'PRELIMINARY',
                ]);
            }

            return $labOrder->load('items');
        });

        Audit::log('lab_order_created', LabOrder::class, $labOrder->id, [
            'new' => [
                'order' => $labOrder->only([
                    'visit_id',
                    'order_number',
                    'status',
                    'priority',
                    'requested_at',
                    'clinical_notes',
                ]),
                'items' => $labOrder->items->map(fn ($item) => $item->only([
                    'test_name',
                    'loinc_code',
                    'specimen_type',
                    'result_status',
                ]))->all(),
            ],
        ], [
            'visit_id' => $visit->id,
        ]);

        return redirect()
            ->route('visits.show', $visit)
            ->with('status', 'Order laboratorium berhasil dibuat.');
    }

    /**
     * Form entry for inputting results.
     */
    public function edit(LabOrder $labOrder)
    {
        $labOrder->load(['visit.patient', 'items']);

        return view('lab.orders.edit', [
            'labOrder' => $labOrder,
        ]);
    }

    /**
     * Update lab order results.
     */
    public function update(Request $request, LabOrder $labOrder)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['REQUESTED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])],
            'verified_by' => ['nullable', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'exists:lab_order_items,id'],
            'items.*.test_name' => ['required', 'string', 'max:255'],
            'items.*.loinc_code' => ['nullable', 'string', 'max:20'],
            'items.*.specimen_type' => ['nullable', 'string', 'max:100'],
            'items.*.result' => ['nullable', 'string'],
            'items.*.unit' => ['nullable', 'string', 'max:20'],
            'items.*.reference_range' => ['nullable', 'string', 'max:255'],
            'items.*.abnormal_flag' => ['nullable', Rule::in(['-', 'N', 'L', 'H', 'A'])],
            'items.*.result_status' => ['required', Rule::in(['PRELIMINARY', 'FINAL', 'AMENDED', 'CANCELLED'])],
            'items.*.observed_at' => ['nullable', 'date'],
            'items.*.resulted_at' => ['nullable', 'date'],
        ]);

        $labOrder->load('items');

        $orderSnapshot = [
            'status' => $labOrder->status,
            'verified_by' => $labOrder->verified_by,
            'completed_at' => $labOrder->completed_at,
        ];

        $itemSnapshot = $labOrder->items
            ->mapWithKeys(fn ($item) => [
                $item->id => $this->itemAuditPayload($item),
            ])->all();

        DB::transaction(function () use ($labOrder, $data) {
            $labOrder->status = $data['status'];
            $labOrder->verified_by = $data['verified_by'] ?? Auth::id();
            $labOrder->completed_at = $data['status'] === 'COMPLETED' ? now() : null;
            $labOrder->save();

            $existingItems = $labOrder->items->keyBy('id');

            foreach ($data['items'] as $itemData) {
                $itemId = $itemData['id'] ?? null;

                if ($itemId && $existingItems->has($itemId)) {
                    $existingItems->get($itemId)->update($this->mapItemData($itemData));
                } else {
                    $labOrder->items()->create($this->mapItemData($itemData));
                }
            }
        });

        $labOrder->load('items');

        $orderChanges = $this->calculateOrderChanges($orderSnapshot, [
            'status' => $labOrder->status,
            'verified_by' => $labOrder->verified_by,
            'completed_at' => $labOrder->completed_at,
        ]);

        $itemChanges = $this->calculateItemChanges($itemSnapshot, $labOrder->items->mapWithKeys(fn ($item) => [
            $item->id => $this->itemAuditPayload($item),
        ])->all());

        $changesPayload = array_filter([
            'order' => $orderChanges,
            'items' => $itemChanges,
        ], fn ($value) => ! empty($value));

        if (! empty($changesPayload)) {
            Audit::log('lab_order_updated', LabOrder::class, $labOrder->id, $changesPayload, [
                'visit_id' => $labOrder->visit_id,
                'status' => $labOrder->status,
            ]);
        }

        return redirect()
            ->route('visits.show', $labOrder->visit_id)
            ->with('status', 'Hasil laboratorium berhasil diperbarui.');
    }

    private function mapItemData(array $item): array
    {
        return [
            'test_name' => $item['test_name'],
            'loinc_code' => $item['loinc_code'] ?? null,
            'specimen_type' => $item['specimen_type'] ?? null,
            'result' => $item['result'] ?? null,
            'unit' => $item['unit'] ?? null,
            'reference_range' => $item['reference_range'] ?? null,
            'abnormal_flag' => $item['abnormal_flag'] ?? '-',
            'result_status' => $item['result_status'],
            'observed_at' => $item['observed_at'] ?? null,
            'resulted_at' => $item['resulted_at'] ?? now(),
        ];
    }

    private function itemAuditPayload($item): array
    {
        return [
            'test_name' => $item->test_name,
            'loinc_code' => $item->loinc_code,
            'specimen_type' => $item->specimen_type,
            'result' => $item->result,
            'unit' => $item->unit,
            'reference_range' => $item->reference_range,
            'abnormal_flag' => $item->abnormal_flag,
            'result_status' => $item->result_status,
            'observed_at' => $item->observed_at ? $item->observed_at->toDateTimeString() : null,
            'resulted_at' => $item->resulted_at ? $item->resulted_at->toDateTimeString() : null,
        ];
    }

    private function calculateOrderChanges(array $before, array $after): array
    {
        $changes = [];

        foreach ($after as $key => $newValue) {
            $oldValue = $before[$key] ?? null;

            if ($oldValue instanceof \DateTimeInterface) {
                $oldValue = $oldValue->toDateTimeString();
            }

            if ($newValue instanceof \DateTimeInterface) {
                $newValue = $newValue->toDateTimeString();
            }

            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    private function calculateItemChanges(array $before, array $after): array
    {
        $changes = [];

        foreach ($after as $itemId => $itemData) {
            if (! array_key_exists($itemId, $before)) {
                $changes[] = [
                    'id' => $itemId,
                    'action' => 'created',
                    'new' => $itemData,
                ];
                continue;
            }

            $diff = [];
            foreach ($itemData as $key => $value) {
                $oldValue = $before[$itemId][$key] ?? null;
                if ($oldValue !== $value) {
                    $diff[$key] = [
                        'old' => $oldValue,
                        'new' => $value,
                    ];
                }
            }

            if (! empty($diff)) {
                $changes[] = [
                    'id' => $itemId,
                    'changes' => $diff,
                ];
            }
        }

        foreach (array_diff_key($before, $after) as $itemId => $itemData) {
            $changes[] = [
                'id' => $itemId,
                'action' => 'deleted',
                'old' => $itemData,
            ];
        }

        return $changes;
    }

    private function generateOrderNumber(): string
    {
        do {
            $candidate = 'LAB'.now('Asia/Jakarta')->format('ymdHis');
        } while (LabOrder::where('order_number', $candidate)->exists());

        return $candidate;
    }
}
