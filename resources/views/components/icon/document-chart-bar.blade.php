@props(['class' => 'h-6 w-6 text-current'])
<svg {{ $attributes->merge(['class' => $class, 'xmlns' => 'http://www.w3.org/2000/svg', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke-width' => '1.5', 'stroke' => 'currentColor']) }}>
    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 2.25h-6A2.25 2.25 0 0 0 5.25 4.5v15A2.25 2.25 0 0 0 7.5 21.75h9a2.25 2.25 0 0 0 2.25-2.25V8.25L13.5 2.25z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 2.25V7.5h5.25" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v-3m3 3v-4.5m3 4.5V12" />
</svg>
