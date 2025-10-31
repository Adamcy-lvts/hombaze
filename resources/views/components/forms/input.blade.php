@props([
    'label' => '',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'error' => '',
    'hint' => '',
    'icon' => null,
    'suffix' => null,
    'prefix' => null,
])

<div class="space-y-2">
    @if($label)
        <label {{ $attributes->only(['for']) }} class="block text-sm font-medium text-gray-900">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-dynamic-component :component="$icon" class="h-5 w-5 text-gray-400" />
            </div>
        @endif

        @if($prefix)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">{{ $prefix }}</span>
            </div>
        @endif

        <input
            type="{{ $type }}"
            {{ $attributes->except(['label', 'type', 'placeholder', 'required', 'disabled', 'error', 'hint', 'icon', 'suffix', 'prefix'])->merge([
                'class' => 'block w-full px-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300/60 rounded-xl shadow-sm bg-white/95 backdrop-blur-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 hover:border-gray-400/60 hover:shadow-md disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed' .
                    ($icon || $prefix ? ' pl-10' : '') .
                    ($suffix ? ' pr-10' : '') .
                    ($error ? ' border-red-300 focus:border-red-500 focus:ring-red-500/50' : ''),
                'placeholder' => $placeholder,
                'required' => $required,
                'disabled' => $disabled,
            ]) }}
        />

        @if($suffix)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">{{ $suffix }}</span>
            </div>
        @endif
    </div>

    @if($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @elseif($hint)
        <p class="text-sm text-gray-500">{{ $hint }}</p>
    @endif
</div>