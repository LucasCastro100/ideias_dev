<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-800">
    <!-- Primary Navigation Menu -->
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-3">
                <!-- Sidebar Toggle + IdeiasDev Logo -->
                <button @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))"
                        class="p-2 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-gray-200 transition sidebar-toggle-btn">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <span class="text-xl font-bold" style="font-family: 'Fredoka', sans-serif; background: linear-gradient(135deg, #60a5fa, #a78bfa, #f472b6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        IdeiasDev
                    </span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @php
                        $userSystem = Auth::user()->system;
                        $systemSlug = $userSystem?->slug;
                        $canViewAll = !$userSystem || Auth::user()->isSuperAdmin();
                        $allSystemsNav = \App\Support\NewModulesNav::allSystems();
                        $systemsRoutePatterns = array_map(fn($s) => $s['slug'] . '.*', $allSystemsNav);
                    @endphp

                    @if ($canViewAll)
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                    @if (!$canViewAll && $systemSlug)
                        @php $mySystem = collect($allSystemsNav)->firstWhere('slug', $systemSlug); @endphp
                        @if ($mySystem)
                            <x-nav-link href="{{ route($mySystem['route']) }}" :active="request()->routeIs($systemSlug . '.*')">
                                {{ __($mySystem['label']) }}
                            </x-nav-link>
                        @endif
                    @endif

                    @if ($canViewAll)
                        <div class="relative flex items-center" x-data="{ systemsOpen: false }" @click.outside="systemsOpen = false">
                            <button @click="systemsOpen = !systemsOpen"
                                class="inline-flex items-center gap-1.5 px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none
                                    {{ request()->routeIs($systemsRoutePatterns) ? 'border-blue-500 text-gray-100' : 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-600' }}">
                                {{ __('Sistemas') }}
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>

                            <div x-show="systemsOpen" x-cloak
                                class="absolute z-50 top-full left-0 mt-2 w-72 max-h-96 overflow-y-auto rounded-lg bg-gray-900 border border-gray-800 shadow-lg py-2">
                                @foreach ($allSystemsNav as $sys)
                                    <a href="{{ route($sys['route']) }}" wire:navigate
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-gray-100 transition">
                                        <i class="fas {{ $sys['icon'] }} text-blue-400 w-4 text-center"></i>
                                        {{ $sys['label'] }}
                                    </a>
                                @endforeach
                                <div class="border-t border-gray-800 mt-1 pt-1">
                                    <a href="{{ route('admin.systems') }}" wire:navigate
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-gray-100 transition">
                                        <i class="fas fa-gear w-4 text-center"></i>
                                        Gerenciar sistemas
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Auth::user()->isSuperAdmin())
                        <x-nav-link href="{{ route('admin.users') }}" :active="request()->routeIs('admin.users')">
                            {{ __('Usuários') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-900 hover:text-gray-200 focus:outline-none focus:bg-gray-800 active:bg-gray-800 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam?->name ?? 'Sem time' }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    @if (Auth::user()->currentTeam)
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>
                                    @endif

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-900 hover:text-gray-200 focus:outline-none focus:bg-gray-800 active:bg-gray-800 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                Gerenciar Conta
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                Perfil
                            </x-dropdown-link>

                            {{-- API Tokens desabilitado --}}

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
Sair
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-200 hover:bg-gray-800 focus:outline-none focus:bg-gray-800 focus:text-gray-200 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @php
                $userSystem = Auth::user()->system;
                $systemSlug = $userSystem?->slug;
                $canViewAll = !$userSystem || Auth::user()->isSuperAdmin();
                $allSystemsNav = \App\Support\NewModulesNav::allSystems();
            @endphp

            @if ($canViewAll)
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->isSuperAdmin())
                <x-responsive-nav-link href="{{ route('admin.users') }}" :active="request()->routeIs('admin.users')">
                    {{ __('Usuários') }}
                </x-responsive-nav-link>
            @endif

            @if ($canViewAll)
                <div class="block px-4 pt-2 pb-1 text-xs text-gray-400 uppercase tracking-wide">Sistemas</div>
                @foreach ($allSystemsNav as $sys)
                    <x-responsive-nav-link href="{{ route($sys['route']) }}" :active="request()->routeIs($sys['slug'] . '.*')">
                        <i class="fas {{ $sys['icon'] }} w-4 text-center mr-2 text-blue-400"></i>{{ $sys['label'] }}
                    </x-responsive-nav-link>
                @endforeach
                <x-responsive-nav-link href="{{ route('admin.systems') }}" :active="request()->routeIs('admin.systems')">
                    <i class="fas fa-gear w-4 text-center mr-2 text-gray-500"></i>Gerenciar sistemas
                </x-responsive-nav-link>
            @else
                @php $mySystem = collect($allSystemsNav)->firstWhere('slug', $systemSlug); @endphp
                @if ($mySystem)
                    <x-responsive-nav-link href="{{ route($mySystem['route']) }}" :active="request()->routeIs($systemSlug . '.*')">
                        {{ __($mySystem['label']) }}
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    Perfil
                </x-responsive-nav-link>

                {{-- API Tokens desabilitado --}}

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200 dark:border-gray-600"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    @if (Auth::user()->currentTeam)
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>
                    @endif

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>

<style>
    @media (min-width: 800px) {
        .sidebar-toggle-btn { display: none !important; }
    }
</style>
