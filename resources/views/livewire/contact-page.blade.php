<div class="min-h-screen bg-gray-50">
    <!-- Clean Header Section -->
    <section class="relative bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
                <a href="{{ route('landing') }}" wire:navigate class="hover:text-emerald-600 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="font-medium text-gray-900">Contact Us</span>
            </nav>

            <!-- Clean Header -->
            <div class="text-center">
                <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900 mb-2">
                    Get In <span class="text-emerald-600">Touch</span>
                </h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Have questions about properties, need assistance, or want to partner with us?
                    We're here to help you every step of the way.
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Success Message -->
        @if($success)
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-1 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-1 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2"
                class="mb-8 bg-emerald-50/80 backdrop-blur-xl border border-emerald-200/60 rounded-2xl p-6 shadow-xl"
            >
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-emerald-900">Message Sent Successfully!</h3>
                        <p class="text-emerald-700">Thank you for contacting us. We'll get back to you within 24 hours.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-20">
            
            <!-- Contact Form -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Send us a Message</h2>
                        <p class="text-gray-600 text-sm">We'd love to hear from you</p>
                    </div>
                </div>

                <form wire:submit.prevent="submit" class="space-y-6">
                    <!-- Inquiry Type -->
                    <div>
                        <label for="inquiry_type" class="block text-sm font-bold text-gray-900 mb-2">Inquiry Type</label>
                        <select wire:model="inquiry_type" id="inquiry_type" class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300">
                            <option value="general">General Inquiry</option>
                            <option value="property">Property Question</option>
                            <option value="agent">Agent Services</option>
                            <option value="partnership">Partnership</option>
                            <option value="support">Technical Support</option>
                        </select>
                    </div>

                    <!-- Name and Email Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-900 mb-2">Full Name *</label>
                            <input 
                                type="text" 
                                wire:model="name" 
                                id="name"
                                placeholder="Enter your full name"
                                class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300"
                            >
                            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-900 mb-2">Email Address *</label>
                            <input 
                                type="email" 
                                wire:model="email" 
                                id="email"
                                placeholder="Enter your email"
                                class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300"
                            >
                            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Phone and Subject Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-bold text-gray-900 mb-2">Phone Number *</label>
                            <input 
                                type="tel" 
                                wire:model="phone" 
                                id="phone"
                                placeholder="+234 801 234 5678"
                                class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300"
                            >
                            @error('phone') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-bold text-gray-900 mb-2">Subject *</label>
                            <input 
                                type="text" 
                                wire:model="subject" 
                                id="subject"
                                placeholder="What's this about?"
                                class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300"
                            >
                            @error('subject') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-bold text-gray-900 mb-2">Message *</label>
                        <textarea 
                            wire:model="message" 
                            id="message" 
                            rows="6"
                            placeholder="Tell us more about your inquiry..."
                            class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300 resize-none"
                        ></textarea>
                        @error('message') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 disabled:from-gray-400 disabled:to-gray-500 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:scale-100 shadow-lg hover:shadow-xl disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove>Send Message</span>
                        <span wire:loading class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-6">
                <!-- Quick Contact -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Quick Contact</h3>
                            <p class="text-gray-600 text-sm">Get in touch directly</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-blue-100/60 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Phone</p>
                                <p class="text-gray-600">+234 (0) 1 234 5678</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-emerald-100/60 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Email</p>
                                <p class="text-gray-600">hello@homebaze.ng</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-purple-100/60 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Business Hours</p>
                                <p class="text-gray-600">Mon - Fri: 8AM - 6PM</p>
                                <p class="text-gray-600">Sat: 9AM - 4PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white/80 backdrop-blur-2xl border border-gray-200/40 rounded-3xl p-8 shadow-2xl">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4v10a2 2 0 002 2h6a2 2 0 002-2V8M7 8H5a2 2 0 00-2 2v8a2 2 0 002 2h2m0-12h10m-5 4v4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Follow Us</h3>
                            <p class="text-gray-600">Stay connected on social media</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <a href="#" class="flex items-center justify-center space-x-2 bg-blue-50/60 hover:bg-blue-100/60 border border-blue-200/60 rounded-xl p-4 transition-all duration-300 hover:scale-105">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-blue-600">Facebook</span>
                        </a>

                        <a href="#" class="flex items-center justify-center space-x-2 bg-sky-50/60 hover:bg-sky-100/60 border border-sky-200/60 rounded-xl p-4 transition-all duration-300 hover:scale-105">
                            <svg class="w-5 h-5 text-sky-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                            <span class="font-semibold text-sky-600">Twitter</span>
                        </a>

                        <a href="#" class="flex items-center justify-center space-x-2 bg-pink-50/60 hover:bg-pink-100/60 border border-pink-200/60 rounded-xl p-4 transition-all duration-300 hover:scale-105">
                            <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-pink-600">Instagram</span>
                        </a>

                        <a href="#" class="flex items-center justify-center space-x-2 bg-blue-50/60 hover:bg-blue-100/60 border border-blue-200/60 rounded-xl p-4 transition-all duration-300 hover:scale-105">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-blue-600">LinkedIn</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Office Locations -->
        <div class="bg-white/80 backdrop-blur-2xl border border-gray-200/40 rounded-3xl p-8 shadow-2xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4">Our Office Locations</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">Visit us at any of our offices across Nigeria</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($officeLocations as $office)
                    <div class="bg-white/60 backdrop-blur-xl border border-gray-200/40 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900">{{ $office['city'] }}</h4>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p class="text-gray-600">{{ $office['address'] }}</p>
                            </div>

                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <p class="text-gray-600">{{ $office['phone'] }}</p>
                            </div>

                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600">{{ $office['email'] }}</p>
                            </div>

                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-600">{{ $office['hours'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>