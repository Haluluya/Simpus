<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-6">
        <div class="mx-auto max-w-7xl px-6">
            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-[#0F172A]">BPJS VClaim</h1>
                <p class="mt-1 text-sm text-[#64748B]">Integrasi lengkap dengan BPJS VClaim REST API</p>
            </div>

            {{-- Status Connection --}}
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
                            <svg class="h-6 w-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-[#0F172A]">Status Koneksi BPJS VClaim</p>
                            <p class="text-sm text-[#64748B]">Mode: <span class="font-medium">{{ config('bpjs.use_mock') ? 'Sandbox/Mock' : 'Live Production' }}</span></p>
                            <p class="text-xs text-[#64748B]">Base URL: {{ config('bpjs.base_url') }}</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700">Aktif</span>
                </div>
            </div>

            {{-- Tabs --}}
            <div x-data="{ activeTab: 'peserta' }" class="space-y-6">
                <div class="flex gap-2 overflow-x-auto rounded-2xl border border-[#E2E8F0] bg-white p-2">
                    <button @click="activeTab = 'peserta'"
                            :class="activeTab === 'peserta' ? 'bg-[#2563EB] text-white' : 'text-[#64748B] hover:bg-[#F1F5F9]'"
                            class="rounded-xl px-6 py-3 text-sm font-semibold transition whitespace-nowrap">
                        Cek Peserta
                    </button>
                    <button @click="activeTab = 'sep'"
                            :class="activeTab === 'sep' ? 'bg-[#2563EB] text-white' : 'text-[#64748B] hover:bg-[#F1F5F9]'"
                            class="rounded-xl px-6 py-3 text-sm font-semibold transition whitespace-nowrap">
                        Kelola SEP
                    </button>
                    <button @click="activeTab = 'rujukan'"
                            :class="activeTab === 'rujukan' ? 'bg-[#2563EB] text-white' : 'text-[#64748B] hover:bg-[#F1F5F9]'"
                            class="rounded-xl px-6 py-3 text-sm font-semibold transition whitespace-nowrap">
                        Cek Rujukan
                    </button>
                    <button @click="activeTab = 'referensi'"
                            :class="activeTab === 'referensi' ? 'bg-[#2563EB] text-white' : 'text-[#64748B] hover:bg-[#F1F5F9]'"
                            class="rounded-xl px-6 py-3 text-sm font-semibold transition whitespace-nowrap">
                        Referensi
                    </button>
                    <button @click="activeTab = 'monitoring'"
                            :class="activeTab === 'monitoring' ? 'bg-[#2563EB] text-white' : 'text-[#64748B] hover:bg-[#F1F5F9]'"
                            class="rounded-xl px-6 py-3 text-sm font-semibold transition whitespace-nowrap">
                        Monitoring
                    </button>
                </div>

                {{-- Tab: Cek Peserta --}}
                <div x-show="activeTab === 'peserta'" x-cloak class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-[#0F172A]">Verifikasi Kepesertaan BPJS</h3>
                    
                    <form id="form-cek-peserta" class="space-y-4">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-[#374151]">Tipe Pencarian</label>
                                <select id="search-type" class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                                    <option value="nik">Berdasarkan NIK</option>
                                    <option value="kartu">Berdasarkan No. Kartu BPJS</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-[#374151]">Tanggal Pelayanan</label>
                                <input type="date" id="service-date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                            </div>
                        </div>

                        <div id="nik-field">
                            <label class="mb-2 block text-sm font-medium text-[#374151]">NIK (16 digit)</label>
                            <input type="text" id="nik" maxlength="16" placeholder="3201234567890123" class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                        </div>

                        <div id="kartu-field" style="display: none;">
                            <label class="mb-2 block text-sm font-medium text-[#374151]">No. Kartu BPJS (13 digit)</label>
                            <input type="text" id="no-kartu" maxlength="13" placeholder="0001234567890" class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                        </div>

                        <button type="submit" id="btn-cek-peserta" class="inline-flex items-center gap-2 rounded-lg bg-[#2563EB] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1D4ED8] transition-opacity disabled:opacity-60">
                            <svg class="h-5 w-5 btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="btn-text">Verifikasi Peserta</span>
                        </button>
                    </form>

                    <div id="result-peserta" class="mt-6 hidden">
                        <!-- Result akan ditampilkan di sini -->
                    </div>
                </div>

                {{-- Tab: SEP --}}
                <div x-show="activeTab === 'sep'" x-cloak class="space-y-6">
                    <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-[#0F172A]">Buat SEP Baru</h3>
                        
                        <form id="form-create-sep" class="space-y-4">
                            @csrf
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium">No. Kartu BPJS *</label>
                                    <input type="text" name="noKartu" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Tanggal SEP *</label>
                                    <input type="date" name="tglSep" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">No. Rujukan *</label>
                                    <input type="text" name="noRujukan" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Tanggal Rujukan *</label>
                                    <input type="date" name="tglRujukan" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">No. MR *</label>
                                    <input type="text" name="noMR" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Kode PPK *</label>
                                    <input type="text" name="ppkPelayanan" value="{{ config('satusehat.organization_id') }}" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Jenis Pelayanan *</label>
                                    <select name="jnsPelayanan" required class="w-full rounded-lg border-[#E2E8F0]">
                                        <option value="1">Rawat Inap</option>
                                        <option value="2" selected>Rawat Jalan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Kelas Rawat *</label>
                                    <select name="klsRawat" required class="w-full rounded-lg border-[#E2E8F0]">
                                        <option value="1">Kelas 1</option>
                                        <option value="2">Kelas 2</option>
                                        <option value="3" selected>Kelas 3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Asal Rujukan *</label>
                                    <select name="asalRujukan" required class="w-full rounded-lg border-[#E2E8F0]">
                                        <option value="1">Faskes 1</option>
                                        <option value="2">Faskes 2 (RS)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">PPK Rujukan *</label>
                                    <input type="text" name="ppkRujukan" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Kode Poli *</label>
                                    <input type="text" name="poli" placeholder="contoh: INT" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Diagnosa Awal *</label>
                                    <input type="text" name="diagAwal" placeholder="Kode ICD-10" required class="w-full rounded-lg border-[#E2E8F0]">
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-3">
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Tujuan Kunjungan *</label>
                                    <select name="tujuan" required class="w-full rounded-lg border-[#E2E8F0]">
                                        <option value="0">Normal</option>
                                        <option value="1">Prosedur</option>
                                        <option value="2">Konsul Dokter</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Eksekutif *</label>
                                    <select name="eksekutif" required class="w-full rounded-lg border-[#E2E8F0]">
                                        <option value="0" selected>Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">COB *</label>
                                    <select name="cob" required class="w-full rounded-lg border-[#E2E8F0]">
                                        <option value="0" selected>Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Katarak *</label>
                                    <select name="katarak" required class="w-full rounded-lg border-[#E2E8F0]">
                                        <option value="0" selected>Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">Laka Lantas *</label>
                                    <select name="lakaLantas" required class="w-full rounded-lg border-[#E2E8F0]">
                                        <option value="0" selected>Bukan Kecelakaan Lalu Lintas</option>
                                        <option value="1">KLL dan bukan kecelakaan kerja</option>
                                        <option value="2">KLL dan kecelakaan kerja</option>
                                        <option value="3">Kecelakaan kerja</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">Catatan</label>
                                <textarea name="catatan" rows="3" class="w-full rounded-lg border-[#E2E8F0]"></textarea>
                            </div>

                            <input type="hidden" name="user" value="{{ auth()->user()->name ?? 'system' }}">

                            <button type="submit" id="btn-create-sep" class="inline-flex items-center gap-2 rounded-lg bg-[#16A34A] px-6 py-3 text-sm font-semibold text-white hover:bg-[#15803D] transition-opacity disabled:opacity-60">
                                <svg class="h-5 w-5 btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="btn-text">Buat SEP</span>
                            </button>
                        </form>

                        <div id="result-sep" class="mt-6 hidden"></div>
                    </div>

                    {{-- Delete SEP --}}
                    <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-[#0F172A]">Hapus SEP</h3>
                        <form id="form-delete-sep" class="flex gap-4">
                            @csrf
                            <input type="text" name="noSep" placeholder="No. SEP" required class="flex-1 rounded-lg border-[#E2E8F0]">
                            <input type="hidden" name="user" value="{{ auth()->user()->name ?? 'system' }}">
                            <button type="submit" id="btn-delete-sep" class="inline-flex items-center gap-2 rounded-lg bg-[#DC2626] px-6 py-3 text-sm font-semibold text-white hover:bg-[#B91C1C] transition-opacity disabled:opacity-60">
                                <svg class="h-5 w-5 btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span class="btn-text">Hapus SEP</span>
                            </button>
                        </form>
                        <div id="result-delete-sep" class="mt-4 hidden"></div>
                    </div>
                </div>

                {{-- Tab: Rujukan --}}
                <div x-show="activeTab === 'rujukan'" x-cloak class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-[#0F172A]">Cek Rujukan</h3>
                    <form id="form-cek-rujukan" class="flex gap-4">
                        @csrf
                        <input type="text" name="no_rujukan" placeholder="No. Rujukan" required class="flex-1 rounded-lg border-[#E2E8F0]">
                        <button type="submit" id="btn-cek-rujukan" class="inline-flex items-center gap-2 rounded-lg bg-[#2563EB] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1D4ED8] transition-opacity disabled:opacity-60">
                            <svg class="h-5 w-5 btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span class="btn-text">Cari</span>
                        </button>
                    </form>
                    <div id="result-rujukan" class="mt-6 hidden"></div>
                </div>

                {{-- Tab: Referensi --}}
                <div x-show="activeTab === 'referensi'" x-cloak class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-[#0F172A]">Cari Diagnosa (ICD-10)</h3>
                        <form id="form-diagnosa" class="flex gap-4">
                            <input type="text" name="keyword" placeholder="Ketik minimal 3 karakter" required minlength="3" class="flex-1 rounded-lg border-[#E2E8F0]">
                            <button type="submit" class="rounded-lg bg-[#2563EB] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1D4ED8]">Cari</button>
                        </form>
                        <div id="result-diagnosa" class="mt-4 hidden"></div>
                    </div>

                    <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-[#0F172A]">Daftar Poliklinik</h3>
                        <form id="form-poli" class="flex gap-4">
                            <input type="text" name="keyword" placeholder="Cari poli (opsional)" class="flex-1 rounded-lg border-[#E2E8F0]">
                            <button type="submit" class="rounded-lg bg-[#2563EB] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1D4ED8]">Cari</button>
                        </form>
                        <div id="result-poli" class="mt-4 hidden"></div>
                    </div>
                </div>

                {{-- Tab: Monitoring --}}
                <div x-show="activeTab === 'monitoring'" x-cloak class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-[#0F172A]">Monitoring SEP</h3>
                    <form id="form-monitoring" class="grid gap-4 md:grid-cols-3">
                        @csrf
                        <div>
                            <label class="mb-2 block text-sm font-medium">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border-[#E2E8F0]">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border-[#E2E8F0]">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Jenis Pelayanan</label>
                            <select name="service_type" class="w-full rounded-lg border-[#E2E8F0]">
                                <option value="1">Rawat Inap</option>
                                <option value="2" selected>Rawat Jalan</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#2563EB] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1D4ED8]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Lihat Monitoring
                            </button>
                        </div>
                    </form>
                    <div id="result-monitoring" class="mt-6 hidden"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Helper function for button loading state
        function setButtonLoading(buttonId, loading, loadingText = 'Memproses...') {
            const button = document.getElementById(buttonId);
            if (!button) return;

            const icon = button.querySelector('.btn-icon');
            const text = button.querySelector('.btn-text');

            if (loading) {
                button.disabled = true;
                button.dataset.originalText = text?.textContent || '';
                button.dataset.originalIcon = icon?.outerHTML || '';
                if (icon) {
                    icon.outerHTML = '<svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                }
                if (text) text.textContent = loadingText;
            } else {
                button.disabled = false;
                const spinner = button.querySelector('.animate-spin');
                if (spinner && button.dataset.originalIcon) {
                    spinner.outerHTML = button.dataset.originalIcon;
                }
                if (text && button.dataset.originalText) {
                    text.textContent = button.dataset.originalText;
                }
            }
        }

        // Toggle NIK/Kartu field
        document.getElementById('search-type').addEventListener('change', function() {
            const nikField = document.getElementById('nik-field');
            const kartuField = document.getElementById('kartu-field');
            if (this.value === 'nik') {
                nikField.style.display = 'block';
                kartuField.style.display = 'none';
                document.getElementById('no-kartu').removeAttribute('required');
                document.getElementById('nik').setAttribute('required', 'required');
            } else {
                nikField.style.display = 'none';
                kartuField.style.display = 'block';
                document.getElementById('nik').removeAttribute('required');
                document.getElementById('no-kartu').setAttribute('required', 'required');
            }
        });

        // Form Cek Peserta
        document.getElementById('form-cek-peserta').addEventListener('submit', async function(e) {
            e.preventDefault();
            const searchType = document.getElementById('search-type').value;
            const serviceDate = document.getElementById('service-date').value;
            const resultDiv = document.getElementById('result-peserta');

            let url, data;
            if (searchType === 'nik') {
                url = '{{ route("bpjs.cek-peserta") }}';
                data = {
                    nik: document.getElementById('nik').value,
                    service_date: serviceDate
                };
            } else {
                url = '{{ route("bpjs.cek-peserta-kartu") }}';
                data = {
                    no_kartu: document.getElementById('no-kartu').value,
                    service_date: serviceDate
                };
            }

            // Show loading state
            setButtonLoading('btn-cek-peserta', true, 'Memverifikasi...');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                resultDiv.classList.remove('hidden');
                if (result.success) {
                    const peserta = result.data.response?.peserta;
                    const statusKeterangan = peserta?.statusPeserta?.keterangan || '-';
                    
                    // Determine styling based on status
                    let statusBgColor, statusTextColor, statusBorderColor, statusIcon, participantStatusText;
                    if (statusKeterangan === 'AKTIF') {
                        statusBgColor = 'bg-green-50';
                        statusTextColor = 'text-green-900';
                        statusBorderColor = 'border-green-200';
                        statusIcon = '✓';
                        participantStatusText = 'Peserta Aktif';
                    } else if (statusKeterangan === 'TIDAK AKTIF') {
                        statusBgColor = 'bg-red-50';
                        statusTextColor = 'text-red-900';
                        statusBorderColor = 'border-red-200';
                        statusIcon = '✗';
                        participantStatusText = 'Peserta Tidak Aktif';
                    } else {
                        statusBgColor = 'bg-gray-50';
                        statusTextColor = 'text-gray-900';
                        statusBorderColor = 'border-gray-200';
                        statusIcon = '•';
                        participantStatusText = 'Status Tidak Diketahui';
                    }
                    
                    resultDiv.innerHTML = `
                        <div class="rounded-lg border ${statusBorderColor} ${statusBgColor} p-4">
                            <div class="flex items-start gap-2">
                                <span class="font-semibold ${statusTextColor}">${statusIcon}</span>
                                <div class="flex-1">
                                    <h4 class="font-semibold ${statusTextColor}">${participantStatusText}</h4>
                                    <div class="mt-2 space-y-1 text-sm">
                                        <p><span class="font-medium">Nama:</span> ${peserta?.nama || '-'}</p>
                                        <p><span class="font-medium">No. Kartu:</span> ${peserta?.noKartu || '-'}</p>
                                        <p><span class="font-medium">NIK:</span> ${peserta?.nik || '-'}</p>
                                        <p><span class="font-medium">Hak Kelas:</span> ${peserta?.hakKelas?.keterangan || '-'}</p>
                                        <p><span class="font-medium">Status:</span> <span class="font-semibold">${peserta?.statusPeserta?.keterangan || '-'}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">${result.message}</div>`;
                }
            } catch (error) {
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = `<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">Error: ${error.message}</div>`;
            } finally {
                setButtonLoading('btn-cek-peserta', false);
            }
        });

        // Form Create SEP
        document.getElementById('form-create-sep').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            const resultDiv = document.getElementById('result-sep');

            setButtonLoading('btn-create-sep', true, 'Membuat SEP...');

            try {
                const response = await fetch('{{ route("bpjs.sep.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                resultDiv.classList.remove('hidden');
                if (result.success) {
                    const sep = result.data.response?.sep;
                    resultDiv.innerHTML = `
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <h4 class="font-semibold text-emerald-900">✓ SEP Berhasil Dibuat</h4>
                            <p class="mt-2"><span class="font-medium">No. SEP:</span> ${sep?.noSep || '-'}</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">${result.message}</div>`;
                }
            } catch (error) {
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = `<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">Error: ${error.message}</div>`;
            } finally {
                setButtonLoading('btn-create-sep', false);
            }
        });

        // Form Delete SEP
        document.getElementById('form-delete-sep').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!confirm('Yakin ingin menghapus SEP ini?')) return;
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('result-delete-sep');

            setButtonLoading('btn-delete-sep', true, 'Menghapus...');

            try {
                const response = await fetch('{{ route("bpjs.sep.delete") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                const result = await response.json();
                
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = result.success 
                    ? `<div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">✓ SEP berhasil dihapus</div>`
                    : `<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">${result.message}</div>`;
            } catch (error) {
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = `<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">Error: ${error.message}</div>`;
            } finally {
                setButtonLoading('btn-delete-sep', false);
            }
        });

        // Form Cek Rujukan
        document.getElementById('form-cek-rujukan').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const resultDiv = document.getElementById('result-rujukan');

            setButtonLoading('btn-cek-rujukan', true, 'Mencari...');

            try {
                const response = await fetch('{{ route("bpjs.rujukan.cek") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                const result = await response.json();
                
                resultDiv.classList.remove('hidden');
                if (result.success) {
                    const rujukan = result.data.response?.rujukan;
                    resultDiv.innerHTML = `
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <h4 class="font-semibold text-emerald-900">✓ Rujukan Ditemukan</h4>
                            <div class="mt-2 space-y-1 text-sm">
                                <p><span class="font-medium">No. Rujukan:</span> ${rujukan?.noKunjungan || '-'}</p>
                                <p><span class="font-medium">Peserta:</span> ${rujukan?.peserta?.nama || '-'}</p>
                                <p><span class="font-medium">Diagnosa:</span> ${rujukan?.diagnosa?.nama || '-'}</p>
                                <p><span class="font-medium">Tgl Rujukan:</span> ${rujukan?.tglKunjungan || '-'}</p>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">${result.message}</div>`;
                }
            } catch (error) {
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = `<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">Error: ${error.message}</div>`;
            } finally {
                setButtonLoading('btn-cek-rujukan', false);
            }
        });

        // Similar handlers for diagnosa, poli, and monitoring forms...
    </script>
    @endpush
</x-app-layout>
