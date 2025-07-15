@if (Route::has('login'))
    @include('navigation-menu-web')
@else
    @include('navigation-menu-dashboard')
@endif
