@php
    $isTbr = request()->routeIs('tbr.*');
    $isClientes = request()->routeIs('clientes.*');
    $isFinanceiro = request()->routeIs('financeiro.*');
    $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
    $hasCompany = auth()->check() && auth()->user()->teams()->exists();
    $newModules = \App\Support\NewModulesNav::all();
    $activeModuleSlug = collect($newModules)->keys()->first(fn($slug) => request()->routeIs($slug . '.*'));
@endphp
<div class="px-3 mt-3">
    <div class="flex items-center justify-between px-2 py-1.5 cursor-pointer text-gray-500 text-xs uppercase font-bold tracking-wider"
        @click="showNav = !showNav" title="Mostrar/Esconder Navegação" style="user-select: none">
        <span x-show="!sidebarCollapsed">Navegação</span>
        <svg x-show="!sidebarCollapsed" :class="showNav ? 'rotate-180' : ''"
            class="h-3.5 w-3.5 transform transition-transform" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <ul x-show="showNav" x-transition.opacity
        class="space-y-0.5 mt-1">

        {{-- TBR Navigation --}}
        @if ($isTbr)
        <li>
            <a href="{{ route('tbr.dashboard') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('tbr.dashboard') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Dashboard">
                <i class="sidebar-icon fas fa-house text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('tbr.categories') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('tbr.categories') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Categorias">
                <i class="sidebar-icon fas fa-tags text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Categorias</span>
            </a>
        </li>
        <li>
            <a href="{{ route('tbr.questions') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('tbr.questions') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Perguntas">
                <i class="sidebar-icon fas fa-question-circle text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Perguntas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('tbr.modalities') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('tbr.modalities') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Modalidades">
                <i class="sidebar-icon fas fa-layer-group text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Modalidades</span>
            </a>
        </li>
        <li>
            <a href="{{ route('tbr.dp-missions') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('tbr.dp-missions') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Missões DP">
                <i class="sidebar-icon fas fa-robot text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Missões DP</span>
            </a>
        </li>
        @endif

        {{-- Clientes Navigation --}}
        @if ($isClientes)
        <li>
            <a href="{{ route('clientes.dashboard') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('clientes.dashboard') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Dashboard">
                <i class="sidebar-icon fas fa-house text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clientes.clients') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('clientes.clients') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Clientes">
                <i class="sidebar-icon fas fa-users text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Clientes</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clientes.plans') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('clientes.plans') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Planos">
                <i class="sidebar-icon fas fa-file-invoice text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Planos</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clientes.receitas') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('clientes.receitas') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Receitas">
                <i class="sidebar-icon fas fa-money-bill-wave text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Receitas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clientes.accounts') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('clientes.accounts') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Gastos">
                <i class="sidebar-icon fas fa-credit-card text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Gastos</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clientes.lesson-logs') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('clientes.lesson-logs') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Aulas">
                <i class="sidebar-icon fas fa-book-open text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Aulas</span>
            </a>
        </li>
        @if ($isSuperAdmin || $hasCompany)
        <li class="border-t border-gray-800 my-1 pt-1">
            <a href="{{ route('clientes.companies') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('clientes.companies') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Empresas">
                <i class="sidebar-icon fas fa-building text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Empresas</span>
            </a>
        </li>
        @endif
        @endif

        {{-- Financeiro Navigation --}}
        @if ($isFinanceiro)
        <li>
            <a href="{{ route('financeiro.dashboard') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('financeiro.dashboard') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Dashboard">
                <i class="sidebar-icon fas fa-house text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('financeiro.transactions') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('financeiro.transactions') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Contas">
                <i class="sidebar-icon fas fa-credit-card text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Contas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('financeiro.account-types') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('financeiro.account-types') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Tipos de Conta">
                <i class="sidebar-icon fas fa-list text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Tipos de Conta</span>
            </a>
        </li>
        <li>
            <a href="{{ route('financeiro.categories') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('financeiro.categories') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Categorias">
                <i class="sidebar-icon fas fa-tags text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Categorias</span>
            </a>
        </li>
        <li class="border-t border-gray-800 my-1 pt-1">
            <a href="{{ route('faturamento-hospedagem.clientes') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('faturamento-hospedagem.clientes') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Clientes de Hospedagem">
                <i class="sidebar-icon fas fa-server text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Clientes Hospedagem</span>
            </a>
        </li>
        <li>
            <a href="{{ route('faturamento-hospedagem.faturas') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('faturamento-hospedagem.faturas') ? 'active' : '' }}"
                :class="{ 'justify-center': sidebarCollapsed }" title="Faturas de Hospedagem">
                <i class="sidebar-icon fas fa-file-invoice text-gray-500"></i>
                <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">Faturas Hospedagem</span>
            </a>
        </li>
        @endif

        {{-- Módulos novos (vitrine de projetos) --}}
        @if ($activeModuleSlug)
            @foreach ($newModules[$activeModuleSlug]['pages'] as $page)
                <li>
                    <a href="{{ route($page['route']) }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs($page['route']) ? 'active' : '' }}"
                        :class="{ 'justify-center': sidebarCollapsed }" title="{{ $page['label'] }}">
                        <i class="sidebar-icon fas {{ $page['icon'] }} text-gray-500"></i>
                        <span x-show="!sidebarCollapsed" class="sidebar-text text-xs font-semibold text-gray-400">{{ $page['label'] }}</span>
                    </a>
                </li>
            @endforeach
        @endif
    </ul>
</div>
