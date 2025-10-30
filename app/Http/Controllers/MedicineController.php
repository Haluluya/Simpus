<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $medicines = Medicine::query()
            ->when($search, function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('drugs.index', [
            'medicines' => $medicines,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('medicines.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        Medicine::create($data);

        return redirect()
            ->route('medicines.index')
            ->with('status', __('Obat berhasil ditambahkan.'));
    }

    public function edit(Medicine $medicine)
    {
        return view('medicines.edit', [
            'medicine' => $medicine,
        ]);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $data = $this->validatedData($request, $medicine);

        $medicine->update($data);

        return redirect()
            ->route('medicines.index')
            ->with('status', __('Data obat berhasil diperbarui.'));
    }

    private function validatedData(Request $request, ?Medicine $medicine = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('medicines', 'kode')->ignore($medicine?->id)],
            'nama' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:30'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimal' => ['nullable', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);
    }
}
