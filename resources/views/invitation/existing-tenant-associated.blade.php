<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Invitation Accepted</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
            <!-- Success Icon -->
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mb-2">Successfully Associated!</h2>
            
            <p class="text-gray-600 mb-4">
                Your existing tenant account has been successfully associated with {{ $invitation->landlord->name }}.
            </p>

            @if($invitation->property)
                <div class="bg-blue-50 p-3 rounded-lg mb-4">
                    <p class="text-sm text-blue-800">
                        <strong>Property:</strong> {{ $invitation->property->title }}
                    </p>
                </div>
            @endif

            @if($invitation->message)
                <div class="bg-gray-50 p-3 rounded-lg mb-4">
                    <p class="text-sm text-gray-700 italic">
                        "{{ $invitation->message }}"
                    </p>
                </div>
            @endif

            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-800">
                    You can now access your tenant dashboard and communicate with your landlord through the platform.
                </p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('filament.tenant.pages.dashboard') }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 inline-block">
                    Go to Tenant Dashboard
                </a>
                <a href="/" class="w-full bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition duration-200 inline-block">
                    Return to Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html>