<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-16 text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">
                Simple, Transparent Pricing
            </h1>
            <p class="mt-4 text-xl text-gray-600">
                Choose the right bundle for your property listing needs.
            </p>
        </div>

        @if (session()->has('error'))
            <div class="mb-8 rounded-2xl border border-red-100 bg-red-50 px-6 py-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif
        @if (session()->has('message'))
            <div class="mb-8 rounded-2xl border border-emerald-100 bg-emerald-50 px-6 py-4 text-sm text-emerald-700">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($packages as $package)
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="p-8">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">{{ $package->name }}</h3>
                            <div class="flex items-center gap-2">
                                @if ($currentPackageSlug === $package->slug)
                                    <span class="rounded bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800">Current</span>
                                @endif
                                @if ($package->price <= 0)
                                    <span class="rounded bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Free</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-gray-900">₦{{ number_format($package->price, 0) }}</span>
                            @if ($package->price > 0)
                                <span class="text-sm text-gray-500">/bundle</span>
                            @endif
                        </div>

                        <ul class="mb-8 space-y-4">
                            <li class="flex items-start">
                                <svg class="mr-2 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-600">{{ $package->listing_credits }} Listing Credits</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="mr-2 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-600">{{ $package->featured_credits }} Featured Credits</span>
                            </li>
                            @if ($package->max_active_listing_credits)
                                <li class="flex items-start">
                                    <svg class="mr-2 h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-gray-600">Max {{ $package->max_active_listing_credits }} Active Listings</span>
                                </li>
                            @endif
                        </ul>

                        <form method="POST" action="{{ route('listing-bundles.purchase', ['type' => 'package', 'slug' => $package->slug]) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 font-bold text-white transition-colors hover:bg-emerald-700">
                                Select {{ $package->name }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-24">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-900">Featured Add-ons</h2>
                <p class="mt-2 text-lg text-gray-600">Need a little extra boost? Get individual credits.</p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($addons as $addon)
                    <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">{{ $addon->name }}</h4>
                            <p class="text-sm text-gray-500">
                                @if ($addon->listing_credits > 0) {{ $addon->listing_credits }} Listing @endif
                                @if ($addon->featured_credits > 0) {{ $addon->featured_credits }} Featured @endif
                                Credits
                            </p>
                            <p class="mt-1 font-bold text-emerald-600">₦{{ number_format($addon->price, 0) }}</p>
                        </div>
                        <form method="POST" action="{{ route('listing-bundles.purchase', ['type' => 'addon', 'slug' => $addon->slug]) }}">
                            @csrf
                            <button type="submit" class="rounded-lg bg-gray-900 px-6 py-2 font-semibold text-white transition-colors hover:bg-gray-800">
                                Buy
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-24 grid gap-8 rounded-3xl border border-gray-100 bg-white p-12 text-center md:grid-cols-3">
            <div>
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h5 class="font-bold text-gray-900">No Hidden Fees</h5>
                <p class="mt-2 text-sm text-gray-500">Transparent pricing with no recurring monthly subscriptions.</p>
            </div>
            <div>
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h5 class="font-bold text-gray-900">Instant Activation</h5>
                <p class="mt-2 text-sm text-gray-500">Credits are available as soon as your payment is confirmed.</p>
            </div>
            <div>
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-.382-3.040z"></path></svg>
                </div>
                <h5 class="font-bold text-gray-900">Secure Payments</h5>
                <p class="mt-2 text-sm text-gray-500">All transactions are processed through encrypted gateways.</p>
            </div>
        </div>
    </div>
</div>
