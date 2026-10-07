@php
    $isTbr = request()->routeIs('tbr.*');
    $isClientes = request()->routeIs('clientes.*');
    $isFinanceiro = request()->routeIs('financeiro.*');
    $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
    $hasCompany = auth()->check() && auth()->user()->teams()->exists();
    $newModules = \App\Support\NewModulesNav::all();
    $activeModuleSlug = collect($newModules)->keys()->first(fn($slug) => request()->routeIs($slug . '.*'));
@endphp

<div class="sidebar h-full flex flex-col"
     x-data="{ showActions: true, showNav: true }">
    {{-- Header --}}
    <div class="flex items-center py-3 border-b border-gray-800/50 transition-all duration-300"
         :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-4'">
        <span x-show="!sidebarCollapsed" class="text-xs uppercase font-bold tracking-wider text-gray-500">
            @if ($isTbr) TBR
            @elseif ($isClientes) Clientes
            @elseif ($isFinanceiro) Financeiro
            @elseif ($activeModuleSlug) {{ $newModules[$activeModuleSlug]['label'] }}
            @else Menu @endif
        </span>
        <button x-show="isMobile" @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))"
            class="p-1.5 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-gray-200 transition" title="Fechar">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <button x-show="!isMobile" @click="sidebarCollapsed = !sidebarCollapsed"
            class="p-1.5 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-gray-200 transition"
            :title="sidebarCollapsed ? 'Expandir' : 'Colapsar'">
            <svg :class="sidebarCollapsed ? 'rotate-180' : ''"
                class="h-5 w-5 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    {{-- Navegação --}}
    @include('partials.sidebar-nav')

    {{-- Spacer --}}
    <div class="flex-1"></div>

    {{-- Perfil / Sair --}}
    <div class="px-3 py-3 border-t border-gray-800/50 mt-2">
        <ul class="space-y-0.5">
            <li>
                <a href="{{ route('profile.show') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('profile.show') ? 'active' : '' }}"
                    :class="{ 'justify-center': sidebarCollapsed }" title="Perfil">
                    <i class="sidebar-icon fas fa-user text-gray-500"></i>
                    <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Perfil</span>
                </a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="sidebar-action-btn logout flex items-center gap-3 px-4 py-2.5 rounded-lg w-full text-left transition"
                        :class="{ 'justify-center': sidebarCollapsed }" title="Sair">
                        <i class="sidebar-icon fas fa-sign-out-alt text-gray-500"></i>
                        <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Sair</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
