<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-6">
        <div class="mx-auto max-w-7xl px-6">
            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[#0F172A]">Antrean Kerja Lab</h1>
                <p class="text-sm text-[#6B7280] mt-1">Daftar permintaan tes laboratorium dari dokter (diurutkan berdasarkan waktu permintaan)</p>
            </div>

            {{-- Filter Status --}}
            <div class="mb-6 flex gap-3">
                <a href="{{ route('lab.index', ['status' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $currentStatus === 'all' ? 'bg-[#2563EB] text-white' : 'bg-white text-[#374151] border border-[#E2E8F0] hover:bg-[#F8FAFC]' }}">
                    Semua
                </a>
                <a href="{{ route('lab.index', ['status' => 'PENDING']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $currentStatus === 'PENDING' ? 'bg-[#F59E0B] text-white' : 'bg-white text-[#374151] border border-[#E2E8F0] hover:bg-[#F8FAFC]' }}">
                    Menunggu
                </a>
                <a href="{{ route('lab.index', ['status' => 'COMPLETED']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $currentStatus === 'COMPLETED' ? 'bg-[#16A34A] text-white' : 'bg-white text-[#374151] border border-[#E2E8F0] hover:bg-[#F8FAFC]' }}">
                    Selesai
                </a>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
            <div class="mb-6 rounded-lg bg-[#D1FAE5] border border-[#10B981] p-4">
                <p class="text-sm font-medium text-[#065F46]">{{ session('success') }}</p>
            </div>
            @endif

            {{-- Lab Orders Table --}}
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Waktu Permintaan
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Nama Pasien
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    No. RM
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Dokter Peminta
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Poli Asal
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Tes yang Diminta
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-[#6B7280] uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @forelse($labOrders as $order)
                            <tr class="hover:bg-[#F8FAFC] transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-[#0F172A]">{{ $order->requested_at->format('H:i') }}</p>
                                            <p class="text-xs text-[#6B7280]">{{ $order->requested_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div>
                                        <p class="text-sm font-semibold text-[#0F172A]">{{ $order->visit->patient->name }}</p>
                                        <p class="text-xs text-[#6B7280]">
                                            @if($order->visit->patient->gender === 'MALE') L 
                                            @elseif($order->visit->patient->gender === 'FEMALE') P
                                            @else - 
                                            @endif
                                            • 
                                            @if($order->visit->patient->date_of_birth)
                                                {{ \Carbon\Carbon::parse($order->visit->patient->date_of_birth)->age }} thn
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-mono font-semibold text-[#2563EB]">{{ $order->visit->patient->medical_record_number }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#DBEAFE] text-xs font-bold text-[#2563EB]">
                                            {{ substr($order->orderedByUser->name ?? 'N', 0, 2) }}
                                        </div>
                                        <p class="text-sm text-[#374151]">{{ $order->orderedByUser->name ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-1 text-xs font-medium text-[#374151]">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $order->visit->clinic_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="space-y-1">
                                        @if($order->items->count() > 0)
                                            @foreach($order->items as $item)
                                            <div class="flex items-center gap-2">
                                                @if($item->result)
                                                <svg class="h-4 w-4 text-[#16A34A] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                @else
                                                <svg class="h-4 w-4 text-[#F59E0B] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                @endif
                                                <span class="text-sm text-[#374151]">{{ $item->test_name }}</span>
                                            </div>
                                            @endforeach
                                        @else
                                            <span class="text-sm text-[#6B7280] italic">Belum ada item</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($order->status === 'PENDING')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#FEF3C7] px-3 py-1 text-xs font-semibold text-[#F59E0B]">
                                        <svg class="h-3 w-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Menunggu
                                    </span>
                                    @elseif($order->status === 'COMPLETED')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#D1FAE5] px-3 py-1 text-xs font-semibold text-[#16A34A]">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Selesai
                                    </span>
                                    @else
                                    <span class="inline-flex items-center rounded-full bg-[#E2E8F0] px-3 py-1 text-xs font-semibold text-[#6B7280]">
                                        {{ $order->status }}
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($order->status === 'PENDING')
                                        <a href="{{ route('lab.input-result', $order) }}" 
                                           class="inline-flex items-center gap-2 rounded-lg bg-[#2563EB] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1D4ED8] transition-colors shadow-sm">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Input Hasil
                                        </a>
                                        @else
                                        <a href="{{ route('lab.show', $order) }}" 
                                           class="inline-flex items-center gap-2 rounded-lg bg-white border border-[#E2E8F0] px-4 py-2 text-sm font-semibold text-[#374151] hover:bg-[#F8FAFC] transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat Hasil
                                        </a>
                                        <a href="{{ route('lab.print', $order) }}" 
                                           target="_blank"
                                           class="inline-flex items-center gap-2 rounded-lg bg-[#2563EB] border border-[#2563EB] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1D4ED8] transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            Cetak
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="h-16 w-16 text-[#E2E8F0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-[#374151]">Tidak ada permintaan lab</p>
                                            <p class="text-xs text-[#6B7280] mt-1">Belum ada permintaan tes dari dokter</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($labOrders->hasPages())
                <div class="border-t border-[#E2E8F0] px-6 py-4">
                    {{ $labOrders->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
