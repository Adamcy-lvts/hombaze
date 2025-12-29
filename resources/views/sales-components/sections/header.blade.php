<div class="border-b-4 border-double border-gray-200 dark:border-gray-700 pb-8 mb-12">
    <div class="flex flex-col items-center space-y-4">
        <div class="w-20 h-20 bg-linear-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-xl flex items-center justify-center transform -rotate-3 hover:rotate-3 transition-transform duration-300">
            <x-application-logo class="w-12 h-12 text-white" />
        </div>
        <div class="text-center">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight uppercase">Sales Agreement</h1>
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
