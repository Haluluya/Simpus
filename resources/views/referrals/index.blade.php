<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] px-6 py-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#1E293B]">Rujukan</h1>
                    <p class="mt-1 text-sm text-[#64748B]">Kelola rujukan pasien ke fasilitas kesehatan lanjutan</p>
                </div>
                @can('referral.create')
                    <a href="{{ route('referrals.create') }}" class="flex h-12 items-center gap-2 rounded-xl bg-[#2563EB] px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1D4ED8]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Rujukan Baru
                    </a>
                @endcan
            </div>

            <form method="GET" class="mb-6 rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Cari Rujukan</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="No. rujukan, nama pasien, atau no. RM..."
                                   data-autocomplete="referrals"
                                   data-autocomplete-url="{{ route('search.suggestions') }}"
                                   data-autocomplete-submit="true"
                                   class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white pl-10 pr-4 text-sm text-[#0F172A] placeholder-[#94A3B8] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                        </div>
                    </div>
                    <div><label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Status</label><select name="status" class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]"><option value="">Semua Status</option><option value="pending" @selected(request('status') === 'pending')>Menunggu</option><option value="approved" @selected(request('status') === 'approved')>Disetujui</option><option value="completed" @selected(request('status') === 'completed')>Selesai</option></select></div>
                    <div><label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Periode</label><input type="date" name="date" value="{{ request('date') }}" class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]"></div>
                </div>
                <div class="mt-4 flex items-center gap-3"><button type="submit" class="flex h-10 items-center gap-2 rounded-xl bg-[#2563EB] px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1D4ED8]"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>Terapkan Filter</button><a href="{{ route('referrals.index') }}" class="flex h-10 items-center gap-2 rounded-xl border border-[#CBD5E1] bg-white px-6 text-sm font-semibold text-[#0F172A] shadow-sm transition hover:bg-[#F8FAFC]">Reset</a></div>
            </form>

            <div class="rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] px-6 py-4"><h2 class="text-lg font-semibold text-[#1E293B]">Daftar Rujukan ({{ $referrals->total() ?? 0 }} rujukan)</h2></div>
                <div class="overflow-x-auto"><table class="w-full"><thead class="border-b border-[#E2E8F0] bg-[#F8FAFC]"><tr><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">No. Rujukan</th><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">No. RM</th><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Nama Pasien</th><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Tanggal</th><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Fasilitas Tujuan</th><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Departemen</th><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Diagnosis</th><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Status</th><th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Aksi</th></tr></thead><tbody class="divide-y divide-[#E2E8F0] bg-white">@forelse($referrals ?? [] as $referral)<tr class="transition hover:bg-[#F8FAFC]"><td class="px-6 py-4"><a href="{{ route('referrals.show', $referral) }}" class="font-semibold text-[#2563EB] hover:underline">{{ $referral->referral_number }}</a></td><td class="px-6 py-4 text-sm text-[#64748B]">{{ $referral->patient->medical_record_number ?? '-' }}</td><td class="px-6 py-4 text-sm font-medium text-[#0F172A]">{{ $referral->patient->name ?? '-' }}</td><td class="px-6 py-4 text-sm text-[#64748B]">{{ $referral->referral_date ? $referral->referral_date->format('d/m/Y') : '-' }}</td><td class="px-6 py-4 text-sm text-[#0F172A]">{{ $referral->destination_facility ?? '-' }}</td><td class="px-6 py-4 text-sm text-[#64748B]">{{ $referral->department ?? '-' }}</td><td class="px-6 py-4"><div class="max-w-xs truncate text-sm text-[#64748B]" title="{{ $referral->diagnosis }}">{{ $referral->diagnosis ?? '-' }}</div></td><td class="px-6 py-4">@php $s=['pending'=>['l'=>'Menunggu','b'=>'#FEF3C7','t'=>'#F59E0B','i'=>'clock'],'approved'=>['l'=>'Disetujui','b'=>'#DBEAFE','t'=>'#2563EB','i'=>'check'],'completed'=>['l'=>'Selesai','b'=>'#D1FAE5','t'=>'#16A34A','i'=>'check-circle']]; $st=$s[$referral->status]??['l'=>$referral->status,'b'=>'#F3F4F6','t'=>'#6B7280','i'=>'question']; @endphp<span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold" style="background-color:{{$st['b']}};color:{{$st['t']}}">@if($st['i']==='clock')<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>@elseif($st['i']==='check')<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>@elseif($st['i']==='check-circle')<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>@endif{{$st['l']}}</span></td><td class="px-6 py-4"><a href="{{ route('referrals.show', $referral) }}" class="text-[#2563EB] transition hover:text-[#1D4ED8]"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a></td></tr>@empty<tr><td colspan="9" class="px-6 py-12 text-center"><svg class="mx-auto h-12 w-12 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="mt-2 text-sm text-[#64748B]">Tidak ada data rujukan ditemukan</p></td></tr>@endforelse</tbody></table></div>
                @if(isset($referrals) && $referrals->hasPages())<div class="border-t border-[#E2E8F0] px-6 py-4">{{$referrals->links()}}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
