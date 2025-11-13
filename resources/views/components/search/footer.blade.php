<!-- Simple Footer for Search Pages -->
<footer class="bg-white border-t border-gray-200 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-linear-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-gray-900">HomeBaze</span>
                        <span class="text-xs text-emerald-600 font-medium block">PREMIUM</span>
                    </div>
                </div>
                <p class="text-gray-600 mb-4 max-w-md">
                    Nigeria's premier real estate platform. Find verified properties from trusted agents across all major cities.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-emerald-600 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 5.052 3.657 9.245 8.438 9.878v-6.988h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.245 20 15.052 20 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-emerald-600 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-emerald-600 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.017 2.906a.75.75 0 01.793.65l.01.117v3.803l-.001.117a.75.75 0 01-1.5-.117V4.809L8.354 7.774a.75.75 0 01-1.06-1.06l2.96-2.96H7.587a.75.75 0 01-.117-1.5L7.587 2.25h3.803l.117.001a.75.75 0 01.51.655zM4.5 6a2.25 2.25 0 00-2.25 2.25v7.5A2.25 2.25 0 004.5 18h7.5a2.25 2.25 0 002.25-2.25V12a.75.75 0 011.5 0v3.75A3.75 3.75 0 0112 19.5H4.5A3.75 3.75 0 01.75 15.75v-7.5A3.75 3.75 0 014.5 4.5H8.25a.75.75 0 010 1.5H4.5z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Browse</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('properties.search', ['listingType' => 'rent']) }}" class="text-gray-600 hover:text-emerald-600 transition-colors duration-200">For Rent</a></li>
                    <li><a href="{{ route('properties.search', ['listingType' => 'sale']) }}" class="text-gray-600 hover:text-emerald-600 transition-colors duration-200">For Sale</a></li>
                    <li><a href="{{ route('properties.search', ['listingType' => 'shortlet']) }}" class="text-gray-600 hover:text-emerald-600 transition-colors duration-200">Short Let</a></li>
                    <li><a href="{{ route('properties.search', ['isFeatured' => true]) }}" class="text-gray-600 hover:text-emerald-600 transition-colors duration-200">Featured Properties</a></li>
                </ul>
            </div>

            <!-- Locations -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Popular Cities</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('properties.search', ['q' => 'Lagos']) }}" class="text-gray-600 hover:text-emerald-600 transition-colors duration-200">Lagos</a></li>
                    <li><a href="{{ route('properties.search', ['q' => 'Abuja']) }}" class="text-gray-600 hover:text-emerald-600 transition-colors duration-200">Abuja</a></li>
                    <li><a href="{{ route('properties.search', ['q' => 'Port Harcourt']) }}" class="text-gray-600 hover:text-emerald-600 transition-colors duration-200">Port Harcourt</a></li>
                    <li><a href="{{ route('properties.search', ['q' => 'Kano']) }}" class="text-gray-600 hover:text-emerald-600 transition-colors duration-200">Kano</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-200 pt-8 mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm">
                    © {{ date('Y') }} HomeBaze. All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-500 hover:text-emerald-600 text-sm transition-colors duration-200">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-emerald-600 text-sm transition-colors duration-200">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-emerald-600 text-sm transition-colors duration-200">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</footer>
