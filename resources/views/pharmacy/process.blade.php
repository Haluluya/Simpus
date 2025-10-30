<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-6">
        <div class="mx-auto max-w-5xl px-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center gap-4">
                <a href="{{ route('pharmacy.index') }}" class="text-[#6B7280] hover:text-[#0F172A]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-[#0F172A]">Proses Resep</h1>
                    <p class="text-sm text-[#6B7280] mt-1">ID Resep: #{{ $prescription->id }}</p>
                </div>
                
                {{-- Status Badge --}}
                @if($prescription->status === 'PENDING')
                <span class="inline-flex items-center gap-2 rounded-full bg-[#FEF3C7] px-4 py-2 text-sm font-semibold text-[#F59E0B]">
                    <svg class="h-5 w-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Menunggu Diproses
                </span>
                @elseif($prescription->status === 'SELESAI')
                <span class="inline-flex items-center gap-2 rounded-full bg-[#D1FAE5] px-4 py-2 text-sm font-semibold text-[#16A34A]">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Sudah Diserahkan
                </span>
                @endif
            </div>

            {{-- Patient Info Card --}}
            <div class="mb-6 rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0F172A] mb-4">Informasi Pasien</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-[#6B7280]">Nama Pasien</p>
                        <p class="font-semibold text-[#0F172A]">{{ $prescription->visit->patient->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B7280]">No. Rekam Medis</p>
                        <p class="font-semibold text-[#2563EB]">{{ $prescription->visit->patient->medical_record_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B7280]">Jenis Kelamin / Usia</p>
                        <p class="font-semibold text-[#0F172A]">
                            @if($prescription->visit->patient->gender === 'MALE') Laki-laki
                            @elseif($prescription->visit->patient->gender === 'FEMALE') Perempuan
                            @else -
                            @endif
                            • 
                            @if($prescription->visit->patient->date_of_birth)
                                {{ \Carbon\Carbon::parse($prescription->visit->patient->date_of_birth)->age }} tahun
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B7280]">Dokter Peresep</p>
                        <p class="font-semibold text-[#0F172A]">{{ $prescription->doctor->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-[#E2E8F0]">
                    <p class="text-xs text-[#6B7280]">Waktu Resep Dibuat</p>
                    <p class="font-semibold text-[#0F172A]">{{ $prescription->created_at->format('d F Y, H:i') }}</p>
                </div>
                @if($prescription->catatan)
                <div class="mt-4 pt-4 border-t border-[#E2E8F0]">
                    <p class="text-xs text-[#6B7280]">Catatan Dokter</p>
                    <p class="text-sm text-[#0F172A] whitespace-pre-line">{{ $prescription->catatan }}</p>
                </div>
                @endif
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

            {{-- Form Serahkan Obat --}}
            @if($prescription->status === 'PENDING')
            <form method="POST" action="{{ route('pharmacy.dispense', $prescription) }}" class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm overflow-hidden">
                @csrf
                
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-base font-bold text-[#0F172A]">Daftar Obat yang Diminta</h3>
                    <p class="text-sm text-[#6B7280] mt-1">Pastikan stok mencukupi sebelum menyerahkan obat</p>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($prescription->items as $index => $item)
                    <div class="bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] p-5">
                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                        
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#2563EB] text-white font-bold flex-shrink-0">
                                {{ $index + 1 }}
                            </div>
                            
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-[#0F172A] text-lg">{{ $item->masterMedicine->nama_obat }}</h4>
                                        <p class="text-sm text-[#6B7280] mt-1">
                                            <span class="font-semibold">Stok Tersedia:</span> {{ $item->masterMedicine->stok }} {{ $item->masterMedicine->satuan }}
                                        </p>
                                    </div>
                                    
                                    @if($item->masterMedicine->stok >= $item->jumlah)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#D1FAE5] px-3 py-1 text-xs font-semibold text-[#16A34A]">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Stok Cukup
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#FEE2E2] px-3 py-1 text-xs font-semibold text-[#DC2626]">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Stok Kurang!
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-[#E2E8F0] grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-[#6B7280] mb-1">Jumlah Diminta</p>
                                        <p class="text-2xl font-bold text-[#0F172A]">{{ $item->jumlah }} <span class="text-base font-normal text-[#6B7280]">{{ $item->masterMedicine->satuan }}</span></p>
                                    </div>
                                    
                                    <div>
                                        <label for="jumlah_{{ $index }}" class="block text-xs text-[#6B7280] mb-2">
                                            Jumlah Diserahkan <span class="text-red-600">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            id="jumlah_{{ $index }}" 
                                            name="items[{{ $index }}][jumlah_diserahkan]" 
                                            required
                                            min="0"
                                            max="{{ $item->masterMedicine->stok }}"
                                            value="{{ old('items.'.$index.'.jumlah_diserahkan', $item->jumlah) }}"
                                            class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                                    </div>
                                    
                                    <div>
                                        <p class="text-xs text-[#6B7280] mb-1">Dosis</p>
                                        <p class="text-sm font-semibold text-[#0F172A]">{{ $item->dosis ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-[#6B7280]">Tidak ada item obat dalam resep ini</p>
                    </div>
                    @endforelse

                    @if($prescription->items->count() > 0)
                    <div class="mt-6 pt-6 border-t border-[#E2E8F0]">
                        <label for="catatan_apoteker" class="block text-sm font-medium text-[#374151] mb-2">
                            Catatan Apoteker (Opsional)
                        </label>
                        <textarea 
                            id="catatan_apoteker" 
                            name="catatan_apoteker" 
                            rows="3"
                            class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]"
                            placeholder="Tambahkan catatan khusus untuk pasien...">{{ old('catatan_apoteker') }}</textarea>
                    </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="border-t border-[#E2E8F0] p-6 bg-[#F8FAFC] flex items-center justify-between gap-3">
                    <a href="{{ route('pharmacy.index') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-white border-2 border-[#E2E8F0] px-6 py-3 text-sm font-semibold text-[#374151] hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    
                    <div class="flex gap-3">
                        <button 
                            type="button"
                            onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 rounded-lg bg-white border-2 border-[#DC2626] px-6 py-3 text-sm font-semibold text-[#DC2626] hover:bg-[#FEE2E2] transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batalkan Resep
                        </button>
                        
                        <button 
                            type="submit" 
                            class="inline-flex items-center gap-2 rounded-lg bg-[#16A34A] border-2 border-[#16A34A] px-8 py-3 text-sm font-semibold text-white hover:bg-[#15803D] transition-colors shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Serahkan Obat
                        </button>
                    </div>
                </div>
            </form>
            @else
            {{-- View Only Mode --}}
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm overflow-hidden">
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-base font-bold text-[#0F172A]">Riwayat Obat yang Diserahkan</h3>
                </div>

                <div class="p-6 space-y-4">
                    @foreach($prescription->items as $index => $item)
                    <div class="bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] p-5">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#16A34A] text-white font-bold flex-shrink-0">
                                ✓
                            </div>
                            
                            <div class="flex-1">
                                <h4 class="font-bold text-[#0F172A] text-lg">{{ $item->masterMedicine->nama_obat }}</h4>
                                <div class="mt-3 grid grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-[#6B7280]">Jumlah Diserahkan</p>
                                        <p class="text-lg font-bold text-[#0F172A]">{{ $item->jumlah }} {{ $item->masterMedicine->satuan }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-[#6B7280]">Dosis</p>
                                        <p class="text-sm font-semibold text-[#0F172A]">{{ $item->dosis ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-[#E2E8F0] p-6 bg-[#F8FAFC] flex items-center justify-between gap-3">
                    <a href="{{ route('pharmacy.index') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-white border-2 border-[#E2E8F0] px-6 py-3 text-sm font-semibold text-[#374151] hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    
                    <button 
                        onclick="window.print()" 
                        class="inline-flex items-center gap-2 rounded-lg bg-white border-2 border-[#2563EB] px-6 py-3 text-sm font-semibold text-[#2563EB] hover:bg-[#EEF2FF] transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Bukti
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Cancel Modal --}}
    <div id="cancelModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-[18px] p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-[#0F172A] mb-4">Batalkan Resep?</h3>
            
            <form method="POST" action="{{ route('pharmacy.cancel', $prescription) }}">
                @csrf
                <div class="mb-4">
                    <label for="alasan" class="block text-sm font-medium text-[#374151] mb-2">
                        Alasan Pembatalan <span class="text-red-600">*</span>
                    </label>
                    <textarea 
                        id="alasan" 
                        name="alasan" 
                        required
                        rows="3"
                        class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]"
                        placeholder="Contoh: Stok obat habis, Pasien menolak, dll"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button 
                        type="button"
                        onclick="document.getElementById('cancelModal').classList.add('hidden')"
                        class="flex-1 rounded-lg bg-white border-2 border-[#E2E8F0] px-4 py-2 text-sm font-semibold text-[#374151] hover:bg-gray-50">
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 rounded-lg bg-[#DC2626] px-4 py-2 text-sm font-semibold text-white hover:bg-[#B91C1C]">
                        Ya, Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
