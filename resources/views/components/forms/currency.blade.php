@props([
    'label' => '',
    'placeholder' => '0',
    'required' => false,
    'disabled' => false,
    'error' => '',
    'hint' => '',
    'currency' => '₦',
    'min' => null,
    'max' => null,
    'prefix' => '₦',
    'thousands' => ',',
])

@php
    $inputId = 'currency-' . uniqid();
    $currency = $prefix ?? $currency;
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-900">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
            <span class="text-gray-600 text-sm font-medium">{{ $currency }}</span>
        </div>

        <input
            type="text"
            id="{{ $inputId }}"
            {{ $attributes->merge([
                'class' => 'block w-full pl-10 pr-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300/60 rounded-xl shadow-xs bg-white/95 backdrop-blur-xl transition-all duration-300 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 hover:border-gray-400/60 hover:shadow-md disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed' .
                    ($error ? ' border-red-300 focus:border-red-500 focus:ring-red-500/50' : ''),
                'placeholder' => $placeholder,
                'required' => $required,
                'disabled' => $disabled,
            ]) }}
            inputmode="numeric"
            pattern="[0-9,]*"
            x-data="currencyInput({
                thousands: '{{ $thousands }}',
                @if($min) min: {{ $min }}, @endif
                @if($max) max: {{ $max }}, @endif
                @if($attributes->whereStartsWith('wire:model')->first())
                wireModel: '{{ str_replace(['wire:model=', 'wire:model.live='], '', $attributes->whereStartsWith('wire:model')->first()) }}'
                @endif
            })"
            x-init="init()"
            @input="handleInput($event)"
            @keydown="handleKeydown($event)"
            @paste="handlePaste($event)"
            @blur="handleBlur()"
        />
    </div>

    @if($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @elseif($hint)
        <p class="text-sm text-gray-500">{{ $hint }}</p>
    @endif
</div>