<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-subtitle">Integrasi Layanan Nasional</p>
            <h1 class="page-title">Integrasi BPJS &amp; SATUSEHAT</h1>
        </div>
    </x-slot>

    <div x-data="{ tab: 'bpjs' }" class="space-y-8">
        <section class="rounded-3xl border border-[#E2E8F0] bg-white p-6 shadow-sm md:p-8">
            <div class="space-y-8">
                <div class="space-y-2">
                    <h2 class="text-xl font-semibold text-[#0F172A]">Integrasi BPJS &amp; SATUSEHAT</h2>
                    <p class="text-sm text-[#64748B]">Kelola integrasi dengan sistem eksternal untuk memastikan pelayanan tetap lancar.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex items-start justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100">
                                <svg class="h-6 w-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-[#0F172A]">BPJS VClaim</p>
                                <p class="text-xs text-[#64748B]">Koneksi ke server BPJS aktif dan stabil.</p>
                                <p class="text-xs text-[#64748B]">Mode: <span class="font-medium text-[#0F172A]">{{ $isBpjsMock ? 'Sandbox' : 'Live Production' }}</span></p>
                                <p class="text-xs text-[#64748B]">Terakhir sinkron: <span class="font-medium text-[#0F172A]">{{ $bpjsLastSync ?? __('Belum ada data') }}</span></p>
                            </div>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Online</span>
                    </div>

                    <div class="flex items-start justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100">
                                <svg class="h-6 w-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-[#0F172A]">SATUSEHAT</p>
                                <p class="text-xs text-[#64748B]">Terhubung dengan platform SATUSEHAT Kemenkes.</p>
                                <p class="text-xs text-[#64748B]">Mode: <span class="font-medium text-[#0F172A]">{{ $isSatuSehatMock ? 'Sandbox' : 'Live Production' }}</span></p>
                                <p class="text-xs text-[#64748B]">Terakhir sinkron: <span class="font-medium text-[#0F172A]">{{ $satusehatLastSync ?? __('Belum ada data') }}</span></p>
                            </div>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Online</span>
                    </div>
                </div>

                <div class="flex justify-start">
                    <div class="inline-flex rounded-full bg-[#F1F5F9] p-1">
                        <button type="button"
                                class="rounded-full px-4 py-2 text-sm font-semibold transition"
                                :class="tab === 'bpjs' ? 'bg-white text-[#2563EB] shadow-sm' : 'text-[#64748B]'"
                                @click="tab = 'bpjs'">
                            BPJS VClaim
                        </button>
                        <button type="button"
                                class="rounded-full px-4 py-2 text-sm font-semibold transition"
                                :class="tab === 'satusehat' ? 'bg-white text-[#2563EB] shadow-sm' : 'text-[#64748B]'"
                                @click="tab = 'satusehat'">
                            SATUSEHAT
                        </button>
                    </div>
                </div>

                <div class="space-y-8" x-cloak>
                    <div x-show="tab === 'bpjs'" class="space-y-6">
                        <div class="rounded-2xl border border-[#E2E8F0] bg-white/80 p-6 shadow-sm">
                            <div class="space-y-2">
                                <h3 class="text-base font-semibold text-[#0F172A]">Verifikasi Peserta BPJS</h3>
                                <p class="text-sm text-[#64748B]">Verifikasi kepesertaan dan hak eligibilitas pasien sebelum pelayanan.</p>
                            </div>
                            <form id="bpjs-form" class="mt-4 grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="bpjs-nik" class="text-xs font-semibold uppercase tracking-wide text-[#64748B]">No. Kartu BPJS</label>
                                    <input id="bpjs-nik" type="text" maxlength="16" required placeholder="0001234567890" class="h-12 rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 text-sm text-[#0F172A] focus:border-[#2563EB] focus:ring-[#2563EB]">
                                </div>
                                <div class="space-y-2">
                                    <label for="bpjs-service-date" class="text-xs font-semibold uppercase tracking-wide text-[#64748B]">Tanggal Pelayanan</label>
                                    <input id="bpjs-service-date" type="date" value="{{ now()->format('Y-m-d') }}" required class="h-12 rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 text-sm text-[#0F172A] focus:border-[#2563EB] focus:ring-[#2563EB]">
                                </div>
                                <div class="md:col-span-2 flex justify-start">
                                    <button type="submit" id="bpjs-submit" class="inline-flex items-center gap-2 rounded-2xl bg-[#2563EB] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/40">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Verifikasi Peserta
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div id="bpjs-status" class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-[#1E3A8A]">Hasil Verifikasi</p>
                                <span id="bpjs-status-badge" class="hidden rounded-full px-3 py-1 text-xs font-semibold"></span>
                            </div>
                            <div id="bpjs-status-body" class="mt-3 text-sm text-indigo-700">
                                Belum ada verifikasi yang dilakukan.
                            </div>
                        </div>
                    </div>

                    <div x-show="tab === 'satusehat'" class="space-y-6">
                        <div class="rounded-2xl border border-[#E2E8F0] bg-white/80 p-6 shadow-sm">
                            <div class="space-y-2">
                                <h3 class="text-base font-semibold text-[#0F172A]">Sinkron ke SATUSEHAT</h3>
                                <p class="text-sm text-[#64748B]">Antrikan data pasien ke platform SATUSEHAT. Proses dijalankan melalui background job.</p>
                            </div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <div class="md:col-span-2 space-y-2">
                                    <label for="satusehat-patient" class="text-xs font-semibold uppercase tracking-wide text-[#64748B]">Pilih Pasien</label>
                                    <select id="satusehat-patient" required aria-label="Pilih pasien untuk sinkron" class="h-12 rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] px-4 text-sm text-[#0F172A] focus:border-[#2563EB] focus:ring-[#2563EB]">
                                        <option value="">-- Pilih pasien --</option>
                                        @foreach ($recentPatients as $patient)
                                            <option value="{{ $patient->id }}">{{ $patient->name }} - RM {{ $patient->medical_record_number }} - NIK {{ $patient->nik }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="rounded-2xl bg-[#F8FAFC] px-4 py-3 text-xs text-[#64748B]">Pastikan data pasien sudah lengkap sebelum sinkronisasi. Sistem akan otomatis mengantrekan proses ini ke worker.</p>
                                </div>
                                <div class="md:col-span-2 flex justify-start">
                                    <button type="button" id="satusehat-submit" class="inline-flex items-center gap-2 rounded-2xl bg-[#16A34A] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#15803D] focus:outline-none focus:ring-2 focus:ring-[#16A34A]/40">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Sinkron Pasien
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="satusehat-status" class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p id="satusehat-status-text" class="text-sm text-slate-600">Menunggu permintaan sinkron.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-[#E2E8F0] bg-white p-6 shadow-sm md:p-8">
            <h3 class="text-base font-semibold text-[#0F172A]">Catatan Integrasi</h3>
            <ul class="mt-4 space-y-3 text-sm text-[#475569]">
                <li>Semua panggilan API dicatat pada modul Audit Log untuk kebutuhan jejak audit.</li>
                <li>Mode sandbox menggunakan file contoh pada direktori <code>storage/app/mocks</code>. Ubah <code>BPJS_USE_MOCK</code> atau <code>SATUSEHAT_USE_MOCK</code> pada file <code>.env</code> untuk beralih ke mode live.</li>
                <li>Sinkronisasi SATUSEHAT berjalan melalui antrean. Jalankan <code>php artisan queue:work</code> pada server untuk memproses permintaan.</li>
            </ul>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const bpjsForm = document.getElementById('bpjs-form');
                const bpjsStatusWrapper = document.getElementById('bpjs-status');
                const bpjsStatusBody = document.getElementById('bpjs-status-body');
                const bpjsStatusBadge = document.getElementById('bpjs-status-badge');
                const bpjsButton = document.getElementById('bpjs-submit');

                const satusehatSelect = document.getElementById('satusehat-patient');
                const satusehatStatusWrapper = document.getElementById('satusehat-status');
                const satusehatStatusText = document.getElementById('satusehat-status-text');
                const satusehatButton = document.getElementById('satusehat-submit');

                const toggleVariant = (element, variants, key) => {
                    if (!element || !variants[key]) {
                        return;
                    }

                    Object.values(variants).forEach(classes => element.classList.remove(...classes));
                    element.classList.add(...variants[key]);
                };

                const bpjsWrapperVariants = {
                    default: ['border-indigo-200', 'bg-indigo-50'],
                    success: ['border-emerald-200', 'bg-emerald-50'],
                    error: ['border-rose-200', 'bg-rose-50'],
                };

                const bpjsBodyVariants = {
                    default: ['text-indigo-700'],
                    success: ['text-emerald-700'],
                    error: ['text-rose-700'],
                };

                const satusehatWrapperVariants = {
                    default: ['border-slate-200', 'bg-white'],
                    success: ['border-emerald-200', 'bg-emerald-50'],
                    error: ['border-rose-200', 'bg-rose-50'],
                };

                const satusehatTextVariants = {
                    default: ['text-slate-600'],
                    success: ['text-emerald-700'],
                    error: ['text-rose-700'],
                };

                const resetBpjsBadge = () => {
                    if (!bpjsStatusBadge) {
                        return;
                    }

                    bpjsStatusBadge.classList.add('hidden');
                    bpjsStatusBadge.classList.remove('bg-emerald-100', 'text-emerald-700', 'bg-amber-100', 'text-amber-700', 'bg-blue-100', 'text-blue-700');
                };

                const showBpjsBadge = (text) => {
                    if (!bpjsStatusBadge) {
                        return;
                    }

                    const value = text?.trim();
                    if (!value) {
                        resetBpjsBadge();
                        return;
                    }

                    const normalized = value.toLowerCase();
                    bpjsStatusBadge.textContent = value;
                    bpjsStatusBadge.classList.remove('hidden');
                    bpjsStatusBadge.classList.remove('bg-emerald-100', 'text-emerald-700', 'bg-amber-100', 'text-amber-700', 'bg-blue-100', 'text-blue-700');

                    if (normalized.includes('aktif')) {
                        bpjsStatusBadge.classList.add('bg-emerald-100', 'text-emerald-700');
                    } else if (normalized.includes('non') || normalized.includes('tidak')) {
                        bpjsStatusBadge.classList.add('bg-amber-100', 'text-amber-700');
                    } else {
                        bpjsStatusBadge.classList.add('bg-blue-100', 'text-blue-700');
                    }
                };

                toggleVariant(bpjsStatusWrapper, bpjsWrapperVariants, 'default');
                toggleVariant(bpjsStatusBody, bpjsBodyVariants, 'default');
                toggleVariant(satusehatStatusWrapper, satusehatWrapperVariants, 'default');
                toggleVariant(satusehatStatusText, satusehatTextVariants, 'default');
                resetBpjsBadge();

                if (bpjsForm) {
                    bpjsForm.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        const nik = document.getElementById('bpjs-nik')?.value?.trim();
                        const serviceDate = document.getElementById('bpjs-service-date')?.value;

                        if (!nik || nik.length !== 16 || !serviceDate) {
                            toggleVariant(bpjsStatusWrapper, bpjsWrapperVariants, 'error');
                            toggleVariant(bpjsStatusBody, bpjsBodyVariants, 'error');
                            bpjsStatusBody.textContent = 'Masukkan NIK 16 digit dan tanggal pelayanan yang valid.';
                            resetBpjsBadge();
                            return;
                        }

                        bpjsButton.disabled = true;
                        toggleVariant(bpjsStatusWrapper, bpjsWrapperVariants, 'default');
                        toggleVariant(bpjsStatusBody, bpjsBodyVariants, 'default');
                        bpjsStatusBody.textContent = 'Memproses permintaan...';
                        resetBpjsBadge();

                        try {
                            const response = await fetch('{{ route('bpjs.cek-peserta') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({ nik, service_date: serviceDate }),
                            });

                            const result = await response.json();

                            if (result.success) {
                                const peserta = result.data?.response?.peserta ?? {};
                                toggleVariant(bpjsStatusWrapper, bpjsWrapperVariants, 'success');
                                toggleVariant(bpjsStatusBody, bpjsBodyVariants, 'success');

                                const statusKeterangan = peserta.statusPeserta?.keterangan ?? '';
                                const normalizedStatus = statusKeterangan.toLowerCase();
                                const badgeLabel = normalizedStatus.includes('aktif') ? 'Peserta Aktif' : (statusKeterangan || 'Status Tidak Diketahui');
                                showBpjsBadge(badgeLabel);

                                bpjsStatusBody.innerHTML = `
                                    <p class="text-sm font-semibold text-[#0F172A]">${peserta.nama ?? 'Peserta ditemukan'}</p>
                                    <div class="mt-2 grid gap-1 text-sm">
                                        <p>NIK: <span class="font-medium text-[#0F172A]">${peserta.nik ?? '-'}</span></p>
                                        <p>No. Kartu: <span class="font-medium text-[#0F172A]">${peserta.noKartu ?? '-'}</span></p>
                                        <p>Status: <span class="font-medium text-[#0F172A]">${statusKeterangan || '-'}</span></p>
                                        <p>Kelas: <span class="font-medium text-[#0F172A]">${peserta.hakKelas?.keterangan ?? '-'}</span></p>
                                    </div>
                                `;
                            } else {
                                toggleVariant(bpjsStatusWrapper, bpjsWrapperVariants, 'error');
                                toggleVariant(bpjsStatusBody, bpjsBodyVariants, 'error');
                                bpjsStatusBody.textContent = result.message ?? 'Verifikasi gagal. Periksa kembali data peserta.';
                                resetBpjsBadge();
                            }
                        } catch (error) {
                            toggleVariant(bpjsStatusWrapper, bpjsWrapperVariants, 'error');
                            toggleVariant(bpjsStatusBody, bpjsBodyVariants, 'error');
                            bpjsStatusBody.textContent = error.message ?? 'Terjadi kesalahan saat menghubungi layanan BPJS.';
                            resetBpjsBadge();
                        } finally {
                            bpjsButton.disabled = false;
                        }
                    });
                }

                if (satusehatButton) {
                    satusehatButton.addEventListener('click', async () => {
                        const patientId = satusehatSelect.value;
                        if (!patientId) {
                            toggleVariant(satusehatStatusWrapper, satusehatWrapperVariants, 'error');
                            toggleVariant(satusehatStatusText, satusehatTextVariants, 'error');
                            satusehatStatusText.textContent = 'Silakan pilih pasien terlebih dahulu.';
                            return;
                        }

                        satusehatButton.disabled = true;
                        toggleVariant(satusehatStatusWrapper, satusehatWrapperVariants, 'default');
                        toggleVariant(satusehatStatusText, satusehatTextVariants, 'default');
                        satusehatStatusText.textContent = 'Mengirim permintaan sinkron...';

                        try {
                            const response = await fetch(`{{ url('satusehat/patient') }}/${patientId}/sync`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                            });

                            const result = await response.json();

                            if (response.ok && result.success) {
                                toggleVariant(satusehatStatusWrapper, satusehatWrapperVariants, 'success');
                                toggleVariant(satusehatStatusText, satusehatTextVariants, 'success');
                                satusehatStatusText.textContent = result.message ?? 'Permintaan sinkron berhasil diantrikan.';
                            } else {
                                toggleVariant(satusehatStatusWrapper, satusehatWrapperVariants, 'error');
                                toggleVariant(satusehatStatusText, satusehatTextVariants, 'error');
                                satusehatStatusText.textContent = result.message ?? 'Sinkronisasi gagal dikirim.';
                            }
                        } catch (error) {
                            toggleVariant(satusehatStatusWrapper, satusehatWrapperVariants, 'error');
                            toggleVariant(satusehatStatusText, satusehatTextVariants, 'error');
                            satusehatStatusText.textContent = error.message ?? 'Terjadi kesalahan pada sinkronisasi.';
                        } finally {
                            satusehatButton.disabled = false;
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>



