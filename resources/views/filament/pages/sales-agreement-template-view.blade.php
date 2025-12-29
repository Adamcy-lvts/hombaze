<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Header Card --}}
        <!-- <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="p-8 border-b border-gray-100 dark:border-gray-800 bg-linear-to-br from-gray-50/50 to-white dark:from-gray-800/50 dark:to-gray-900">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-2xl flex items-center justify-center shadow-inner">
                            <x-filament::icon icon="heroicon-o-document-text" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] mb-1">Agreement Template</p>
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $record->name }}</h1>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <x-filament::badge color="info" size="lg" icon="heroicon-m-document-duplicate" class="rounded-xl px-4 py-2">
                            {{ $record->sales_agreements_count ?? $record->salesAgreements()->count() }} Agreements Generated
                        </x-filament::badge>
                        @if($record->is_active)
                            <x-filament::badge color="success" size="lg" icon="heroicon-m-check-circle" class="rounded-xl px-4 py-2">
                                Active Template
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="gray" size="lg" icon="heroicon-m-x-circle" class="rounded-xl px-4 py-2">
                                Inactive
                            </x-filament::badge>
                        @endif
                        @if($record->is_default)
                            <x-filament::badge color="warning" size="lg" icon="heroicon-m-star" class="rounded-xl px-4 py-2">
                                Default Master
                            </x-filament::badge>
                        @endif
                    </div>
                </div>

                @if($record->description)
                    <div class="mt-6 p-4 bg-blue-50/30 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-900/20 rounded-xl">
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed font-medium italic">
                            "{{ $record->description }}"
                        </p>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-800">
                <div class="p-6 flex items-center gap-4 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                        <x-filament::icon icon="heroicon-m-calendar" class="w-5 h-5 text-gray-400" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date Created</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $record->created_at?->format('F j, Y') }}</p>
                    </div>
                </div>
                <div class="p-6 flex items-center gap-4 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                        <x-filament::icon icon="heroicon-m-arrow-path" class="w-5 h-5 text-gray-400" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Last Modified</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $record->updated_at?->format('F j, Y') }}</p>
                    </div>
                </div>
                <div class="p-6 flex items-center gap-4 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                        <x-filament::icon icon="heroicon-m-user-group" class="w-5 h-5 text-gray-400" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ownership Type</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">
                            @if($record->agency_id) Agency Control @elseif($record->agent_id) Personal (Agent) @else System Master @endif
                        </p>
                    </div>
                </div>
            </div>
        </div> -->

        {{-- Preview Section --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Template Content Preview</h2>
                    <p class="text-sm font-medium text-gray-500">Live preview of how agreements will be structured using this template</p>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-full border border-gray-200 dark:border-gray-700">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest leading-none">Merge Tags Active</span>
                </div>
            </div>

            {{-- The "Paper" Container --}}
            <div class="max-w-4xl mx-auto animate-in fade-in zoom-in-95 duration-700 delay-200">
                <div class="bg-white dark:bg-gray-800 rounded-sm shadow-[0_20px_50px_rgba(0,0,0,0.1)] dark:shadow-none dark:border dark:border-gray-700 p-6 sm:p-10 lg:p-14 relative overflow-hidden transition-all duration-500 hover:shadow-[0_40px_80px_rgba(0,0,0,0.15)]">
                    {{-- Paper Texture Overlay --}}
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/natural-paper.png')]"></div>
                    
                    {{-- Document Header Accent --}}
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-linear-to-r from-blue-600 via-indigo-600 to-purple-600 opacity-80"></div>

                    <div class="relative z-10">
                        <div class="fi-prose prose prose-sm max-w-none dark:prose-invert agreement-content-preview">
                            @if (class_exists(\Filament\Forms\Components\RichEditor\RichContentRenderer::class))
                                {!! \Filament\Forms\Components\RichEditor\RichContentRenderer::make($record->terms_and_conditions)->toHtml() !!}
                            @else
                                {!! str($record->terms_and_conditions)->sanitizeHtml() !!}
                            @endif
                        </div>
                    </div>

                    {{-- Corner Stamp Decoration --}}
                    <div class="absolute -bottom-12 -right-12 w-48 h-48 border-[20px] border-blue-500/5 rounded-full pointer-events-none flex items-center justify-center">
                        <span class="text-blue-500/10 font-black text-4xl rotate-12 uppercase">Preview</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    <style>
        .agreement-content-preview h1 { font-size: 1.5rem; font-weight: 900; color: #111827; margin-bottom: 2rem; text-align: center; text-transform: uppercase; border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; }
        @media (min-width: 768px) { .agreement-content-preview h1 { font-size: 2rem; } }

        .agreement-content-preview h2 { font-size: 1.25rem; font-weight: 800; color: #111827; margin-top: 2rem; margin-bottom: 1rem; }
        @media (min-width: 768px) { .agreement-content-preview h2 { font-size: 1.5rem; } }

        .agreement-content-preview h3 { font-size: 1rem; font-weight: 700; color: #111827; margin-top: 1.5rem; margin-bottom: 0.75rem; border-left: 4px solid #3b82f6; padding-left: 1rem; }
        @media (min-width: 768px) { .agreement-content-preview h3 { font-size: 1.125rem; } }

        .agreement-content-preview p { color: #374151; line-height: 1.6; margin-bottom: 1.125rem; font-size: 0.875rem; }
        @media (min-width: 768px) { .agreement-content-preview p { line-height: 1.75; margin-bottom: 1.25rem; font-size: 1rem; } }

        .agreement-content-preview strong { color: #111827; }
        
        .dark .agreement-content-preview h1,
        .dark .agreement-content-preview h2,
        .dark .agreement-content-preview h3,
        .dark .agreement-content-preview strong { color: #f3f4f6; }
        .dark .agreement-content-preview p { color: #d1d5db; }
        .dark .agreement-content-preview h1 { border-color: #374151; }
    </style>
</x-filament-panels::page>
