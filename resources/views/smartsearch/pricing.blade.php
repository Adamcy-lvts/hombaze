@extends('layouts.guest-app')

@section('content')
<style>
    .clean-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease-in-out;
    }
    .clean-card:hover {
        border-color: #d1d5db;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transform: translateY(-2px);
    }
    .premium-text {
        color: #059669;
    }
    .vip-text {
        color: #7c3aed;
    }
</style>

<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-3 tracking-tight">
                Smart<span class="text-emerald-600">Search</span>
            </h1>
            <p class="text-sm md:text-base text-gray-500 max-w-xl mx-auto leading-relaxed">
                Automated property hunting working 24/7 to find your match.
            </p>
            
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <div class="flex items-center text-gray-600 text-xs font-medium bg-white px-3 py-1.5 rounded-full border border-gray-200">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    No Hidden Fees
                </div>
                <div class="flex items-center text-gray-600 text-xs font-medium bg-white px-3 py-1.5 rounded-full border border-gray-200">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Auto-Unlocked
                </div>
                <div class="flex items-center text-gray-600 text-xs font-medium bg-white px-3 py-1.5 rounded-full border border-gray-200">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    24/7 Hunting
                </div>
            </div>
        </div>

        @if (session()->has('error'))
            <div class="mb-8 bg-red-50 border border-red-100 px-4 py-3 rounded-lg text-red-700 text-sm max-w-xl mx-auto flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('success'))
            <div class="mb-8 bg-emerald-50 border border-emerald-100 px-4 py-3 rounded-lg text-emerald-700 text-sm max-w-xl mx-auto flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Current Status -->
        @auth
            @if ($activePurchases->isNotEmpty())
                <div class="mb-12 bg-white rounded-xl shadow-sm border border-gray-200 p-5 max-w-2xl mx-auto hidden md:block">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-900">Your Plans</h3>
                        <a href="{{ route('customer.searches.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Manage</a>
                    </div>
                    <div class="space-y-3">
                        @foreach ($activePurchases as $purchase)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center text-gray-700 font-bold text-xs">
                                        {{ substr($purchase->getTierName(), 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">{{ $purchase->getTierName() }}</div>
                                        <div class="text-gray-500 text-xs">
                                            {{ $purchase->getRemainingSearches() }} searches left
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-gray-500">
                                    {{ $purchase->getDaysRemaining() }} Days
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endauth

        <!-- Pricing Cards -->
        <div class="grid gap-5 lg:grid-cols-4 md:grid-cols-2 lg:items-end">
            @foreach ($tiers as $tier)
                @php
                    $isVip = $tier['value'] === 'vip';
                    $isPopular = $tier['value'] === 'standard';
                    $isPriority = $tier['value'] === 'priority';
                @endphp
                <div class="flex flex-col h-full relative group">
                    @if ($isVip)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-10 w-full text-center">
                            <span class="bg-purple-600 text-white px-3 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide shadow-sm">The Ultimate</span>
                        </div>
                    @elseif ($isPopular)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-10 w-full text-center">
                            <span class="bg-emerald-600 text-white px-3 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide shadow-sm">Popular</span>
                        </div>
                    @endif

                    <div class="flex-1 bg-white rounded-2xl p-5 flex flex-col transition-all duration-300 border hover:border-gray-300 hover:shadow-md {{ $isVip ? 'border-purple-200 shadow-sm' : ($isPopular ? 'border-emerald-200 shadow-sm' : 'border-gray-200') }}">
                        
                        <div class="mb-4">
                            <h3 class="text-lg font-bold {{ $isVip ? 'text-purple-700' : ($isPopular ? 'text-emerald-700' : 'text-gray-900') }}">{{ $tier['label'] }}</h3>
                            <p class="text-gray-500 mt-1 text-xs leading-relaxed">{{ $tier['description'] }}</p>
                        </div>

                        <div class="mb-6 flex items-baseline">
                            <span class="text-3xl font-bold text-gray-900 tracking-tight">{{ $tier['formatted_price'] }}</span>
                            <span class="text-gray-400 font-medium text-xs ml-1">/one-time</span>
                        </div>

                        <div class="flex-1">
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-center text-gray-700 text-sm">
                                    <svg class="h-4 w-4 mr-2.5 {{ $isVip ? 'text-purple-500' : ($isPopular ? 'text-emerald-500' : 'text-gray-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>
                                        @if ($tier['searches'] >= 999)
                                            <span class="font-bold">Unlimited</span> searches
                                        @else
                                            <span class="font-bold">{{ $tier['searches'] }}</span> {{ Str::plural('search', $tier['searches']) }}
                                        @endif
                                    </span>
                                </li>
                                <li class="flex items-center text-gray-700 text-sm">
                                    <svg class="h-4 w-4 mr-2.5 {{ $isVip ? 'text-purple-500' : ($isPopular ? 'text-emerald-500' : 'text-gray-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span><span class="font-bold">{{ $tier['duration_days'] }}</span> days active</span>
                                </li>
                                <li class="flex items-center text-gray-700 text-sm">
                                    <svg class="h-4 w-4 mr-2.5 {{ $isVip ? 'text-purple-500' : ($isPopular ? 'text-emerald-500' : 'text-gray-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <span class="text-xs">
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
                                    <li class="flex items-center text-purple-700 bg-purple-50 p-2 rounded-lg text-xs font-semibold">
                                        <svg class="h-4 w-4 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        3-Hr Priority Access
                                    </li>
                                @endif
                            </ul>
                        </div>

                        @auth
                            <form method="POST" action="{{ route('smartsearch.purchase', $tier['value']) }}">
                                @csrf
                                <button type="submit" class="w-full py-2.5 rounded-lg font-bold text-sm transition-colors {{ $isVip ? 'bg-purple-600 hover:bg-purple-700 text-white' : ($isPopular ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-900 hover:bg-black text-white') }}">
                                    Select
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login', ['redirect' => route('smartsearch.pricing')]) }}" class="block w-full text-center py-2.5 rounded-lg font-bold text-sm transition-colors {{ $isVip ? 'bg-purple-600 hover:bg-purple-700 text-white' : ($isPopular ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-900 hover:bg-black text-white') }}">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        <!-- How It Works (Simplified) -->
        <div class="mt-20">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">How It Works</h2>
            <div class="grid gap-6 md:grid-cols-3">
                <div class="bg-white p-6 rounded-xl border border-gray-200">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 font-bold mb-4">1</div>
                    <h3 class="font-bold text-gray-900 mb-2">Select Plan</h3>
                    <p class="text-sm text-gray-500">Choose a one-time pass that fits your needs. Our flexible tiers offer varying search limits and durations, perfect for both casual browsers and urgent home seekers.</p>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold mb-4">2</div>
                    <h3 class="font-bold text-gray-900 mb-2">Set Criteria</h3>
                    <p class="text-sm text-gray-500">Tell us exactly what you are looking for. Define your location, budget, and property preferences, and let our advanced filters target the best options available.</p>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold mb-4">3</div>
                    <h3 class="font-bold text-gray-900 mb-2">Get Matches</h3>
                    <p class="text-sm text-gray-500">Receive instant alerts before listing goes public. You'll get notified via Email, WhatsApp, or SMS the second a matching property hits the market, giving you a head start.</p>
                </div>
            </div>
        </div>

        <!-- VIP Spotlight (Clean) -->
        <div class="mt-16 bg-white border border-gray-200 rounded-2xl p-6 md:p-10 flex flex-col md:flex-row items-center gap-8">
             <div class="flex-1">
                 <div class="inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide mb-4">VIP Benefit</div>
                 <h2 class="text-2xl font-bold text-gray-900 mb-3">Be First. Win the Property.</h2>
                 <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                     The <span class="font-bold text-gray-900">3-Hour First Dibs</span> window gives you a critical headstart to view properties and contact agents before standard users are even notified.
                 </p>
                 <ul class="space-y-2">
                     <li class="flex items-center text-sm text-gray-700">
                         <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                         See photos 3 hours early
                     </li>
                     <li class="flex items-center text-sm text-gray-700">
                         <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                         Priority agent contact
                     </li>
                 </ul>
             </div>
             <div class="w-full md:w-1/3 bg-gray-50 rounded-xl p-6 text-center border border-gray-100">
                 <div class="text-4xl font-bold text-purple-600 mb-2">3 HR</div>
                 <div class="text-sm font-bold text-gray-900">Head Start</div>
                 <div class="text-xs text-gray-500 mt-1">Exclusive Priority Access</div>
             </div>
        </div>

        <!-- FAQ (Clean) -->
        <div class="mt-20 max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">FAQ</h2>
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200">
                    <h3 class="font-bold text-gray-900 text-sm mb-2">What happens if no matches are found?</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">If your search period expires without matches, we offer a 30-day free extension.</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-200">
                    <h3 class="font-bold text-gray-900 text-sm mb-2">Can I modify criteria?</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Yes, you can update your filters anytime from your dashboard.</p>
                </div>
            </div>
        </div>
        
        <!-- Footer CTA -->
        <div class="mt-16 text-center pb-12">
            @guest
                <a href="{{ route('register') }}" class="inline-block px-8 py-3 rounded-xl bg-gray-900 text-white font-bold text-sm hover:bg-black transition-transform hover:scale-105">
                    Start Your Search
                </a>
            @endguest
        </div>
    </div>
</div>
@endsection
