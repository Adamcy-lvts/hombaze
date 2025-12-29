<div class="bg-white dark:bg-gray-800 p-10 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
    <div class="flex items-center gap-3 mb-8 border-b border-gray-100 dark:border-gray-700 pb-4">
        <x-filament::icon icon="heroicon-o-scale" class="w-5 h-5 text-gray-400" />
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Standard & Special Provisions</h3>
    </div>

    <div class="fi-prose prose prose-sm max-w-none dark:prose-invert agreement-content-render">
        @if (class_exists(\Filament\Forms\Components\RichEditor\RichContentRenderer::class))
            {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content)->toHtml() !!}
        @else
            {!! str($content)->sanitizeHtml() !!}
        @endif
    </div>
</div>

<style>
    .agreement-content-render {
        font-family: 'Inter', system-ui, sans-serif;
    }
    .agreement-content-render h3 {
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #111827;
        border-left: 4px solid #3b82f6;
        padding-left: 1rem;
    }
    .dark .agreement-content-render h3 {
        color: #f3f4f6;
    }
    .agreement-content-render p {
        margin-bottom: 1.25rem;
        line-height: 1.75;
        color: #374151;
    }
    .dark .agreement-content-render p {
        color: #d1d5db;
    }
    .agreement-content-render strong {
        color: #111827;
        font-weight: 700;
    }
    .dark .agreement-content-render strong {
        color: #ffffff;
    }
</style>
