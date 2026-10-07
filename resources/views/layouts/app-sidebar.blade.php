<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('styles')
</head>

<body class="font-sans antialiased dark">
    <x-toast />

    <div class="fixed top-0 left-0 right-0 z-30">
        @include('navigation-menu')
    </div>

    <div x-data="{
        sidebarCollapsed: JSON.parse(localStorage.getItem('sidebarCollapsed') || 'false'),
        sidebarOpen: false,
        isMobile: window.innerWidth <= 800,
        init() {
            this.$watch('sidebarCollapsed', val => { localStorage.setItem('sidebarCollapsed', val); window.dispatchEvent(new CustomEvent('sidebar-collapsed', { detail: val })) });
            window.addEventListener('resize', () => { this.isMobile = window.innerWidth <= 800; if (!this.isMobile) { this.sidebarOpen = false; this.sidebarCollapsed = JSON.parse(localStorage.getItem('sidebarCollapsed') || 'false'); } });
            window.addEventListener('toggle-sidebar', () => { if (this.isMobile) { this.sidebarOpen = !this.sidebarOpen; if (this.sidebarOpen) window.dispatchEvent(new CustomEvent('force-sidebar-expand')); } });
        }
    }"
         @sidebar-collapsed.window="sidebarCollapsed = $event.detail"
         class="h-screen pt-16 bg-gray-950">

        {{-- Mobile overlay backdrop --}}
        <template x-if="isMobile">
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity duration-300"
                 x-transition:leave="transition-opacity duration-200"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-black/60">
            </div>
        </template>

        @if ($showSidebar)
            {{-- Desktop sidebar --}}
            <div x-show="!isMobile"
                 class="fixed left-0 top-16 bottom-0 z-40 transition-all duration-300 ease-in-out overflow-hidden"
                 :class="sidebarCollapsed ? 'w-16' : 'w-64'">
                @include('partials.sidebar')
            </div>

            {{-- Mobile sidebar overlay --}}
            <div x-show="isMobile && sidebarOpen"
                 x-transition:enter="transition-transform duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:leave="transition-transform duration-200"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed left-0 top-0 bottom-0 z-50 w-64 overflow-y-auto overflow-x-hidden bg-gray-950 border-r border-gray-800">
                @include('partials.sidebar')
            </div>
        @endif

        <div class="h-full overflow-y-auto p-4 sm:p-6 transition-all duration-300 ease-in-out"
             :class="isMobile ? 'ml-0' : (sidebarCollapsed ? 'ml-16' : 'ml-64')">
            {{ $slot }}
        </div>
    </div>

    @stack('modals')

    {{-- Livewire sidebar (modals de evento e equipe) --}}
    <livewire:page.sidebar />

    @livewireScripts
    @stack('scripts')

    @if (str_contains(request()->getHost(), 'ideias.dev.br'))
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/5534991535839" target="_blank" rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110">
        <i class="fab fa-whatsapp text-3xl"></i>
    </a>
    @endif
</body>

</html>
