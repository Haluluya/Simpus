@props(['class' => 'h-6 w-6 text-current'])

<svg {{ $attributes->merge(['class' => $class, 'xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke-width' => '1.5', 'stroke' => 'currentColor']) }}>
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15a1.5 1.5 0 001.3-2.25L13.3 4.5a1.5 1.5 0 00-2.6 0L3.2 17.25a1.5 1.5 0 001.3 2.25z" />
</svg>
