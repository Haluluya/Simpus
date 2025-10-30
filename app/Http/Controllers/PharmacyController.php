<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\MasterMedicine;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    /**
     * Dashboard Apotek - Antrean Resep Masuk
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'PENDING');
        
        $prescriptions = Prescription::query()
            ->with([
                'visit.patient',
                'doctor',
                'items.masterMedicine',
            ])
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('pharmacy.index', [
            'prescriptions' => $prescriptions,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Form Proses Resep (lihat detail & input hasil)
     */
    public function process(Prescription $prescription)
    {
        $prescription->load([
            'visit.patient',
            'doctor',
            'items.masterMedicine',
        ]);

        return view('pharmacy.process', [
            'prescription' => $prescription,
        ]);
    }

    /**
     * Serahkan Obat & Kurangi Stok
     */
    public function dispense(Request $request, Prescription $prescription)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:prescription_items,id'],
            'items.*.jumlah_diserahkan' => ['required', 'integer', 'min:0'],
            'catatan_apoteker' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        try {
            // Update setiap item & kurangi stok
            foreach ($data['items'] as $itemData) {
                $item = PrescriptionItem::find($itemData['id']);
                $medicine = $item->masterMedicine;

                // Cek stok mencukupi
                if ($medicine->stok < $itemData['jumlah_diserahkan']) {
                    return back()
                        ->withErrors(['error' => "Stok {$medicine->nama_obat} tidak mencukupi. Tersedia: {$medicine->stok} {$medicine->satuan}"])
                        ->withInput();
                }

                // Kurangi stok
                $medicine->decrement('stok', $itemData['jumlah_diserahkan']);

                // Update item (bisa tambah field jumlah_diserahkan di migration nanti)
                $item->update([
                    'jumlah' => $itemData['jumlah_diserahkan'], // for now
                ]);
            }

            // Update status resep
            $prescription->update([
                'status' => 'SELESAI',
                'catatan' => ($prescription->catatan ?? '') . "\n\nCatatan Apoteker: " . ($data['catatan_apoteker'] ?? ''),
            ]);

            Audit::log('prescription_dispensed', Prescription::class, $prescription->id, [
                'new' => ['status' => 'SELESAI'],
            ], ['user_id' => Auth::id()]);

            DB::commit();

            return redirect()
                ->route('pharmacy.index')
                ->with('success', 'Resep berhasil diserahkan dan stok obat telah dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Batalkan/Tolak Resep
     */
    public function cancel(Request $request, Prescription $prescription)
    {
        $data = $request->validate([
            'alasan' => ['required', 'string', 'max:500'],
        ]);

        $prescription->update([
            'status' => 'DIBATALKAN',
            'catatan' => ($prescription->catatan ?? '') . "\n\nDibatalkan: " . $data['alasan'],
        ]);

        Audit::log('prescription_cancelled', Prescription::class, $prescription->id, [
            'old' => ['status' => $prescription->getOriginal('status')],
            'new' => ['status' => 'DIBATALKAN'],
        ], ['user_id' => Auth::id()]);

        return redirect()
            ->route('pharmacy.index')
            ->with('success', 'Resep berhasil dibatalkan.');
    }
}
