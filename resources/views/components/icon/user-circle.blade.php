@props(['class' => 'h-6 w-6 text-current'])

<svg {{ $attributes->merge(['class' => $class, 'xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke-width' => '1.5', 'stroke' => 'currentColor']) }}>
    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9A3.75 3.75 0 1 1 8.25 9a3.75 3.75 0 0 1 7.5 0z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0 1 15 0v.75H4.5z" />
    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" fill="none" />
</svg>
