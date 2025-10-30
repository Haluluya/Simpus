@props(['class' => 'h-6 w-6 text-current'])

<svg {{ $attributes->merge(['class' => $class, 'xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke-width' => '1.5', 'stroke' => 'currentColor']) }}>
    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5l16.5 7.5-16.5 7.5 3.375-7.5L3.75 4.5z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M7.125 12h4.5" />
</svg>
