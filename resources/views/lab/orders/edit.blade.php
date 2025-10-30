<x-app-layout>
    {{-- Page Header --}}
    <div class="border-b border-[#E2E8F0] bg-white px-6 py-4">
        <h1 class="text-2xl font-bold text-[#0F172A]">Laboratorium</h1>
        <p class="mt-1 text-sm text-[#6B7280]">Kelola permintaan dan hasil pemeriksaan laboratorium</p>
    </div>

    <div class="flex min-h-screen bg-[#F8FAFC]">
        {{-- Left Panel - Daftar Permintaan Lab --}}
        <div class="w-2/5 border-r border-[#E2E8F0] bg-white">
            <div class="border-b border-[#E2E8F0] p-6">
                <h2 class="text-lg font-bold text-[#0F172A]">Daftar Permintaan Lab</h2>
                
                {{-- Search Box --}}
                <form method="GET" action="{{ route('lab-orders.index') }}" class="mt-4">
                    <input 
                        type="text" 
                        name="search" 
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
                       class="flex-1 border-b-2 border-transparent px-4 py-3 text-center text-sm font-semibold text-[#6B7280] transition-colors hover:text-[#0F172A]">
                        Semua
                    </a>
                    <a href="{{ route('lab-orders.index', ['status' => 'requested']) }}" 
                       class="flex-1 border-b-2 border-transparent px-4 py-3 text-center text-sm font-semibold text-[#6B7280] transition-colors hover:text-[#0F172A]">
                        Baru
                    </a>
                    <a href="{{ route('lab-orders.index', ['status' => 'in_progress']) }}" 
                       class="flex-1 border-b-2 border-transparent px-4 py-3 text-center text-sm font-semibold text-[#6B7280] transition-colors hover:text-[#0F172A]">
                        Proses
                    </a>
                    <a href="{{ route('lab-orders.index', ['status' => 'completed']) }}" 
                       class="flex-1 border-b-2 border-transparent px-4 py-3 text-center text-sm font-semibold text-[#6B7280] transition-colors hover:text-[#0F172A]">
                        Selesai
                    </a>
                </div>
            </div>

            {{-- Lab Orders List --}}
            <div class="overflow-y-auto p-4" style="max-height: calc(100vh - 240px);">
                <div class="space-y-3">
                    @php
                        $recentOrders = \App\Models\LabOrder::query()
                            ->with(['visit.patient', 'orderedByUser', 'items'])
                            ->latest('requested_at')
                            ->limit(20)
                            ->get();
                    @endphp
                    
                    @foreach($recentOrders as $order)
                        <a href="{{ route('lab-orders.edit', $order) }}" 
                           class="block rounded-xl border p-4 transition-all {{ $order->id === $labOrder->id ? 'border-[#2563EB] bg-[#EEF2FF]' : 'border-[#E2E8F0] bg-white hover:border-[#C7D2FE]' }}">
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
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Panel - Detail Pemeriksaan --}}
        <div class="flex-1 overflow-y-auto p-6">
            <div class="mx-auto max-w-3xl">
                <h2 class="mb-6 text-xl font-bold text-[#0F172A]">Detail Pemeriksaan</h2>
                
                <div class="rounded-[18px] border border-[#E2E8F0] bg-[#EEF2FF] p-6">
                    <p class="mb-2 text-sm font-semibold text-[#6B7280]">Informasi Pasien</p>
                    <p class="text-lg font-bold text-[#0F172A]">{{ $labOrder->visit->patient->name }}</p>
                    <p class="text-sm text-[#6B7280]">{{ $labOrder->visit->patient->medical_record_number }}</p>
                </div>

                <form method="POST" action="{{ route('lab-orders.update', $labOrder) }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-[#6B7280]">No. Order</p>
                                <p class="text-base font-bold text-[#0F172A]">{{ $labOrder->order_number }}</p>
                            </div>
                            @if($labOrder->status === 'REQUESTED')
                                <span class="inline-flex rounded-full bg-[#DBEAFE] px-4 py-2 text-sm font-semibold text-[#2563EB]">Permintaan Baru</span>
                            @elseif($labOrder->status === 'IN_PROGRESS')
                                <span class="inline-flex rounded-full bg-[#FEF3C7] px-4 py-2 text-sm font-semibold text-[#F59E0B]">Dalam Proses</span>
                            @elseif($labOrder->status === 'COMPLETED')
                                <span class="inline-flex rounded-full bg-[#D1FAE5] px-4 py-2 text-sm font-semibold text-[#16A34A]">Selesai</span>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-semibold text-[#6B7280]">Jenis Pemeriksaan</p>
                                <p class="text-[#0F172A]">{{ $labOrder->items->pluck('test_name')->join(', ') }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-[#6B7280]">Dokter Pengirim</p>
                                <p class="text-[#0F172A]">{{ $labOrder->orderedByUser?->name ?? 'Dr. Ahmad Rizki' }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p class="mb-2 font-semibold text-[#6B7280]">Tanggal</p>
                            <p class="text-[#0F172A]">{{ $labOrder->requested_at->translatedFormat('d/m/Y') }}</p>
                        </div>
                    </div>

                    <input type="hidden" name="verified_by" value="{{ Auth::id() }}">

                    <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-base font-bold text-[#0F172A]">INPUT HASIL</h3>
                        
                        @if($labOrder->status === 'COMPLETED')
                            {{-- Show Results (Read-only for completed orders) --}}
                            <div class="space-y-4">
                                @foreach($labOrder->items as $item)
                                    <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                        <div class="mb-3">
                                            <p class="font-semibold text-[#0F172A]">{{ $item->test_name }}</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Nilai</label>
                                                <div class="flex h-10 items-center rounded-lg border border-[#E2E8F0] bg-white px-3 text-sm">
                                                    <span class="flex-1 text-[#0F172A]">{{ $item->result ?: '-' }}</span>
                                                    <span class="text-[#6B7280]">{{ $item->unit }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Normal</label>
                                                <p class="flex h-10 items-center text-sm text-[#6B7280]">{{ $item->reference_range ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Input Form for non-completed orders --}}
                            <div class="space-y-4">
                                @foreach($labOrder->items as $index => $item)
                                    <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][test_name]" value="{{ $item->test_name }}">
                                        <input type="hidden" name="items[{{ $index }}][loinc_code]" value="{{ $item->loinc_code }}">
                                        <input type="hidden" name="items[{{ $index }}][specimen_type]" value="{{ $item->specimen_type }}">
                                        <input type="hidden" name="items[{{ $index }}][result_status]" value="FINAL">
                                        <input type="hidden" name="items[{{ $index }}][abnormal_flag]" value="N">
                                        
                                        <div class="mb-3 flex items-center justify-between">
                                            <p class="font-semibold text-[#0F172A]">{{ $item->test_name }}</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Nilai</label>
                                                <div class="relative">
                                                    <input 
                                                        type="text" 
                                                        name="items[{{ $index }}][result]"
                                                        value="{{ old("items.{$index}.result", $item->result) }}"
                                                        placeholder="" 
                                                        class="h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 pr-20 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#6B7280]">{{ $item->unit ?: 'g/dL' }}</span>
                                                    <input type="hidden" name="items[{{ $index }}][unit]" value="{{ $item->unit ?: 'g/dL' }}">
                                                </div>
                                                <x-input-error :messages="$errors->get("items.{$index}.result")" class="mt-1" />
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase text-[#6B7280]">Normal</label>
                                                <input 
                                                    type="text" 
                                                    name="items[{{ $index }}][reference_range]"
                                                    value="{{ old("items.{$index}.reference_range", $item->reference_range) }}"
                                                    placeholder="13.0-17.0"
                                                    class="h-10 w-full rounded-lg border border-[#E2E8F0] bg-white px-3 text-sm text-[#6B7280] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Status Selection --}}
                    <input type="hidden" id="status-input" name="status" value="{{ $labOrder->status }}">

                    {{-- Submit Buttons --}}
                    @if($labOrder->status === 'COMPLETED')
                        {{-- Buttons for Completed Orders --}}
                        <div class="space-y-3">
                            <a href="{{ route('lab.show', $labOrder) }}" 
                               class="flex h-12 w-full items-center justify-center gap-2 rounded-xl border-2 border-[#2563EB] bg-white px-6 text-sm font-semibold text-[#2563EB] hover:bg-[#EEF2FF] transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Hasil
                            </a>
                            <a href="{{ route('lab.print', $labOrder) }}" 
                               target="_blank"
                               class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#2563EB] px-6 text-sm font-semibold text-white hover:bg-[#1D4ED8] transition-colors shadow-sm">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Hasil
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            @if($labOrder->status === 'REQUESTED')
                                <button type="submit" onclick="document.getElementById('status-input').value='IN_PROGRESS'" class="h-12 w-full rounded-xl bg-[#F59E0B] px-6 text-sm font-semibold text-white hover:bg-[#D97706]">
                                    Mulai Pemeriksaan
                                </button>
                                <a href="{{ route('lab-orders.index') }}" class="flex h-12 items-center justify-center rounded-xl border border-[#E2E8F0] bg-white px-6 text-sm font-semibold text-[#6B7280] hover:bg-[#F8FAFC]">
                                    Kembali
                                </a>
                            @elseif($labOrder->status === 'IN_PROGRESS')
                                <button type="submit" onclick="document.getElementById('status-input').value='COMPLETED'" class="h-12 w-full rounded-xl bg-[#16A34A] px-6 text-sm font-semibold text-white hover:bg-[#15803D]">
                                    Selesai & Simpan
                                </button>
                                <button type="submit" onclick="document.getElementById('status-input').value='IN_PROGRESS'" class="h-12 w-full rounded-xl border border-[#2563EB] bg-white px-6 text-sm font-semibold text-[#2563EB] hover:bg-[#EEF2FF]">
                                    Simpan Draft
                                </button>
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
