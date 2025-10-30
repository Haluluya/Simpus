@props(['class' => 'h-6 w-6 text-current'])

<svg {{ $attributes->merge(['class' => $class, 'xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke-width' => '1.5', 'stroke' => 'currentColor']) }}>
    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h12.75m-12.75 6h12.75m-12.75 6H12" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8.25v7.5l2.25-1.5 2.25 1.5v-7.5" />
</svg>
