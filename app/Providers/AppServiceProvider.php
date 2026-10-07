<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('create', function ($user) {
            return $user->canCreate();
        });

        Gate::define('create-event', function ($user) {
            return in_array($user->role_id, [1, 2]);
        });

        Gate::define('edit', function ($user) {
            return $user->canEdit();
        });

        Gate::define('delete', function ($user) {
            return $user->canDelete();
        });

        Gate::define('manage-users', function ($user) {
            return $user->isSuperAdmin();
        });
    }
}
