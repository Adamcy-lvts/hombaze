<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Controls Section --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Document Generation</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Configure and generate your professional sales agreement</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <x-filament::button 
                            wire:click="generateSalesAgreementDocument" 
                            type="button" 
                            size="lg" 
                            color="info"
                            icon="heroicon-o-document-plus"
                            class="w-full sm:w-auto shadow-lg shadow-blue-500/20"
                        >
                            <span wire:loading.remove wire:target="generateSalesAgreementDocument">
                                @if(filled($this->salesAgreementDocument))
                                    Regenerate Document
                                @else
                                    Generate Agreement
                                @endif
                            </span>
                            <span wire:loading wire:target="generateSalesAgreementDocument">
                                Generating...
                            </span>
                        </x-filament::button>

                        @if(filled($this->salesAgreementDocument))
                            <x-filament::button 
                                wire:click="downloadPdf" 
                                type="button" 
                                size="lg" 
                                color="success"
                                icon="heroicon-o-arrow-down-tray"
                                class="w-full sm:w-auto shadow-lg shadow-green-500/20"
                            >
                                Download PDF
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <label for="template-select" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Select Agreement Template
                    </label>
                    <div class="relative group">
                        <select
                            id="template-select"
                            wire:model.live="data.template_id"
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-200 appearance-none text-gray-900 dark:text-white"
                        >
                            <option value="">Standard Default Template</option>
                            @foreach($this->availableTemplates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                            <x-filament::icon icon="heroicon-m-chevron-down" class="w-5 h-5" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 flex items-center gap-1">
                        <x-filament::icon icon="heroicon-m-information-circle" class="w-4 h-4" />
                        Templates provide specialized legal wording for different property types
                    </p>
                </div>

                @if($this->salesAgreementDocument && !empty($this->salesAgreementDocument['template_id']))
                    <div class="bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-xl p-5 flex gap-4 animate-in fade-in slide-in-from-right-4 duration-500">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center shrink-0">
                            <x-filament::icon icon="heroicon-o-document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="font-bold text-blue-900 dark:text-blue-300">{{ $this->salesAgreementDocument['template_name'] ?? 'Selected Template' }}</h3>
                            <p class="text-sm text-blue-700 dark:text-blue-400 mt-1 leading-relaxed">
                                {{ $this->salesAgreementDocument['template_description'] ?: 'Professional legal template selected for this agreement.' }}
                            </p>
                        </div>
                    </div>
                @elseif($this->salesAgreementDocument)
                    <div class="bg-gray-50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800 rounded-xl p-5 flex gap-4 animate-in fade-in slide-in-from-right-4 duration-500">
                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center shrink-0">
                            <x-filament::icon icon="heroicon-o-shield-check" class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-gray-300">Default Standard Template</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Using the system\'s verified standard property sales agreement.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($this->salesAgreementDocument)
            <div class="mt-8 animate-in fade-in zoom-in-95 duration-700">
                @if (isset($this->salesAgreementDocument['error']))
                    <div class="p-6 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 rounded-2xl flex gap-4">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-6 h-6 text-red-600" />
                        <p class="text-red-700 dark:text-red-400 font-medium">{{ $this->salesAgreementDocument['error'] }}</p>
                    </div>
                @else
                    {{-- The "Paper" Container --}}
                    <div class="max-w-5xl mx-auto pb-20">
                        <div class="bg-white dark:bg-slate-900 rounded-sm shadow-[0_30px_100px_rgba(0,0,0,0.12)] border border-gray-100 dark:border-slate-800 p-4 sm:p-8 md:p-12 relative overflow-hidden transition-all duration-500">
                            {{-- Paper Texture Overlay (Subtle) --}}
                            <div class="absolute inset-0 opacity-[0.04] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/natural-paper.png')]"></div>
                            
                            {{-- Document Header Accent --}}
                            <div class="absolute top-0 left-0 right-0 h-2 bg-linear-to-r from-blue-600 via-indigo-600 to-purple-600"></div>

                            <div class="relative z-10 space-y-16">
                                @php
                                    $agreement = $this->record;
                                    $property = $this->record->property;
                                    $content = $this->salesAgreementDocument['content'] ?? '';
                                @endphp

                                {{-- Header Section --}}
                                <div class="border-b-4 border-double border-gray-200 dark:border-gray-700 pb-8 mb-12">
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="w-14 h-14 md:w-20 md:h-20 bg-linear-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-xl flex items-center justify-center transform -rotate-3 hover:rotate-3 transition-transform duration-300">
                                            <x-application-logo class="w-8 h-8 md:w-12 md:h-12 text-white" />
                                        </div>
                                        <div class="text-center">
                                            <h1 class="text-2xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight uppercase">Sales Agreement</h1>
                                            <div class="flex items-center justify-center gap-3 mt-2">
                                                <span class="h-px w-8 bg-gray-300 dark:bg-gray-600"></span>
                                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em]">
                                                    Property: {{ $agreement->property?->title ?? 'Real Estate Asset' }}
                                                </p>
                                                <span class="h-px w-8 bg-gray-300 dark:bg-gray-600"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Details Grid --}}
                                <div class="space-y-16">
                                    {{-- Property Glance --}}
                                    {{-- Property Glance (Redesigned) --}}
                                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden group hover:shadow-md transition-shadow">
                                        {{-- Header / Title Row --}}
                                        <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-start justify-between gap-6 bg-linear-to-b from-gray-50/50 to-white dark:from-slate-800/30 dark:to-slate-900">
                                            <div class="space-y-3 flex-1">
                                                <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                                                    <div class="p-1.5 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                                        <x-filament::icon icon="heroicon-o-home-modern" class="w-4 h-4" />
                                                    </div>
                                                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Subject Property</span>
                                                </div>
                                                <div>
                                                    <h2 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white leading-tight mb-1">
                                                        {{ $property?->title ?? 'N/A' }}
                                                    </h2>
                                                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                        <x-filament::icon icon="heroicon-m-map-pin" class="w-4 h-4 text-gray-400" />
                                                        <span class="font-medium">{{ $property?->address ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="md:text-right">
                                                <div class="inline-block bg-blue-50/50 dark:bg-blue-900/10 rounded-xl px-5 py-3 border border-blue-100 dark:border-blue-900/30">
                                                    <p class="text-[10px] font-bold text-blue-600/70 dark:text-blue-400/70 uppercase tracking-widest mb-1">Market Valuation</p>
                                                    <p class="text-xl font-black text-blue-600 dark:text-blue-400 tracking-tight">
                                                        {{ formatNaira($property?->price ?? 0) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Divider with dash pattern --}}
                                        <div class="h-px bg-gray-100 dark:bg-slate-800 w-full relative">
                                            <div class="absolute inset-0 bg-image-repeat-x opacity-20" style="background-image: linear-gradient(to right, #000 33%, rgba(255,255,255,0) 0%); background-position: bottom; background-size: 8px 1px;"></div>
                                        </div>

                                        {{-- Metadata Grid --}}
                                        <div class="p-5 md:p-8 bg-white dark:bg-slate-900">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-12">
                                                <div class="space-y-1">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Category</p>
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                        <p class="text-sm font-bold text-gray-700 dark:text-gray-200">
                                                            {{ $property?->propertyType?->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">City</p>
                                                    <p class="text-sm font-bold text-gray-700 dark:text-gray-200">
                                                        {{ $property?->city?->name ?? 'N/A' }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">State</p>
                                                    <p class="text-sm font-bold text-gray-700 dark:text-gray-200">
                                                        {{ $property?->state?->name ?? 'N/A' }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Property ID</p>
                                                    <p class="text-sm font-mono font-medium text-gray-500 dark:text-gray-400">
                                                        #{{ str_pad($property?->id ?? 0, 6, '0', STR_PAD_LEFT) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- The Parties --}}
                                    {{-- @include('sales-components.sections.parties-info', ['agreement' => $this->record]) --}}
                                    
                                    {{-- Financial Terms --}}
                                    {{-- @include('sales-components.sections.sale-terms', ['agreement' => $this->record]) --}}
                                    
                                    {{-- Legal Content --}}
                                    <div class="pt-8 border-t border-gray-100 dark:border-slate-800">
                                        <div class="bg-white dark:bg-gray-800 p-5 md:p-10 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
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
                                    </div>
                                    
                                    {{-- Execution Section --}}
                                    <div class="pt-12 border-t-2 border-gray-100 dark:border-slate-800">
                                        <div class="flex items-center gap-3 mb-12">
                                            <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                                                <x-filament::icon icon="heroicon-o-pencil-square" class="w-4 h-4 text-gray-400" />
                                            </div>
                                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Signatures & Execution</h3>
                                        </div>
                                        @php
                                            $sellerName = $agreement->seller_name ?: $agreement->property?->owner?->name;
                                            $buyerName = $agreement->buyer_name ?: $agreement->buyer?->name;
                                        @endphp

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-20">
                                            {{-- Seller Section --}}
                                            <div class="space-y-10 group">
                                                <div class="h-40 border-b-2 border-gray-100 dark:border-slate-800 relative flex items-end pb-6 transition-colors duration-500 group-hover:border-blue-500/30">
                                                    <div class="absolute inset-0 bg-linear-to-t from-blue-50/20 to-transparent dark:from-blue-900/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-2xl"></div>
                                                    @if($agreement->status === 'signed' || $agreement->status === 'completed')
                                                        <div class="absolute inset-x-0 top-0 flex items-center justify-center h-full pointer-events-none overflow-hidden">
                                                            <div class="border-4 border-blue-500/20 px-6 py-2 rounded-xl rotate-[-15deg] transform scale-150 opacity-40">
                                                                <span class="text-4xl font-black text-blue-500 tracking-[0.3em] uppercase whitespace-nowrap">EXECUTED</span>
                                                            </div>
                                                        </div>
                                                        <div class="absolute bottom-6 left-0 animate-in fade-in slide-in-from-left-4 duration-1000">
                                                            <span class="text-4xl font-normal text-blue-800/80 dark:text-blue-400/70 signature-font">{{ $sellerName }}</span>
                                                        </div>
                                                    @endif
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] absolute -bottom-8 left-0">Digital Signature of Seller</p>
                                                </div>
                                                <div class="flex items-center gap-5">
                                                    <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center border border-blue-100 dark:border-blue-900/30 transition-transform duration-500 group-hover:scale-110">
                                                        <x-filament::icon icon="heroicon-o-user" class="w-7 h-7 text-blue-600 dark:text-blue-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-lg font-black text-gray-900 dark:text-white tracking-tight">{{ $sellerName ?? 'Authorized Seller' }}</p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Verified: {{ $agreement->signed_date?->format('F j, Y') ?? 'Pending' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Buyer Section --}}
                                            <div class="space-y-10 group">
                                                <div class="h-40 border-b-2 border-gray-100 dark:border-slate-800 relative flex items-end pb-6 transition-colors duration-500 group-hover:border-emerald-500/30">
                                                    <div class="absolute inset-0 bg-linear-to-t from-emerald-50/20 to-transparent dark:from-emerald-900/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-2xl"></div>
                                                    @if($agreement->status === 'signed' || $agreement->status === 'completed')
                                                        <div class="absolute inset-x-0 top-0 flex items-center justify-center h-full pointer-events-none overflow-hidden">
                                                            <div class="border-4 border-emerald-500/20 px-6 py-2 rounded-xl rotate-[-15deg] transform scale-150 opacity-40">
                                                                <span class="text-4xl font-black text-emerald-500 tracking-[0.3em] uppercase whitespace-nowrap">EXECUTED</span>
                                                            </div>
                                                        </div>
                                                        <div class="absolute bottom-6 left-0 animate-in fade-in slide-in-from-left-4 duration-1000 delay-300">
                                                            <span class="text-4xl font-normal text-emerald-800/80 dark:text-emerald-400/70 signature-font">{{ $buyerName }}</span>
                                                        </div>
                                                    @endif
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] absolute -bottom-8 left-0">Digital Signature of Buyer</p>
                                                </div>
                                                <div class="flex items-center gap-5">
                                                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center border border-emerald-100 dark:border-emerald-900/30 transition-transform duration-500 group-hover:scale-110">
                                                        <x-filament::icon icon="heroicon-o-user" class="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-lg font-black text-gray-900 dark:text-white tracking-tight">{{ $buyerName ?? 'Authorized Buyer' }}</p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Verified: {{ $agreement->signed_date?->format('F j, Y') ?? 'Pending' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <style>
                                            @import url('https://fonts.googleapis.com/css2?family=Mrs+Saint+Delafield&display=swap');
                                            .signature-font {
                                                font-family: 'Mrs Saint Delafield', cursive;
                                            }
                                        </style>
                                    </div>
                                </div>
                            </div>

                            {{-- Corner Stamp Decoration --}}
                            <div class="absolute -bottom-16 -right-16 w-64 h-64 border-[30px] border-blue-500/5 rounded-full pointer-events-none flex items-center justify-center group-hover:scale-110 transition-transform duration-1000">
                                <span class="text-blue-500/[0.07] font-black text-5xl rotate-12 tracking-widest">OFFICIAL</span>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-500 dark:text-gray-500 px-6 font-medium">
                            <div class="flex items-center gap-4">
                                <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-full border border-gray-200 dark:border-slate-700">Generated: {{ $this->salesAgreementDocument['generated_at'] ?? '' }}</span>
                                <span class="h-1 w-1 bg-gray-300 rounded-full"></span>
                                <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-full border border-gray-200 dark:border-slate-700">Ref: AG-{{ str_pad($this->record->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/10 text-emerald-600 dark:text-emerald-400 px-4 py-1.5 rounded-full border border-emerald-100 dark:border-emerald-900/30">
                                <x-filament::icon icon="heroicon-m-shield-check" class="w-4 h-4" />
                                <span class="font-bold">Electronically verified via {{ config('app.name') }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <style>
        .agreement-content h1, .agreement-content h2, .agreement-content h3 {
            color: #111827 !important;
        }
        .dark .agreement-content h1, .dark .agreement-content h2, .dark .agreement-content h3 {
            color: #f3f4f6 !important;
        }
        .agreement-content p, .agreement-content li {
            color: #374151 !important;
        }
        .dark .agreement-content p, .dark .agreement-content li {
            color: #d1d5db !important;
        }
        
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .shadow-lg { shadow: none; }
        }
    </style>
</x-filament-panels::page>
