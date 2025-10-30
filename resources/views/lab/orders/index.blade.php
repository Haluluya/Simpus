<x-app-layout>
    {{-- Page Header --}}
    <div class="border-b border-[#E2E8F0] bg-white px-6 py-4">
        <h1 class="text-2xl font-bold text-[#0F172A]">Laboratorium</h1>
        <p class="mt-1 text-sm text-[#6B7280]">Kelola permintaan dan hasil pemeriksaan laboratorium</p>
    </div>

    <div class="flex min-h-screen bg-[#F8FAFC]" x-data="{ 
        selectedOrder: null,
        status: '{{ $status }}',
        selectOrder(order) {
            this.selectedOrder = order;
        }
    }">
        {{-- Left Panel - Daftar Permintaan Lab --}}
        <div class="w-2/5 border-r border-[#E2E8F0] bg-white">
            <div class="border-b border-[#E2E8F0] p-6">
                <h2 class="text-lg font-bold text-[#0F172A]">Daftar Permintaan Lab</h2>
                
                {{-- Search Box --}}
                <form method="GET" action="{{ route('lab-orders.index') }}" class="mt-4">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}"
                        placeholder="Cari..." 
                        data-autocomplete="lab_orders"
                        data-autocomplete-url="{{ route('search.suggestions') }}"
                        data-autocomplete-submit="true"
                        class="h-11 w-full rounded-xl border border-[#E2E8F0] px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                </form>
            </div>

            {{-- Tabs --}}
            <div class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                <div class="flex">
                    <a href="{{ route('lab-orders.index', ['status' => 'all']) }}" 
                       class="flex-1 border-b-2 px-4 py-3 text-center text-sm font-semibold transition-colors {{ $status === 'all' ? 'border-[#2563EB] text-[#2563EB]' : 'border-transparent text-[#6B7280] hover:text-[#0F172A]' }}">
                        Semua
                    </a>
                    <a href="{{ route('lab-orders.index', ['status' => 'requested']) }}" 
                       class="flex-1 border-b-2 px-4 py-3 text-center text-sm font-semibold transition-colors {{ $status === 'requested' ? 'border-[#2563EB] text-[#2563EB]' : 'border-transparent text-[#6B7280] hover:text-[#0F172A]' }}">
                        Baru
                    </a>
                    <a href="{{ route('lab-orders.index', ['status' => 'in_progress']) }}" 
                       class="flex-1 border-b-2 px-4 py-3 text-center text-sm font-semibold transition-colors {{ $status === 'in_progress' ? 'border-[#2563EB] text-[#2563EB]' : 'border-transparent text-[#6B7280] hover:text-[#0F172A]' }}">
                        Proses
                    </a>
                    <a href="{{ route('lab-orders.index', ['status' => 'completed']) }}" 
                       class="flex-1 border-b-2 px-4 py-3 text-center text-sm font-semibold transition-colors {{ $status === 'completed' ? 'border-[#2563EB] text-[#2563EB]' : 'border-transparent text-[#6B7280] hover:text-[#0F172A]' }}">
                        Selesai
                    </a>
                </div>
            </div>

            {{-- Lab Orders List --}}
            <div class="overflow-y-auto p-4" style="max-height: calc(100vh - 240px);">
                <div class="space-y-3">
                    @forelse($labOrders as $order)
                        <a href="{{ route('lab-orders.edit', $order) }}" 
                           class="block rounded-xl border p-4 transition-all {{ request()->route('labOrder')?->id === $order->id ? 'border-[#2563EB] bg-[#EEF2FF]' : 'border-[#E2E8F0] bg-white hover:border-[#C7D2FE]' }}">
                            <div class="mb-2 flex items-start justify-between">
                                <div>
                                    <p class="font-semibold text-[#2563EB]">{{ $order->order_number }}</p>
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $order->visit->patient->name }}</p>
                                    <p class="text-xs text-[#6B7280]">{{ $order->visit->patient->medical_record_number }} • Poli {{ $order->visit->clinic_name }}</p>
                                </div>
                                <div>
                                    @if($order->status === 'REQUESTED')
                                        <span class="inline-flex rounded-full bg-[#DBEAFE] px-3 py-1 text-xs font-semibold text-[#2563EB]">Permintaan Baru</span>
                                    @elseif($order->status === 'IN_PROGRESS')
                                        <span class="inline-flex rounded-full bg-[#FEF3C7] px-3 py-1 text-xs font-semibold text-[#F59E0B]">Dalam Proses</span>
                                    @elseif($order->status === 'COMPLETED')
                                        <span class="inline-flex rounded-full bg-[#D1FAE5] px-3 py-1 text-xs font-semibold text-[#16A34A]">Selesai</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mt-2 text-xs text-[#6B7280]">
                                <p class="font-semibold">Jenis Pemeriksaan</p>
                                <p>{{ $order->items->pluck('test_name')->take(2)->join(', ') }}@if($order->items->count() > 2), +{{ $order->items->count() - 2 }} lainnya @endif</p>
                            </div>
                            
                            <div class="mt-2 text-xs text-[#6B7280]">
                                {{ $order->requested_at->translatedFormat('d/m/Y') }} • {{ $order->orderedByUser?->name ?? 'Dr. Ahmad Rizki' }}
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-[#E2E8F0] py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <p class="mt-3 text-sm font-semibold text-[#6B7280]">Tidak ada permintaan lab</p>
                            <p class="text-xs text-[#94A3B8]">Belum ada data untuk status ini</p>
                        </div>
                    @endforelse
                </div>
                
                @if($labOrders->hasPages())
                    <div class="mt-4">
                        {{ $labOrders->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Panel - Detail Pemeriksaan --}}
        <div class="flex-1 overflow-y-auto p-6">
            <div class="mx-auto max-w-3xl">
                <h2 class="mb-6 text-xl font-bold text-[#0F172A]">Detail Pemeriksaan</h2>
                
                <div class="rounded-[18px] border border-[#E2E8F0] bg-[#EEF2FF] p-6">
                    <p class="mb-2 text-sm font-semibold text-[#6B7280]">Informasi Pasien</p>
                    <p class="text-lg font-bold text-[#0F172A]">Budi Santoso</p>
                    <p class="text-sm text-[#6B7280]">RM001234</p>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-[#6B7280]">No. Order</p>
                                <p class="text-base font-bold text-[#0F172A]">LAB20251029001</p>
                            </div>
                            <span class="inline-flex rounded-full bg-[#DBEAFE] px-4 py-2 text-sm font-semibold text-[#2563EB]">Permintaan Baru</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-semibold text-[#6B7280]">Jenis Pemeriksaan</p>
                                <p class="text-[#0F172A]">Darah Lengkap</p>
                            </div>
                            <div>
                                <p class="font-semibold text-[#6B7280]">Dokter Pengirim</p>
                                <p class="text-[#0F172A]">Dr. Ahmad Rizki</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p class="mb-2 font-semibold text-[#6B7280]">Tanggal</p>
                            <p class="text-[#0F172A]">29/10/2025</p>
                        </div>
                    </div>

                    <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-base font-bold text-[#0F172A]">INPUT HASIL</h3>
                        
                        <div class="space-y-4">
                            {{-- Hemoglobin --}}
                            <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="font-semibold text-[#0F172A]">Hemoglobin</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Nilai</label>
                                        <div class="relative">
                                            <input type="text" placeholder="" class="h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 pr-12 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#6B7280]">g/dL</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Normal</label>
                                        <p class="flex h-10 items-center text-sm text-[#6B7280]">13.0-17.0</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Leukosit --}}
                            <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="font-semibold text-[#0F172A]">Leukosit</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Nilai</label>
                                        <div class="relative">
                                            <input type="text" placeholder="" class="h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 pr-16 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#6B7280]">10^3/uL</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Normal</label>
                                        <p class="flex h-10 items-center text-sm text-[#6B7280]">4.0-10.0</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Eritrosit --}}
                            <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="font-semibold text-[#0F172A]">Eritrosit</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Nilai</label>
                                        <div class="relative">
                                            <input type="text" placeholder="" class="h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 pr-16 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#6B7280]">10^6/uL</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Normal</label>
                                        <p class="flex h-10 items-center text-sm text-[#6B7280]">4.5-5.5</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Trombosit --}}
                            <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="font-semibold text-[#0F172A]">Trombosit</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Nilai</label>
                                        <div class="relative">
                                            <input type="text" placeholder="" class="h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 pr-16 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#6B7280]">10^3/uL</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Normal</label>
                                        <p class="flex h-10 items-center text-sm text-[#6B7280]">150-400</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="text-center">
                        <button type="submit" class="h-12 w-full rounded-xl bg-[#F59E0B] px-6 text-sm font-semibold text-white hover:bg-[#D97706]">
                            Mulai Pemeriksaan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
