<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] px-6 py-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#1E293B]">Audit Trail</h1>
                    <p class="mt-1 text-sm text-[#64748B]">Riwayat aktivitas dan perubahan data sistem</p>
                </div>
                <a href="#" class="flex h-10 items-center gap-2 rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm font-semibold text-[#0F172A] shadow-sm transition hover:bg-[#F8FAFC]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ekspor Log
                </a>
            </div>

            <form method="GET" class="mb-6 rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Cari Aktivitas</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="User, IP, atau detail aktivitas..."
                    data-autocomplete="audit"
                    data-autocomplete-url="{{ route('search.suggestions') }}"
                    data-autocomplete-submit="true"
                    class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white pl-10 pr-4 text-sm text-[#0F172A] placeholder-[#94A3B8] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Aksi</label>
                        <select name="action" class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                            <option value="">Semua Aksi</option>
                            @foreach(['CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'VIEW'] as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Entitas</label>
                        <select name="entity_type" class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                            <option value="">Semua Entitas</option>
                            @foreach(['Rekam Medis', 'Kunjungan', 'User', 'Pasien', 'Lab Order', 'Rujukan', 'Obat'] as $entityType)
                                <option value="{{ $entityType }}" @selected(request('entity_type') === $entityType)>{{ $entityType }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Status</label>
                        <select name="status" class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                            <option value="">Semua Status</option>
                            <option value="success" @selected(request('status') === 'success')>Berhasil</option>
                            <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                    </div>
                </div>
                <div class="mt-4 flex gap-3">
                    <button type="submit" class="flex h-10 items-center gap-2 rounded-xl bg-[#2563EB] px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1D4ED8]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('audit.logs') }}" class="flex h-10 items-center gap-2 rounded-xl border border-[#CBD5E1] bg-white px-6 text-sm font-semibold text-[#0F172A] shadow-sm transition hover:bg-[#F8FAFC]">Reset</a>
                </div>
            </form>

            <div class="rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] px-6 py-4">
                    <h2 class="text-lg font-semibold text-[#1E293B]">Riwayat Aktivitas ({{ $logs->total() ?? 0 }} log)</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Timestamp</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">User</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Aksi</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Entitas</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Detail</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">IP Address</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0] bg-white">
                            @forelse($logs ?? [] as $log)
                                <tr class="transition hover:bg-[#F8FAFC]">
                                    <td class="px-6 py-4 text-sm text-[#64748B]">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-[#0F172A]">{{ $log->user->name ?? 'System' }}</td>
                                    <td class="px-6 py-4 text-sm text-[#64748B]">{{ $log->user?->roles?->first()?->name ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        @php $a=['CREATE'=>['l'=>'CREATE','bg'=>'#D1FAE5','tx'=>'#16A34A'],'UPDATE'=>['l'=>'UPDATE','bg'=>'#DBEAFE','tx'=>'#2563EB'],'DELETE'=>['l'=>'DELETE','bg'=>'#FEE2E2','tx'=>'#DC2626'],'LOGIN'=>['l'=>'LOGIN','bg'=>'#DBEAFE','tx'=>'#2563EB']]; $at=$a[$log->action]??['l'=>$log->action,'bg'=>'#F1F5F9','tx'=>'#64748B']; @endphp
                                        <span class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold" style="background-color:{{ $at['bg'] }};color:{{ $at['tx'] }}">{{ $at['l'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-[#0F172A]">{{ $log->entity_type ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-[#64748B]">{{ Str::limit($log->description ?? '-', 50) }}</td>
                                    <td class="px-6 py-4 text-sm font-mono text-[#64748B]">{{ $log->ip_address ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        @if($log->status === 'success')
                                            <span class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold" style="background-color:#D1FAE5;color:#16A34A">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Berhasil
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold" style="background-color:#FEE2E2;color:#DC2626">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                Gagal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <button onclick="showDetail({{ $log->id }})" class="text-[#2563EB] hover:text-[#1D4ED8] text-sm font-medium">Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-[#64748B]">Tidak ada log aktivitas ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($logs) && $logs->hasPages())
                    <div class="border-t border-[#E2E8F0] px-6 py-4">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

            <div class="mt-6 rounded-2xl border border-[#F59E0B] bg-[#FFFBEB] p-6">
                <div class="flex items-start gap-3">
                    <svg class="h-6 w-6 text-[#F59E0B] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-[#92400E] mb-2">Keamanan & Privasi Data</h3>
                        <p class="text-sm text-[#92400E] mb-3">Log audit disimpan minimal 5 tahun sesuai regulasi Kemenkes RI. Data terenkripsi dan hanya dapat diakses oleh administrator sistem.</p>
                        <ul class="space-y-2 text-sm text-[#92400E]">
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Semua aktivitas terekam dengan timestamp, user, dan IP address</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Log tidak dapat dihapus atau dimodifikasi setelah tercatat</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Backup otomatis dilakukan setiap hari untuk mencegah kehilangan data</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div id="detailModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-[#E2E8F0] px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-[#1E293B]">Detail Log Aktivitas</h3>
                <button onclick="closeModal()" class="text-[#64748B] hover:text-[#0F172A]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="p-6">
                <div class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-[#2563EB]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showDetail(logId) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('modalContent');
            
            modal.style.display = 'flex';
            content.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-[#2563EB]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;
            
            fetch(`/audit/logs/${logId}`)
                .then(response => response.json())
                .then(data => {
                    content.innerHTML = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">ID</label>
                                    <p class="text-sm text-[#0F172A]">#${data.id}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">Waktu</label>
                                    <p class="text-sm text-[#0F172A]">${data.performed_at}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">User</label>
                                    <p class="text-sm text-[#0F172A]">${data.user.name}</p>
                                    <p class="text-xs text-[#64748B]">${data.user.email}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">IP Address</label>
                                    <p class="text-sm font-mono text-[#0F172A]">${data.ip_address || '-'}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">Aksi</label>
                                    <p class="text-sm text-[#0F172A]">${data.action}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">Entitas</label>
                                    <p class="text-sm text-[#0F172A]">${data.entity_type || '-'}</p>
                                    ${data.entity_id ? `<p class="text-xs text-[#64748B]">ID: ${data.entity_id}</p>` : ''}
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">Deskripsi</label>
                                <p class="text-sm text-[#0F172A]">${data.description || '-'}</p>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">Status</label>
                                ${data.status === 'success' 
                                    ? '<span class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold" style="background-color:#D1FAE5;color:#16A34A"><svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Berhasil</span>'
                                    : '<span class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold" style="background-color:#FEE2E2;color:#DC2626"><svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg> Gagal</span>'
                                }
                            </div>
                            
                            ${data.error_message ? `
                                <div class="rounded-xl border border-[#FEE2E2] bg-[#FEF2F2] p-4">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#DC2626] mb-2">
                                        <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Pesan Error
                                    </label>
                                    <p class="text-sm text-[#991B1B] font-mono whitespace-pre-wrap">${data.error_message}</p>
                                </div>
                            ` : ''}
                            
                            ${data.user_agent ? `
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1">User Agent</label>
                                    <p class="text-xs text-[#64748B] font-mono break-all">${data.user_agent}</p>
                                </div>
                            ` : ''}
                            
                            ${data.old_values && Object.keys(data.old_values).length > 0 ? `
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-2">Nilai Sebelumnya</label>
                                    <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-3">
                                        <pre class="text-xs text-[#0F172A] overflow-x-auto">${JSON.stringify(data.old_values, null, 2)}</pre>
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${data.new_values && Object.keys(data.new_values).length > 0 ? `
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-2">Nilai Baru</label>
                                    <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-3">
                                        <pre class="text-xs text-[#0F172A] overflow-x-auto">${JSON.stringify(data.new_values, null, 2)}</pre>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                })
                .catch(error => {
                    content.innerHTML = `
                        <div class="rounded-xl border border-[#FEE2E2] bg-[#FEF2F2] p-4 text-center">
                            <svg class="mx-auto h-12 w-12 text-[#DC2626] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-[#DC2626]">Gagal memuat detail log</p>
                        </div>
                    `;
                });
        }
        
        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        document.getElementById('detailModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
