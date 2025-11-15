{{-- resources/views/lease-components/sections/terms-conditions.blade.php --}}
<div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">TERMS AND CONDITIONS</h3>

    {{-- Use Filament's RichContentRenderer with merge tags to safely render rich editor HTML.
         This will sanitize HTML, handle private image temporary URLs, and
         apply Filament-specific styles. We also include Tailwind Typography
         classes as a fallback/augmentation. Content is already processed with merge tags by the controller. --}}
    <div class="fi-prose prose prose-sm max-w-none dark:prose-invert">
        @if (class_exists(\Filament\Forms\Components\RichEditor\RichContentRenderer::class))
            {{-- Render to string to avoid object-to-string conversion error --}}
            {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content)->toHtml() !!}
        @else
            {{-- Fallback: sanitize HTML using Filament helper if renderer not available --}}
            {!! str($content)->sanitizeHtml() !!}
        @endif
    </div>
</div>