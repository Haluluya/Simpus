@props(['type' => 'button', 'disabled' => false])

<button {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['type' => $type, 'class' => 'btn btn-danger']) }}>
    {{ $slot }}
</button>
