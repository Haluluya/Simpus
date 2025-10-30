<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] px-6 py-8">
        <div class="mx-auto max-w-7xl">
            {{-- Success/Error Messages --}}
            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-xl border-l-4 border-[#16A34A] bg-[#D1FAE5] px-4 py-3 shadow-sm">
                <svg class="h-5 w-5 text-[#16A34A]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium text-[#16A34A]">{{ session('success') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-xl border-l-4 border-[#DC2626] bg-[#FEE2E2] px-4 py-3 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 text-[#DC2626]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-[#DC2626]">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#1E293B]">Kelola Pengguna</h1>
                    <p class="mt-1 text-sm text-[#64748B]">Manajemen user dan hak akses sistem</p>
                </div>
                <a href="{{ route('users.create') }}" class="flex h-10 items-center gap-2 rounded-xl bg-[#2563EB] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1D4ED8]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah User
                </a>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-[#64748B]">Total User</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ $totalUsers }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-[#64748B]">User Aktif</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ $activeUsers }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#D1FAE5]">
                            <svg class="h-6 w-6 text-[#16A34A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-[#64748B]">User Nonaktif</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ $inactiveUsers }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#FEE2E2]">
                            <svg class="h-6 w-6 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-[#64748B]">Login Hari Ini</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ $todayLogins }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] px-6 py-4">
                    <form method="GET" class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Cari User</label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Nama, email, atau NIP..."
                                   data-autocomplete="users"
                                   data-autocomplete-url="{{ route('search.suggestions') }}"
                                   data-autocomplete-submit="true"
                                   class="h-10 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                        </div>
                        <div class="w-48">
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Role</label>
                            <select name="role" class="h-10 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                                <option value="">Semua Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" @selected($roleFilter === $role->name)>{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex h-10 items-center gap-2 rounded-xl bg-[#2563EB] px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1D4ED8]">Cari</button>
                            <a href="{{ route('users.index') }}" class="flex h-10 items-center rounded-xl border border-[#CBD5E1] bg-white px-6 text-sm font-semibold text-[#0F172A] shadow-sm transition hover:bg-[#F8FAFC]">Reset</a>
                        </div>
                    </form>
                </div>
                <div class="p-6">
                    <p class="mb-4 text-sm font-medium text-[#1E293B]">Daftar Pengguna ({{ $users->total() }} user)</p>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">NIP</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Last Login</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] bg-white">
                                @forelse($users as $user)
                                    <tr class="transition hover:bg-[#F8FAFC]">
                                        <td class="px-4 py-4 text-sm font-medium text-[#0F172A]">{{ $user->name }}</td>
                                        <td class="px-4 py-4 text-sm text-[#64748B]">{{ $user->email }}</td>
                                        <td class="px-4 py-4 text-sm text-[#64748B]">{{ $user->nik ?? $user->professional_identifier ?? '-' }}</td>
                                        <td class="px-4 py-4">
                                            @forelse($user->roles as $role)
                                                @php
                                                    $colors = [
                                                        'dokter' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                                                        'petugas pendaftaran' => ['bg' => '#D1FAE5', 'text' => '#16A34A'],
                                                        'petugas laboratorium' => ['bg' => '#FEF3C7', 'text' => '#F59E0B'],
                                                        'admin' => ['bg' => '#FEE2E2', 'text' => '#DC2626'],
                                                    ];
                                                    $color = $colors[strtolower($role->name)] ?? ['bg' => '#F1F5F9', 'text' => '#64748B'];
                                                @endphp
                                                <span class="inline-block rounded-lg px-3 py-1 text-xs font-semibold" style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}">{{ $role->name }}</span>
                                            @empty
                                                <span class="text-xs text-[#94A3B8]">-</span>
                                            @endforelse
                                        </td>
                                        <td class="px-4 py-4 text-sm text-[#64748B]">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '-' }}</td>
                                        <td class="px-4 py-4">
                                            <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold transition-colors hover:opacity-80"
                                                        style="{{ $user->email_verified_at ? 'background-color: #D1FAE5; color: #16A34A' : 'background-color: #FEE2E2; color: #DC2626' }}">
                                                    @if($user->email_verified_at)
                                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Aktif
                                                    @else
                                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Nonaktif
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('users.edit', $user) }}" 
                                                   class="inline-flex items-center justify-center rounded-lg bg-[#2563EB] p-2 text-white transition-colors hover:bg-[#1D4ED8]"
                                                   title="Edit User">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('users.reset-password', $user) }}" 
                                                      class="inline"
                                                      onsubmit="return confirm('Reset password user {{ $user->name }} menjadi \'password123\'?')">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center gap-1 rounded-lg border-2 border-[#F59E0B] bg-white px-3 py-1.5 text-xs font-semibold text-[#F59E0B] transition-colors hover:bg-[#F59E0B] hover:text-white"
                                                            title="Reset Password">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                        </svg>
                                                        Reset
                                                    </button>
                                                </form>
                                                @if($user->id !== auth()->id())
                                                <form method="POST" 
                                                      action="{{ route('users.destroy', $user) }}" 
                                                      class="inline" 
                                                      onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center justify-center rounded-lg bg-[#DC2626] p-2 text-white transition-colors hover:bg-[#B91C1C]"
                                                            title="Hapus User">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-12 text-center">
                                            <svg class="mx-auto h-12 w-12 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <p class="mt-2 text-sm text-[#64748B]">Tidak ada user ditemukan</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($users->hasPages())
                        <div class="mt-4 border-t border-[#E2E8F0] pt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
