<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabOrderResult;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabController extends Controller
{
    /**
     * Menampilkan Lab Work Queue (Antrean Kerja Lab)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $labOrders = LabOrder::query()
            ->with([
                'visit.patient',
                'orderedByUser',
                'items',
                'results',
            ])
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest('requested_at')
            ->paginate(20);

        return view('lab.index', [
            'labOrders' => $labOrders,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Menampilkan form input hasil untuk order tertentu
     */
    public function inputResult(LabOrder $labOrder)
    {
        $labOrder->load([
            'visit.patient',
            'orderedByUser',
            'items',
        ]);

        return view('lab.input-result', [
            'labOrder' => $labOrder,
        ]);
    }

    /**
     * Menampilkan detail hasil lab order
     */
    public function show(LabOrder $labOrder)
    {
        $labOrder->load([
            'visit.patient',
            'orderedByUser',
            'verifiedByUser',
            'items',
        ]);

        return view('lab.show', [
            'labOrder' => $labOrder,
        ]);
    }

    /**
     * Simpan hasil pemeriksaan lab
     */
    public function storeResult(Request $request, LabOrder $labOrder)
    {
        $data = $request->validate([
            'results' => ['required', 'array'],
            'results.*.id' => ['required', 'exists:lab_order_items,id'],
            'results.*.result' => ['required', 'string'],
            'results.*.unit' => ['nullable', 'string'],
            'results.*.reference_range' => ['nullable', 'string'],
            'results.*.abnormal_flag' => ['nullable', 'in:NORMAL,HIGH,LOW,CRITICAL'],
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['results'] as $resultData) {
                $item = LabOrderItem::find($resultData['id']);
                $item->update([
                    'result' => $resultData['result'],
                    'unit' => $resultData['unit'] ?? null,
                    'reference_range' => $resultData['reference_range'] ?? null,
                    'abnormal_flag' => $resultData['abnormal_flag'] ?? 'NORMAL',
                    'result_status' => 'FINAL',
                    'resulted_at' => now(),
                ]);
            }

            // Update status lab order
            $labOrder->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
            ]);

            Audit::log('lab_result_completed', LabOrder::class, $labOrder->id, [
                'new' => ['status' => 'COMPLETED'],
            ], ['user_id' => Auth::id()]);

            DB::commit();

            return redirect()
                ->route('lab.index')
                ->with('success', 'Hasil pemeriksaan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Cetak hasil pemeriksaan lab
     */
    public function print(LabOrder $labOrder)
    {
        // Hanya bisa cetak jika sudah COMPLETED
        if ($labOrder->status !== 'COMPLETED') {
            return redirect()
                ->route('lab.show', $labOrder)
                ->with('error', 'Hasil lab belum lengkap, tidak dapat dicetak.');
        }

        $labOrder->load([
            'visit.patient',
            'orderedByUser',
            'verifiedByUser',
            'items',
        ]);

        return view('lab.print', [
            'labOrder' => $labOrder,
        ]);
    }
}
