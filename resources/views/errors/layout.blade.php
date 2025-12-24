<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'HomeBaze'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 px-4 py-10">
            <div class="w-full max-w-lg rounded-2xl bg-white shadow-lg px-8 py-10 text-center">
                <a href="/" class="inline-flex items-center justify-center">
                    <x-application-logo class="h-16 w-16 fill-current text-gray-400" />
                </a>

                <div class="mt-6 text-sm font-semibold uppercase tracking-widest text-gray-400">
                    @yield('code')
                </div>
                <h1 class="mt-2 text-2xl font-semibold text-gray-900">
                    @yield('message')
                </h1>
                <p class="mt-3 text-sm text-gray-500">
                    @yield('detail')
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="/" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                        Go to homepage
                    </a>
                    <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Go back
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
