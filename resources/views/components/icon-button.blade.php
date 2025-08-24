@props([
    'href' => null,
    'variant' => 'secondary'
])

@php
$baseClasses = 'p-2 rounded-full transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2';

$variantClasses = [
    'primary' => 'text-blue-600 hover:bg-blue-100 focus:ring-blue-500 dark:text-blue-400 dark:hover:bg-blue-900',
    'secondary' => 'text-gray-500 hover:bg-gray-100 focus:ring-gray-500 dark:text-gray-400 dark:hover:bg-gray-700',
    'danger' => 'text-red-600 hover:bg-red-100 focus:ring-red-500 dark:text-red-400 dark:hover:bg-red-900',
    'warning' => 'text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-500 dark:text-yellow-400 dark:hover:bg-yellow-900',
][$variant] ?? 'text-gray-500 hover:bg-gray-100 focus:ring-gray-500 dark:text-gray-400 dark:hover:bg-gray-700';

$classes = $baseClasses . ' ' . $variantClasses;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif