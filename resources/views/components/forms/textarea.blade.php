@props([
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'error' => '',
    'hint' => '',
    'rows' => 4,
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
        <textarea
            rows="{{ $rows }}"
            {{ $attributes->except(['label', 'placeholder', 'required', 'disabled', 'error', 'hint', 'rows'])->merge([
                'class' => 'block w-full px-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300/60 rounded-xl shadow-xs bg-white/95 backdrop-blur-xl transition-all duration-300 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 hover:border-gray-400/60 hover:shadow-md disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed resize-none' .
                    ($error ? ' border-red-300 focus:border-red-500 focus:ring-red-500/50' : ''),
                'placeholder' => $placeholder,
                'required' => $required,
                'disabled' => $disabled,
            ]) }}
        ></textarea>
    </div>

    @if($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @elseif($hint)
        <p class="text-sm text-gray-500">{{ $hint }}</p>
    @endif
</div>