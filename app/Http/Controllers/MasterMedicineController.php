<?php

namespace App\Http\Controllers;

use App\Models\MasterMedicine;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterMedicineController extends Controller
{
    /**
     * Daftar Stok Obat
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $medicines = MasterMedicine::query()
            ->when($search, function ($q, $search) {
                $q->where('nama_obat', 'like', "%{$search}%");
            })
            ->orderBy('nama_obat')
            ->paginate(50);

        return view('pharmacy.medicines.index', [
            'medicines' => $medicines,
        ]);
    }

    /**
     * Form Tambah Obat Baru
     */
    public function create()
    {
        return view('pharmacy.medicines.create');
    }

    /**
     * Simpan Obat Baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50'],
            'stok' => ['required', 'integer', 'min:0'],
        ]);

        $medicine = MasterMedicine::create($data);

        Audit::log('medicine_created', MasterMedicine::class, $medicine->id, [
            'new' => $data,
        ], ['user_id' => Auth::id()]);

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Obat berhasil ditambahkan.');
    }

    /**
     * Form Edit Obat
     */
    public function edit(MasterMedicine $medicine)
    {
        return view('pharmacy.medicines.edit', [
            'medicine' => $medicine,
        ]);
    }

    /**
     * Update Obat
     */
    public function update(Request $request, MasterMedicine $medicine)
    {
        $data = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50'],
            'stok' => ['required', 'integer', 'min:0'],
        ]);

        $old = $medicine->toArray();
        $medicine->update($data);

        Audit::log('medicine_updated', MasterMedicine::class, $medicine->id, [
            'old' => $old,
            'new' => $data,
        ], ['user_id' => Auth::id()]);

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Data obat berhasil diperbarui.');
    }

    /**
     * Hapus Obat
     */
    public function destroy(MasterMedicine $medicine)
    {
        // Cek apakah obat pernah digunakan di resep
        if ($medicine->prescriptionItems()->exists()) {
            return back()
                ->withErrors(['error' => 'Obat tidak dapat dihapus karena sudah pernah digunakan dalam resep.']);
        }

        $old = $medicine->toArray();
        $medicine->delete();

        Audit::log('medicine_deleted', MasterMedicine::class, $medicine->id, [
            'old' => $old,
        ], ['user_id' => Auth::id()]);

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Obat berhasil dihapus.');
    }
}
