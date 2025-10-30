<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-6">
        <div class="mx-auto max-w-3xl px-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center gap-4">
                <a href="{{ route('medicines.index') }}" class="text-[#6B7280] hover:text-[#0F172A]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-[#0F172A]">Edit Data Obat</h1>
                    <p class="text-sm text-[#6B7280] mt-1">Perbarui informasi obat: {{ $medicine->nama_obat }}</p>
                </div>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
            <div class="mb-6 rounded-lg bg-[#FEE2E2] border border-[#DC2626] p-4">
                <ul class="list-disc list-inside text-sm text-[#991B1B]">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('medicines.update', $medicine) }}" class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm overflow-hidden">
                @csrf
                @method('PUT')
                
                <div class="p-6 space-y-6">
                    {{-- Nama Obat --}}
                    <div>
                        <label for="nama_obat" class="block text-sm font-medium text-[#374151] mb-2">
                            Nama Obat <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama_obat" 
                            name="nama_obat" 
                            required
                            value="{{ old('nama_obat', $medicine->nama_obat) }}"
                            class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]"
                            placeholder="Contoh: Paracetamol 500mg">
                        <p class="text-xs text-[#6B7280] mt-1">Masukkan nama obat lengkap dengan dosis</p>
                    </div>

                    {{-- Satuan --}}
                    <div>
                        <label for="satuan" class="block text-sm font-medium text-[#374151] mb-2">
                            Satuan <span class="text-red-600">*</span>
                        </label>
                        <select 
                            id="satuan" 
                            name="satuan" 
                            required
                            class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                            <option value="">Pilih Satuan</option>
                            @php
                                $satuanOptions = ['Tablet', 'Kapsul', 'Kaplet', 'Botol', 'Tube', 'Strip', 'Box', 'Ampul', 'Vial', 'Sachet'];
                                $currentSatuan = old('satuan', $medicine->satuan);
                            @endphp
                            @foreach($satuanOptions as $option)
                                <option value="{{ $option }}" {{ $currentSatuan === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label for="stok" class="block text-sm font-medium text-[#374151] mb-2">
                            Stok Saat Ini <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="stok" 
                            name="stok" 
                            required
                            min="0"
                            value="{{ old('stok', $medicine->stok) }}"
                            class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]"
                            placeholder="0">
                        <p class="text-xs text-[#6B7280] mt-1">Perbarui jumlah stok yang tersedia</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="border-t border-[#E2E8F0] p-6 bg-[#F8FAFC] flex items-center justify-between gap-3">
                    <a href="{{ route('medicines.index') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-white border-2 border-[#E2E8F0] px-6 py-3 text-sm font-semibold text-[#374151] hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </a>
                    
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-2 rounded-lg bg-[#16A34A] border-2 border-[#16A34A] px-8 py-3 text-sm font-semibold text-white hover:bg-[#15803D] transition-colors shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Obat
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
