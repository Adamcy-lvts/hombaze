<div class="relative overflow-hidden -mt-8 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-12">
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(31, 41, 55, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .premium-gradient-text {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .vip-gradient-text {
            background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hover-scale {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .hover-scale:hover {
            transform: scale(1.02) translateY(-3px);
        }
        .bg-blob {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.15;
            pointer-events: none;
        }
        .dark .bg-blob {
            opacity: 0.1;
        }
    </style>

    <!-- Background Blobs -->
    <div class="bg-blob bg-emerald-300 top-[-5%] left-[-5%] dark:bg-emerald-900"></div>
    <div class="bg-blob bg-purple-300 bottom-[-5%] right-[-5%] dark:bg-purple-900"></div>
    <div class="bg-blob bg-blue-300 top-[20%] right-[10%] dark:bg-blue-900"></div>

    <div class="relative z-10 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white mb-4 tracking-tight">
                Simple, Transparent <span class="premium-gradient-text">Pricing</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Choose the right bundle for your property listing needs.
            </p>
        </div>

        @if (session()->has('error'))
            <div class="mb-8 glass-card rounded-xl border-red-200 bg-red-50/50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3 text-red-800 dark:text-red-300 max-w-xl mx-auto flex items-center shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('message'))
            <div class="mb-8 glass-card rounded-xl border-emerald-200 bg-emerald-50/50 dark:bg-emerald-900/20 dark:border-emerald-800 px-4 py-3 text-emerald-800 dark:text-emerald-300 max-w-xl mx-auto flex items-center shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- Section 2: For Agents & Landlords (Listing Bundles) -->
        <div class="mb-20">
            <div class="flex items-center justify-center mb-8">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md px-5 py-1.5 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                        <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 p-1 rounded-md mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        For Sellers & Landlords
                    </h2>
                </div>
            </div>
            <div class="text-center mb-8 -mt-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-lg mx-auto">
                    Maximize your exposure. Purchase listing credits to publish your properties and feature them to reach thousands of potential buyers instantly.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-4 md:grid-cols-2">
                @foreach ($packages as $package)
                    @php
                        $isCurrent = $currentPackageSlug === $package->slug;
                    @endphp
                    <div class="flex flex-col h-full bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-sm border {{ $isCurrent ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-[0_10px_30px_-10px_rgba(16,185,129,0.3)]' : 'border-gray-200 dark:border-gray-700' }} overflow-hidden hover:shadow-lg transition-all duration-300 relative group">
                        @if ($package->price == 0)
                            <div class="absolute top-0 right-0 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 text-[10px] font-bold px-2 py-1 rounded-bl-lg">FREE</div>
                        @elseif ($package->is_featured ?? false)
                             <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg">POPULAR</div>
                        @endif

                        @if ($isCurrent)
                            <div class="absolute top-0 left-0 bg-emerald-500 text-white text-[10px] font-black px-3 py-1 rounded-br-lg shadow-sm">ACTIVE PLAN</div>
                        @endif

                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $package->name }}</h3>
                            <div class="mb-5">
                                <span class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white">₦{{ number_format($package->price, 0) }}</span>
                                @if ($package->price > 0)
                                    <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">/bundle</span>
                                @endif
                            </div>

                            <ul class="space-y-3 mb-6 flex-1">
                                <li class="flex items-center bg-gray-50 dark:bg-gray-700/50 p-2 rounded-lg">
                                    <div class="w-6 h-6 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center mr-2 shadow-sm border border-gray-100 dark:border-gray-700 text-green-500">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="font-bold text-sm text-gray-700 dark:text-gray-300">{{ $package->listing_credits }}</span> <span class="text-gray-500 dark:text-gray-400 text-xs ml-1">Listings</span>
                                </li>
                                <li class="flex items-center bg-gray-50 dark:bg-gray-700/50 p-2 rounded-lg">
                                    <div class="w-6 h-6 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center mr-2 shadow-sm border border-gray-100 dark:border-gray-700 text-green-500">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="font-bold text-sm text-gray-700 dark:text-gray-300">{{ $package->featured_credits }}</span> <span class="text-gray-500 dark:text-gray-400 text-xs ml-1">Featured</span>
                                </li>
                                @if ($package->max_active_listing_credits)
                                    <li class="flex items-center bg-blue-50 dark:bg-blue-900/20 p-2 rounded-lg border border-blue-100 dark:border-blue-800">
                                        <div class="w-6 h-6 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center mr-2 shadow-sm border border-blue-100 dark:border-blue-800 text-blue-500">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <span class="text-xs font-medium text-blue-800 dark:text-blue-300">Max {{ $package->max_active_listing_credits }} Active</span>
                                    </li>
                                @endif
                            </ul>

                            @if ($isCurrent)
                                <button disabled class="w-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold py-3 px-4 rounded-xl shadow-sm text-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Current Plan
                                </button>
                            @else
                                <form method="POST" action="{{ route('listing-bundles.purchase', ['type' => 'package', 'slug' => $package->slug]) }}">
                                    @csrf
                                    <button type="submit" class="w-full bg-gray-900 dark:bg-gray-700 text-white font-bold py-3 px-4 rounded-xl hover:bg-black dark:hover:bg-gray-600 transition-all transform hover:scale-[1.02] shadow-md text-sm">
                                        Select
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Add-ons -->
            <div class="mt-16">
                <div class="text-center mb-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Add-on Credits</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-lg mx-auto">
                        Need a little boost? Top up your listing credits or use <span class="font-bold text-gray-700 dark:text-gray-300">Featured Credits</span> to push your property to the top.
                    </p>
                </div>
                <div class="grid gap-4 lg:grid-cols-3 md:grid-cols-2 max-w-4xl mx-auto">
                    @foreach ($addons as $addon)
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors flex items-center justify-between group">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $addon->name }}</h4>
                                <p class="text-gray-500 dark:text-gray-400 text-[10px] mt-0.5 font-medium bg-gray-50 dark:bg-gray-700 inline-block px-1.5 py-0.5 rounded">
                                    @if($addon->listing_credits > 0) {{ $addon->listing_credits }} LISTING @endif
                                    @if($addon->featured_credits > 0) {{ $addon->featured_credits }} FEATURED @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-gray-900 dark:text-white">₦{{ number_format($addon->price, 0) }}</p>
                                <form method="POST" action="{{ route('listing-bundles.purchase', ['type' => 'addon', 'slug' => $addon->slug]) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 underline decoration-2 underline-offset-2">
                                        Purchase
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="mt-24 grid gap-8 rounded-3xl glass-card p-8 md:p-12 text-center md:grid-cols-3">
            <div>
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h5 class="font-bold text-gray-900 dark:text-white">No Hidden Fees</h5>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Transparent pricing with no recurring monthly subscriptions.</p>
            </div>
            <div>
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h5 class="font-bold text-gray-900 dark:text-white">Instant Activation</h5>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Credits are available as soon as your payment is confirmed.</p>
            </div>
            <div>
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-.382-3.040z"></path></svg>
                </div>
                <h5 class="font-bold text-gray-900 dark:text-white">Secure Payments</h5>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">All transactions are processed through encrypted gateways.</p>
            </div>
        </div>
    </div>
</div>

