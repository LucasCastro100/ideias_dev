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
        <link href="https://fonts.bunny.net/css?family=fredoka:600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="dark">
        <div class="font-sans antialiased bg-gray-950 text-gray-100">
            {{ $slot }}
        </div>

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
