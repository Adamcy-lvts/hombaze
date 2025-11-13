@props([
    'label' => '',
    'description' => '',
    'checked' => false,
    'disabled' => false,
    'error' => '',
    'size' => 'default', // 'sm', 'default', 'lg'
])

@php
$sizeClasses = [
    'sm' => [
        'switch' => 'h-4 w-7',
        'toggle' => 'h-3 w-3',
        'translate' => 'translate-x-3',
    ],
    'default' => [
        'switch' => 'h-6 w-11',
        'toggle' => 'h-5 w-5',
        'translate' => 'translate-x-5',
    ],
    'lg' => [
        'switch' => 'h-7 w-12',
        'toggle' => 'h-6 w-6',
        'translate' => 'translate-x-5',
    ],
];

$classes = $sizeClasses[$size] ?? $sizeClasses['default'];
@endphp

<div class="space-y-2">
    <div class="flex items-center justify-between">
        @if($label || $description)
            <div class="flex-1 {{ $size === 'sm' ? 'text-sm' : 'text-base' }}">
                @if($label)
                    <label {{ $attributes->only(['for']) }} class="font-medium text-gray-900 cursor-pointer">
                        {{ $label }}
                    </label>
                @endif

                @if($description)
                    <p class="text-gray-500 {{ $size === 'sm' ? 'text-xs' : 'text-sm' }} {{ $label ? 'mt-1' : '' }}">
                        {{ $description }}
                    </p>
                @endif
            </div>
        @endif

        <button
            type="button"
            {{ $attributes->except(['label', 'description', 'checked', 'disabled', 'error', 'size'])->merge([
                'class' => "relative inline-flex shrink-0 border-2 border-transparent rounded-full cursor-pointer transition-all duration-300 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 {$classes['switch']}" .
                    ($checked ? ' bg-linear-to-r from-emerald-500 to-teal-500' : ' bg-gray-200'),
                'disabled' => $disabled,
                'aria-pressed' => $checked ? 'true' : 'false',
                'role' => 'switch',
            ]) }}
            x-data="{ checked: {{ $checked ? 'true' : 'false' }} }"
            @click="checked = !checked; $wire.set('{{ $attributes->whereStartsWith('wire:model')->first() ?? 'value' }}', checked)"
        >
            <span class="sr-only">{{ $label ?: 'Toggle setting' }}</span>
            <span
                :class="checked ? '{{ $classes['translate'] }}' : 'translate-x-0'"
                class="pointer-events-none inline-block {{ $classes['toggle'] }} transform rounded-full bg-white shadow-lg ring-0 transition-transform duration-300 ease-in-out"
            ></span>

            <!-- Hidden input for form submission -->
            <input
                type="hidden"
                {{ $attributes->whereStartsWith('wire:model') }}
                :value="checked"
            />
        </button>
    </div>

    @if($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
</div>