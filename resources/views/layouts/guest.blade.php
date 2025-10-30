<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIMPUS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-[#EEF2FF] via-white to-[#F5F7FD]">
    <div class="mx-auto flex min-h-screen w-full max-w-6xl flex-col items-center justify-center px-6 lg:flex-row lg:gap-24">
        <div class="text-center lg:text-left">
            <div class="inline-flex items-center gap-3 rounded-[20px] bg-white/80 px-5 py-3 shadow-[0_18px_40px_-25px_rgba(37,99,235,0.35)] backdrop-blur">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#2563EB] text-white">
                    <x-icon.chart-bar class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-[#2563EB]">SIMPUS</p>
                    <p class="text-xl font-semibold text-[#0F172A]">Sistem Informasi Puskesmas</p>
                </div>
            </div>
            <h1 class="mt-10 text-4xl font-semibold leading-tight text-[#0F172A] lg:text-5xl">
                Kelola layanan kesehatan Puskesmas lebih terstruktur & terintegrasi
            </h1>
            <p class="mt-4 max-w-xl text-base text-[#6B7280]">
                Akses dashboard, pendaftaran, rekam medis elektronik, laboratorium, dan integrasi BPJS serta SATUSEHAT dalam satu platform terpadu.
            </p>
        </div>

        <div class="mt-12 w-full max-w-md rounded-[24px] border border-[#E2E8F0] bg-white p-10 shadow-[0_25px_70px_-30px_rgba(37,99,235,0.4)] lg:mt-0">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
