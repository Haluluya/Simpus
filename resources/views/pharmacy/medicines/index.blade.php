<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-6">
        <div class="mx-auto max-w-7xl px-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#0F172A]">Manajemen Stok Obat</h1>
                    <p class="text-sm text-[#6B7280] mt-1">Kelola data master obat dan stok persediaan apotek</p>
                </div>
                <a href="{{ route('medicines.create') }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-[#2563EB] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1D4ED8] transition-colors shadow-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Obat Baru
                </a>
            </div>

            {{-- Search Box --}}
            <div class="mb-6">
                <form method="GET" action="{{ route('medicines.index') }}" class="flex gap-3">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Cari nama obat..." 
                            class="w-full h-12 rounded-xl border border-[#E2E8F0] px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                    </div>
                    <button type="submit" class="px-6 py-3 rounded-lg bg-[#2563EB] text-white font-semibold hover:bg-[#1D4ED8] transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
            <div class="mb-6 rounded-lg bg-[#D1FAE5] border border-[#10B981] p-4">
                <p class="text-sm font-medium text-[#065F46]">{{ session('success') }}</p>
            </div>
            @endif

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

            {{-- Medicines Table --}}
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Nama Obat
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Satuan
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Stok Tersedia
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Status Stok
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @forelse($medicines as $index => $medicine)
                            <tr class="hover:bg-[#F8FAFC] transition-colors">
                                <td class="px-4 py-4">
                                    <p class="text-sm font-semibold text-[#6B7280]">{{ $medicines->firstItem() + $index }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $medicine->nama_obat }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-1 text-xs font-medium text-[#374151]">
                                        {{ $medicine->satuan }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-lg font-bold text-[#0F172A]">{{ $medicine->stok }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    @if($medicine->stok > 50)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#D1FAE5] px-3 py-1 text-xs font-semibold text-[#16A34A]">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Aman
                                    </span>
                                    @elseif($medicine->stok > 20)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#FEF3C7] px-3 py-1 text-xs font-semibold text-[#F59E0B]">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Perlu Restok
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#FEE2E2] px-3 py-1 text-xs font-semibold text-[#DC2626]">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Stok Kritis!
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('medicines.edit', $medicine) }}" 
                                           class="inline-flex items-center gap-1 rounded-lg bg-[#2563EB] px-3 py-2 text-xs font-semibold text-white hover:bg-[#1D4ED8] transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('medicines.destroy', $medicine) }}" onsubmit="return confirm('Yakin ingin menghapus obat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 rounded-lg bg-white border border-[#DC2626] px-3 py-2 text-xs font-semibold text-[#DC2626] hover:bg-[#FEE2E2] transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="h-16 w-16 text-[#E2E8F0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 008 10.172V5L7 4z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-[#374151]">Belum ada data obat</p>
                                            <p class="text-xs text-[#6B7280] mt-1">Klik tombol "Tambah Obat Baru" untuk menambahkan stok obat</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($medicines->hasPages())
                <div class="border-t border-[#E2E8F0] px-6 py-4">
                    {{ $medicines->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
