@props([
    'title' => 'Tidak ada data',
    'message' => null,
    'icon' => 'information-circle',
])

<div {{ $attributes->class('mx-auto max-w-xl rounded-2xl border border-dashed border-slate-200 bg-white px-8 py-12 text-center shadow-sm') }}>
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-500">
        <x-dynamic-component :component="'icon.'.$icon" class="h-7 w-7" />
    </div>
    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $title }}</h3>
    @if ($message)
        <p class="mt-2 text-sm text-slate-500">{{ $message }}</p>
    @endif

    @if (isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>
