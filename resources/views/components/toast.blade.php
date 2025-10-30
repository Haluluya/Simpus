@props([
    'type' => 'success',
    'message',
])

@php
    $styles = [
        'success' => 'bg-success/10 text-success border-success/20',
        'danger' => 'bg-danger/10 text-danger border-danger/20',
        'warning' => 'bg-warning/10 text-warning border-warning/20',
        'info' => 'bg-brand/10 text-brand border-brand/20',
    ];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition.opacity
     class="fixed bottom-4 right-4 z-50 flex max-w-sm items-start gap-3 rounded-xl border px-5 py-4 text-sm shadow-lg shadow-slate-900/10 {{ $styles[$type] ?? $styles['info'] }}"
     role="alert">
    <div class="mt-0.5 shrink-0">
        @switch($type)
            @case('success')
                <x-icon.check-circle class="h-5 w-5 text-success" />
                @break
            @case('danger')
                <x-icon.exclamation-triangle class="h-5 w-5 text-danger" />
                @break
            @case('warning')
                <x-icon.exclamation-triangle class="h-5 w-5 text-warning" />
                @break
            @default
                <x-icon.information-circle class="h-5 w-5 text-brand" />
        @endswitch
    </div>
    <div class="flex-1 font-medium">{{ $message }}</div>
    <button type="button" class="text-current/80 hover:text-current" @click="show = false">
        <span class="sr-only">{{ __('Tutup notifikasi') }}</span>
        <x-icon.x class="h-4 w-4" />
    </button>
</div>
