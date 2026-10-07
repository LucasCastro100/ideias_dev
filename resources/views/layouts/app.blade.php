<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Icones -->

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased dark">
    <x-toast />

    <div class="h-screen bg-gray-950 flex flex-col overflow-hidden">
        <div class="fixed top-0 left-0 right-0 z-30">
            @include('navigation-menu')
        </div>

        <div class="flex-1 pt-16 overflow-y-auto">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-gray-900 shadow">
                    <div class="max-w-7xl mx-auto p-4">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('modals')

    @livewireScripts

    @if (str_contains(request()->getHost(), 'ideias.dev.br'))
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/5534991535839" target="_blank" rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110">
        <i class="fab fa-whatsapp text-3xl"></i>
    </a>
    @endif
</body>

</html>
