@if (auth()->check())
    @include('navigation-menu-dashboard')
@else
    @include('navigation-menu-web')
@endif
