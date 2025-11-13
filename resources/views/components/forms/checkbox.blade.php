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
    'sm' => 'h-4 w-4',
    'default' => 'h-5 w-5',
    'lg' => 'h-6 w-6',
];

$checkboxSize = $sizeClasses[$size] ?? $sizeClasses['default'];
@endphp

<div class="space-y-2">
    <div class="flex items-start">
        <div class="flex items-center {{ $size === 'sm' ? 'h-5' : 'h-6' }}">
            <input
                type="checkbox"
                {{ $attributes->except(['label', 'description', 'checked', 'disabled', 'error', 'size'])->merge([
                    'class' => "rounded-sm border-gray-300/60 bg-white/95 backdrop-blur-xl shadow-xs transition-all duration-300 text-emerald-600 focus:ring-emerald-500/50 focus:ring-2 focus:border-emerald-500 hover:border-emerald-400 disabled:bg-gray-50 disabled:border-gray-200 disabled:cursor-not-allowed {$checkboxSize}" .
                        ($error ? ' border-red-300 focus:border-red-500 focus:ring-red-500/50' : ''),
                    'checked' => $checked,
                    'disabled' => $disabled,
                ]) }}
            />
        </div>

        @if($label || $description)
            <div class="ml-3 {{ $size === 'sm' ? 'text-sm' : 'text-base' }}">
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
    </div>

    @if($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
</div>