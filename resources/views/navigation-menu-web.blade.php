<div>
    <header class="max-w-7xl mx-auto px-4 text-sm p-4 not-has-[nav]:hidden">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                <x-nav-link :href="route('web.home')" :active="request()->routeIs('web.home')" wire:navigate>
                    {{ __('Home ') }}
                </x-nav-link>

                @auth
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                @else
                    <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                        {{ __('Login') }}
                    </x-nav-link>

                    @if (Route::has('register'))
                        <x-nav-link :href="route('register')" :active="request()->routeIs('register')">
                            {{ __('Register') }}
                        </x-nav-link>
                    @endif
                @endauth
            </nav>
        @endif
    </header>
</div>
