@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary'
])

@php
$baseClasses = 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150';

$variantClasses = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:ring-blue-500',
    'secondary' => 'bg-gray-600 hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:ring-gray-500',
    'success' => 'bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:ring-green-500',
    'danger' => 'bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:ring-red-500',
    'warning' => 'bg-yellow-500 hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:ring-yellow-400',
][$variant] ?? 'bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:ring-blue-500';

$classes = $baseClasses . ' ' . $variantClasses;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif