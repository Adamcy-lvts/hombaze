@props([
    'variant' => 'primary', // 'primary', 'secondary', 'danger', 'success', 'warning', 'info'
    'size' => 'default', // 'sm', 'default', 'lg', 'xl'
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left', // 'left', 'right'
    'type' => 'button',
])

@php
$variants = [
    'primary' => 'bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white shadow-lg',
    'secondary' => 'bg-white/95 backdrop-blur-xl border border-gray-300/60 text-gray-700 hover:bg-white hover:border-gray-400/60 shadow-lg',
    'danger' => 'bg-gradient-to-r from-red-600 via-red-500 to-pink-500 hover:from-red-700 hover:via-red-600 hover:to-pink-600 text-white shadow-lg',
    'success' => 'bg-gradient-to-r from-green-600 via-green-500 to-emerald-500 hover:from-green-700 hover:via-green-600 hover:to-emerald-600 text-white shadow-lg',
    'warning' => 'bg-gradient-to-r from-yellow-600 via-yellow-500 to-orange-500 hover:from-yellow-700 hover:via-yellow-600 hover:to-orange-600 text-white shadow-lg',
    'info' => 'bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 hover:from-blue-700 hover:via-blue-600 hover:to-indigo-600 text-white shadow-lg',
];

$sizes = [
    'sm' => 'px-4 py-2 text-sm',
    'default' => 'px-6 py-3 text-base',
    'lg' => 'px-8 py-4 text-lg',
    'xl' => 'px-10 py-5 text-xl',
];

$variantClasses = $variants[$variant] ?? $variants['primary'];
$sizeClasses = $sizes[$size] ?? $sizes['default'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->except(['variant', 'size', 'disabled', 'loading', 'icon', 'iconPosition', 'type'])->merge([
        'class' => "inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-500 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500/50 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none {$variantClasses} {$sizeClasses}",
        'disabled' => $disabled || $loading,
    ]) }}
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading...
    @else
        @if($icon && $iconPosition === 'left')
            <x-dynamic-component :component="$icon" class="w-5 h-5 mr-2" />
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right')
            <x-dynamic-component :component="$icon" class="w-5 h-5 ml-2" />
        @endif
    @endif
</button>