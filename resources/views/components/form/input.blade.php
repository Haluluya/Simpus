@props([
    'id' => null,
    'name',
    'type' => 'text',
    'label' => null,
    'value' => null,
    'required' => false,
    'placeholder' => null,
])

@php
    $fieldId = $id ?? $name;
    $baseClass = match ($type) {
        'select' => 'select',
        'textarea' => 'textarea',
        default => 'input',
    };
    $inputClasses = $baseClass.' mt-1';
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
@endphp

<div {{ $attributes->class('flex flex-col gap-2') }}>
    @if ($label)
        <label for="{{ $fieldId }}" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
            {{ $label }}@if($required) <span class="text-danger">*</span>@endif
        </label>
    @endif

    @if ($type === 'select')
        <select
            id="{{ $fieldId }}"
            name="{{ $name }}"
            @if($required) required @endif
            class="{{ $inputClasses }}">
            {{ $slot }}
        </select>
    @elseif ($type === 'textarea')
        <textarea
            id="{{ $fieldId }}"
            name="{{ $name }}"
            @if($required) required @endif
            placeholder="{{ $placeholder }}"
            class="{{ $inputClasses }}">{{ old($name, $value) }}</textarea>
    @else
        <input
            id="{{ $fieldId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            class="{{ $inputClasses }}">
    @endif

    <x-input-error :messages="$errors->get($errorKey)" />
</div>
