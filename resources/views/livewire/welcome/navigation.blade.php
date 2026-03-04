<nav class="-mx-3 flex flex-1 justify-end">
    @auth
        @if(auth()->user()->isCustomer())
            <a
                href="{{ route('dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Dashboard
            </a>
        @elseif(auth()->user()->isAgent())
            <a
                href="{{ route('filament.agent.pages.dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Agent Dashboard
            </a>
        @elseif(auth()->user()->isPropertyOwner())
            <a
                href="{{ route('filament.property-owner.pages.dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Landlord Dashboard
            </a>
        @else
            <a
                href="{{ url('/dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Dashboard
            </a>
        @endif
        
        <a
            href="{{ route('listing.create') }}"
            class="ml-4 group flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white pl-4 pr-1.5 py-1.5 rounded-2xl transition-all shadow-lg shadow-emerald-600/20 hover:shadow-xl hover:shadow-emerald-600/30 hover:-translate-y-0.5"
        >
            <span class="font-bold text-sm">List Property</span>
            <div class="bg-white/20 rounded-xl p-1.5 group-hover:bg-white/30 transition-colors">
                <x-heroicon-m-plus class="w-4 h-4" />
            </div>
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
            Log in
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Register
            </a>
        @endif
        
        <a
            href="{{ route('listing.create') }}"
            class="ml-4 group flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white pl-4 pr-1.5 py-1.5 rounded-2xl transition-all shadow-lg shadow-emerald-600/20 hover:shadow-xl hover:shadow-emerald-600/30 hover:-translate-y-0.5"
        >
            <span class="font-bold text-sm">List Property</span>
            <div class="bg-white/20 rounded-xl p-1.5 group-hover:bg-white/30 transition-colors">
                <x-heroicon-m-plus class="w-4 h-4" />
            </div>
        </a>
    @endauth
</nav>
