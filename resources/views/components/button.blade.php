@props([
    'type' => 'button',
    'variant' => 'primary',
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
])

@php
    $baseClasses = 'btn';
    $variants = [
        'primary' => 'btn-primary',
        'outline' => 'btn-outline',
        'danger' => 'btn-danger',
        'success' => 'btn-success',
        'warning' => 'btn-warning',
        'ghost' => 'btn-ghost',
    ];
    $classes = trim($baseClasses.' '.($variants[$variant] ?? $variants['primary']));
    $iconClasses = 'h-5 w-5';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <x-dynamic-component :component="'icon.'.$icon" :class="$iconClasses" />
        @endif
        <span>{{ $slot }}</span>
        @if($icon && $iconPosition === 'right')
            <x-dynamic-component :component="'icon.'.$icon" :class="$iconClasses" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <x-dynamic-component :component="'icon.'.$icon" :class="$iconClasses" />
        @endif
        <span>{{ $slot }}</span>
        @if($icon && $iconPosition === 'right')
            <x-dynamic-component :component="'icon.'.$icon" :class="$iconClasses" />
        @endif
    </button>
@endif
