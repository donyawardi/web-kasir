<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8" data-flash>
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 flex items-start justify-between rounded">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <p class="text-green-700 font-medium">{{ session('success') }}</p>
                            </div>
                            <button onclick="this.closest('[data-flash]').remove()" class="text-green-700">&times;</button>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8" data-flash>
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 flex items-start justify-between rounded">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <p class="text-red-700 font-medium">{{ session('error') }}</p>
                            </div>
                            <button onclick="this.closest('[data-flash]').remove()" class="text-red-700">&times;</button>
                        </div>
                    </div>
                @endif

                @isset($slot)
                    {{ $slot }}
                @endisset

                @yield('content')
            </main>
        </div>

        @stack('modals')

        <script>
            // auto-dismiss flash messages after 4s
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    document.querySelectorAll('[data-flash]').forEach(function (el) { el.remove(); });
                }, 4000);
            });
        </script>

        @livewireScripts
    </body>
</html>
