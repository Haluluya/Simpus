@props(['class' => 'h-6 w-6 text-current'])

<svg {{ $attributes->merge(['class' => $class, 'xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke-width' => '1.5', 'stroke' => 'currentColor']) }}>
    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 9h1.5m-1.5 3.75h1.5V16.5h-1.5z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>
