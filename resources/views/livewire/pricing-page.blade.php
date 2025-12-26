<div class="min-h-screen bg-[#fcfdfd] overflow-hidden">
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
    </style>

    <!-- Background Blobs -->
    <div class="bg-blob bg-emerald-300 top-[-10%] left-[-10%]"></div>
    <div class="bg-blob bg-purple-300 bottom-[-10%] right-[-10%]"></div>
    <div class="bg-blob bg-blue-300 top-[20%] right-[10%]"></div>

    <div class="relative max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 z-10">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 mb-3 tracking-tight">
                Simple, Transparent <span class="premium-gradient-text">Pricing</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Whether you're looking for your dream home or listing it, we have a plan for you.
            </p>
        </div>

        @if (session()->has('error'))
            <div class="mb-8 glass-card rounded-xl border-red-200 bg-red-50/50 px-4 py-3 text-red-800 max-w-xl mx-auto flex items-center shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('message') || session()->has('success'))
            <div class="mb-8 glass-card rounded-xl border-emerald-200 bg-emerald-50/50 px-4 py-3 text-emerald-800 max-w-xl mx-auto flex items-center shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('message') ?? session('success') }}
            </div>
        @endif

        <!-- Section 1: For Home Seekers (Smart Search) -->
        <div class="mb-16">
            <div class="flex items-center justify-center mb-8">
                <div class="bg-white/80 backdrop-blur-md px-5 py-1.5 rounded-full border border-gray-200 shadow-sm">
                    <h2 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="bg-emerald-100 text-emerald-600 p-1 rounded-md mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        For Buyers & Tenants
                    </h2>
                </div>
            </div>
            <div class="text-center mb-8 -mt-4">
                <p class="text-sm text-gray-500 max-w-lg mx-auto">
                    Automate your property hunt. Get instant alerts for new matches and secure VIP access to view properties before anyone else.
                </p>
            </div>

            <div class="grid gap-5 lg:grid-cols-4 md:grid-cols-2 lg:items-end">
                @foreach ($tiers as $tier)
                    @php
                        $isVip = $tier['value'] === 'vip';
                        $isPopular = $tier['value'] === 'standard';
                        $isPriority = $tier['value'] === 'priority';
                    @endphp
                    <div class="flex flex-col h-full relative group {{ $isPopular || $isVip ? 'animate-float' : '' }}" style="{{ $isVip ? 'animation-delay: -1s' : ($isPopular ? 'animation-delay: -3s' : '') }}">
                        @if ($isVip)
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10 w-full text-center">
                                <span class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-3 py-1 rounded-full text-[10px] md:text-xs font-black whitespace-nowrap shadow-lg ring-2 ring-white">👑 THE ULTIMATE</span>
                            </div>
                        @elseif ($isPopular)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-10 w-full text-center">
                                <span class="bg-emerald-600 text-white px-3 py-1 rounded-full text-[10px] md:text-xs font-black whitespace-nowrap shadow-lg ring-2 ring-white">MOST POPULAR</span>
                            </div>
                        @endif

                        <div class="flex-1 glass-card rounded-2xl md:rounded-3xl p-5 md:p-6 flex flex-col hover-scale transition-all duration-500 {{ $isVip ? 'border-purple-200 ring-2 ring-purple-100 shadow-[0_10px_30px_-10px_rgba(124,58,237,0.3)]' : ($isPopular ? 'border-emerald-200 ring-2 ring-emerald-100 shadow-[0_10px_30px_-10px_rgba(16,185,129,0.3)]' : ($isPriority ? 'border-blue-200 shadow-md' : 'shadow-sm')) }}">
                            
                            <div class="mb-4">
                                <h3 class="text-lg md:text-xl font-black {{ $isVip ? 'vip-gradient-text' : ($isPopular ? 'premium-gradient-text' : 'text-gray-900') }}">{{ $tier['label'] }}</h3>
                                <p class="text-gray-500 mt-1 text-xs leading-relaxed">{{ Str::limit($tier['description'], 60) }}</p>
                            </div>

                            <div class="mb-6 flex items-baseline">
                                <span class="text-2xl md:text-4xl font-black text-gray-900 tracking-tight">{{ $tier['formatted_price'] }}</span>
                                <span class="text-gray-400 font-bold text-[10px] md:text-xs ml-1">/one-time</span>
                            </div>

                            <div class="flex-1">
                                <ul class="space-y-3 md:space-y-3 mb-6">
                                    <li class="flex items-center font-semibold text-gray-700">
                                        <div class="w-6 h-6 rounded-full {{ $isVip ? 'bg-purple-100' : ($isPopular ? 'bg-emerald-100' : 'bg-gray-100') }} flex items-center justify-center mr-2.5 flex-shrink-0 transition-colors group-hover:bg-opacity-80">
                                            <svg class="h-3 w-3 {{ $isVip ? 'text-purple-600' : ($isPopular ? 'text-emerald-600' : 'text-gray-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <span class="text-xs md:text-sm">
                                            @if ($tier['searches'] >= 999)
                                                <span class="text-base font-bold">Unlimited</span> searches
                                            @else
                                                <span class="text-base font-bold">{{ $tier['searches'] }}</span> {{ Str::plural('search', $tier['searches']) }}
                                            @endif
                                        </span>
                                    </li>
                                    <li class="flex items-center font-semibold text-gray-700">
                                        <div class="w-6 h-6 rounded-full {{ $isVip ? 'bg-purple-100' : ($isPopular ? 'bg-emerald-100' : 'bg-gray-100') }} flex items-center justify-center mr-2.5 flex-shrink-0 transition-colors group-hover:bg-opacity-80">
                                            <svg class="h-3 w-3 {{ $isVip ? 'text-purple-600' : ($isPopular ? 'text-emerald-600' : 'text-gray-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <span class="text-xs md:text-sm"><span class="text-base font-bold">{{ $tier['duration_days'] }}</span> days active</span>
                                    </li>
                                    <li class="flex items-center font-semibold text-gray-700">
                                        <div class="w-6 h-6 rounded-full {{ $isVip ? 'bg-purple-100' : ($isPopular ? 'bg-emerald-100' : 'bg-gray-100') }} flex items-center justify-center mr-2.5 flex-shrink-0 transition-colors group-hover:bg-opacity-80">
                                            <svg class="h-3 w-3 {{ $isVip ? 'text-purple-600' : ($isPopular ? 'text-emerald-600' : 'text-gray-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        </div>
                                        <span class="text-[10px] md:text-xs">
                                            @if (in_array('whatsapp', $tier['channels']) && in_array('sms', $tier['channels']))
                                                Email, WhatsApp & SMS
                                            @elseif (in_array('whatsapp', $tier['channels']))
                                                Email & WhatsApp
                                            @else
                                                Email Alerts
                                            @endif
                                        </span>
                                    </li>
                                    @if ($isVip)
                                        <li class="flex items-center font-black text-purple-700 bg-purple-50 p-2 rounded-xl border border-purple-100">
                                            <div class="w-6 h-6 rounded-full bg-purple-600 flex items-center justify-center mr-2.5 flex-shrink-0 shadow-sm">
                                                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            </div>
                                            <span class="text-[10px] md:text-xs">3-Hr Priority Access</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <form method="POST" action="{{ route('smartsearch.purchase', $tier['value']) }}">
                                @csrf
                                <button type="submit" class="w-full relative overflow-hidden group/btn px-4 py-3 rounded-xl font-bold text-sm md:text-base transition-all transform hover:scale-[1.02] active:scale-95 {{ $isVip ? 'bg-purple-600 text-white' : ($isPopular ? 'bg-emerald-600 text-white' : 'bg-gray-900 text-white') }}">
                                    <span class="relative z-10 flex items-center justify-center">
                                        Select
                                        <svg class="w-4 h-4 ml-1.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section 2: For Agents & Landlords (Listing Bundles) -->
        <div>
            <div class="flex items-center justify-center mb-8">
                <div class="bg-white/80 backdrop-blur-md px-5 py-1.5 rounded-full border border-gray-200 shadow-sm">
                    <h2 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="bg-blue-100 text-blue-600 p-1 rounded-md mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        For Sellers & Landlords
                    </h2>
                </div>
            </div>
            <div class="text-center mb-8 -mt-4">
                <p class="text-sm text-gray-500 max-w-lg mx-auto">
                    Maximize your exposure. Purchase listing credits to publish your properties and feature them to reach thousands of potential buyers instantly.
                </p>
            </div>

            <div class="grid gap-5 lg:grid-cols-4 md:grid-cols-2">
                @foreach ($packages as $package)
                    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 relative group">
                        @if ($package->price == 0)
                            <div class="absolute top-0 right-0 bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded-bl-lg">FREE</div>
                        @elseif ($package->is_featured ?? false)
                             <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg">POPULAR</div>
                        @endif

                        <div class="p-5 md:p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $package->name }}</h3>
                            <div class="mb-5">
                                <span class="text-2xl md:text-3xl font-black text-gray-900">₦{{ number_format($package->price, 0) }}</span>
                                @if ($package->price > 0)
                                    <span class="text-gray-500 text-xs font-medium">/bundle</span>
                                @endif
                            </div>

                            <ul class="space-y-3 mb-6">
                                <li class="flex items-center bg-gray-50 p-2 rounded-lg">
                                    <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center mr-2 shadow-sm border border-gray-100 text-green-500">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="font-bold text-sm text-gray-700">{{ $package->listing_credits }}</span> <span class="text-gray-500 text-xs ml-1">Listings</span>
                                </li>
                                <li class="flex items-center bg-gray-50 p-2 rounded-lg">
                                    <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center mr-2 shadow-sm border border-gray-100 text-green-500">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="font-bold text-sm text-gray-700">{{ $package->featured_credits }}</span> <span class="text-gray-500 text-xs ml-1">Featured</span>
                                </li>
                                @if ($package->max_active_listing_credits)
                                    <li class="flex items-center bg-blue-50 p-2 rounded-lg border border-blue-100">
                                        <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center mr-2 shadow-sm border border-blue-100 text-blue-500">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <span class="text-xs font-medium text-blue-800">Max {{ $package->max_active_listing_credits }} Active</span>
                                    </li>
                                @endif
                            </ul>

                            <form method="POST" action="{{ route('listing-bundles.purchase', ['type' => 'package', 'slug' => $package->slug]) }}">
                                @csrf
                                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3 px-4 rounded-xl hover:bg-black transition-all transform hover:scale-[1.02] shadow-md text-sm">
                                    Select
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Add-ons -->
            <div class="mt-16">
                <div class="text-center mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Add-on Credits</h3>
                    <p class="text-sm text-gray-500 max-w-lg mx-auto">
                        Need a little boost? Top up your listing credits or use <span class="font-bold text-gray-700">Featured Credits</span> to push your property to the top of search results and homepage for maximum visibility.
                    </p>
                </div>
                <div class="grid gap-4 lg:grid-cols-3 md:grid-cols-2 max-w-4xl mx-auto">
                    @foreach ($addons as $addon)
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:border-emerald-200 transition-colors flex items-center justify-between group">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $addon->name }}</h4>
                                <p class="text-gray-500 text-[10px] mt-0.5 font-medium bg-gray-50 inline-block px-1.5 py-0.5 rounded">
                                    @if($addon->listing_credits > 0) {{ $addon->listing_credits }} LISTING @endif
                                    @if($addon->featured_credits > 0) {{ $addon->featured_credits }} FEATURED @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-gray-900">₦{{ number_format($addon->price, 0) }}</p>
                                <form method="POST" action="{{ route('listing-bundles.purchase', ['type' => 'addon', 'slug' => $addon->slug]) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 underline decoration-2 underline-offset-2">
                                        Purchase
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
