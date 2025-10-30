<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIMPUS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>[x-cloak]{display:none!important;}</style>
</head>
<body>
@php
    $menuItems = [
        ['label' => 'Beranda', 'route' => 'dashboard', 'icon' => 'chart-bar', 'abilities' => []],
        ['label' => 'Integrasi', 'route' => 'integration.index', 'icon' => 'chart-bar', 'abilities' => ['bpjs.verify', 'satusehat.sync']],
        ['label' => 'Kelola Order Lab', 'route' => 'lab-orders.index', 'icon' => 'beaker', 'abilities' => ['lab.view']],
        ['label' => 'Pendaftaran & Antrian', 'route' => 'registrations.index', 'icon' => 'queue-list', 'abilities' => ['patient.create', 'queue.create']],
        ['label' => 'Data Pasien', 'route' => 'patients.index', 'icon' => 'users', 'abilities' => ['patient.view']],
        ['label' => 'Kunjungan & RME', 'route' => 'visits.index', 'icon' => 'clipboard-document-list', 'abilities' => ['visit.view']],
        ['label' => 'Antrean Resep', 'route' => 'pharmacy.index', 'icon' => 'clipboard-document-list', 'abilities' => ['pharmacy.view']],
        ['label' => 'Manajemen Stok Obat', 'route' => 'medicines.index', 'icon' => 'beaker', 'abilities' => ['pharmacy.view']],
        ['label' => 'Rujukan', 'route' => 'referrals.index', 'icon' => 'paper-airplane', 'abilities' => ['referral.view']],
        ['label' => 'Laporan', 'route' => 'reports.visits', 'icon' => 'document-chart-bar', 'abilities' => ['report.view']],
        ['label' => 'Audit Trail', 'route' => 'audit.logs', 'icon' => 'shield-check', 'abilities' => ['audit.view']],
        ['label' => 'Kelola Pengguna', 'route' => 'users.index', 'icon' => 'users', 'abilities' => ['user.manage']],
    ];
    $user = auth()->user();
    $designation = $user?->designation ?: __('Petugas Kesehatan');
    $avatarUrl = $user
        ? ($user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=2563EB&color=FFFFFF&size=128')
        : null;
@endphp

<div x-data="{ sidebarOpen: false }" class="app-shell">
    <!-- Sidebar -->
    <aside class="sidebar hidden lg:flex">
        <div class="flex items-center gap-3">
            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#2563EB] text-white shadow-[0_14px_30px_-15px_rgba(37,99,235,0.7)]">
                <x-icon.chart-bar class="h-6 w-6" />
            </div>
            <div>
                <p class="text-sm font-medium text-[#6B7280]">SIMPUS</p>
                <p class="text-xl font-semibold text-[#0F172A]">Puskesmas</p>
            </div>
        </div>

        <nav class="sidebar-nav mt-10">
            <ul class="space-y-1">
                @foreach ($menuItems as $item)
                    @php
                        $abilities = $item['abilities'] ?? [];
                        $canAccess = empty($abilities) || $user?->canAny($abilities);
                    @endphp
                    @continue(! $canAccess)
                    @php
                        $isActive = request()->routeIs($item['route']) || request()->routeIs(\Illuminate\Support\Str::of($item['route'])->beforeLast('.')->append('*'));
                    @endphp
                    <li>
                        <a href="{{ route($item['route']) }}"
                           class="sidebar-link {{ $isActive ? 'sidebar-link-active' : '' }}">
                            <span class="flex h-9 w-9 items-center justify-center rounded-[12px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-[#EEF2FF] text-[#2563EB]' }}">
                                <x-dynamic-component :component="'icon.'.$item['icon']" class="h-5 w-5" />
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        @if ($user)
            <div class="mt-6 flex items-center gap-3 rounded-[16px] bg-[#EEF2FF] px-4 py-3">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-white shadow">
                    <x-icon.user-circle class="h-6 w-6 text-[#2563EB]" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-[#0F172A]">{{ $user->name }}</p>
                    <p class="text-xs text-[#6B7280]">{{ $designation }}</p>
                </div>
            </div>
        @endif
    </aside>

    <!-- Mobile sidebar -->
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 flex lg:hidden" role="dialog" aria-modal="true">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60" @click="sidebarOpen = false"></div>
        <aside x-show="sidebarOpen"
               x-transition:enter="transition ease-in-out duration-200"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in-out duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="relative flex w-72 flex-col border-r border-[#E2E8F0] bg-white px-5 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-2xl bg-[#2563EB] text-white">
                        <x-icon.chart-bar class="h-5 w-5" />
                    </div>
                    <p class="text-lg font-semibold text-[#0F172A]">SIMPUS</p>
                </div>
                <button type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#EEF2FF] text-[#2563EB]"
                        @click="sidebarOpen = false">
                    <x-icon.x class="h-5 w-5" />
                </button>
            </div>

            <nav class="sidebar-nav mt-8">
                <ul class="space-y-1">
                    @foreach ($menuItems as $item)
                        @php
                            $abilities = $item['abilities'] ?? [];
                            $canAccess = empty($abilities) || $user?->canAny($abilities);
                        @endphp
                        @continue(! $canAccess)
                        @php
                            $isActive = request()->routeIs($item['route']) || request()->routeIs(\Illuminate\Support\Str::of($item['route'])->beforeLast('.')->append('*'));
                        @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="sidebar-link {{ $isActive ? 'sidebar-link-active' : '' }}"
                               @click="sidebarOpen = false">
                                <span class="flex h-9 w-9 items-center justify-center rounded-[12px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-[#EEF2FF] text-[#2563EB]' }}">
                                    <x-dynamic-component :component="'icon.'.$item['icon']" class="h-5 w-5" />
                                </span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>
    </div>

    <!-- Content -->
    <div class="content-area">
        <header class="topbar">
            <button type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-[12px] border border-[#E2E8F0] bg-white text-[#2563EB] hover:bg-[#EEF2FF] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/40 lg:hidden"
                    @click="sidebarOpen = true">
                <span class="sr-only">{{ __('Buka navigasi') }}</span>
                <x-icon.bars-3 class="h-5 w-5" />
            </button>

            <div class="search-bar relative">
                <x-icon.magnifying-glass class="h-5 w-5 text-[#9CA3AF]" />
                <input type="search"
                       name="global-search"
                       id="global-search"
                       placeholder="{{ __('Cari pasien, No. RM, NIK...') }}"
                       data-autocomplete="global"
                       data-autocomplete-url="{{ route('search.suggestions') }}"
                       data-autocomplete-redirect="true"
                       data-autocomplete-fill="label"
                       data-autocomplete-min="2"
                       data-autocomplete-limit="8"
                       class="flex-1 border-0 bg-transparent text-sm text-[#0F172A] placeholder-[#9CA3AF] focus:outline-none focus:ring-0" />
            </div>

            <div class="ml-auto flex items-center gap-3">
                @if ($user)
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button"
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#E2E8F0] bg-white p-1.5 shadow-sm hover:border-[#2563EB]/40 hover:bg-[#EEF2FF] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/30">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ __('Foto profil :name', ['name' => $user->name]) }}" class="h-9 w-9 rounded-full border border-white object-cover shadow" loading="lazy">
                                @else
                                    <div class="grid h-9 w-9 place-items-center rounded-full bg-[#EEF2FF] text-[#2563EB]">
                                        <x-icon.user-circle class="h-5 w-5" />
                                    </div>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil Saya') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Keluar') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endif
            </div>
        </header>

        <main class="page-container">
            @isset($header)
                <div class="mb-6 space-y-1">
                    {{ $header }}
                </div>
            @endisset

            @if (session('status'))
                <x-toast type="success" :message="session('status')" />
            @endif

            @if (session('error'))
                <x-toast type="danger" :message="session('error')" />
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

@stack('modals')
@stack('scripts')
</body>
</html>
