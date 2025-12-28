{{-- resources/views/sales-components/sections/terms-conditions.blade.php --}}
<div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">TERMS AND CONDITIONS</h3>

    <div class="fi-prose prose prose-sm max-w-none dark:prose-invert">
        @if (class_exists(\Filament\Forms\Components\RichEditor\RichContentRenderer::class))
            {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content)->toHtml() !!}
        @else
            {!! str($content)->sanitizeHtml() !!}
        @endif
    </div>
</div>
