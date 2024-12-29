<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        //
    ];

    public function boot(): void
    {
        // Define a gate for admin access
        Gate::define('access-admin', function ($user) {
            return $user instanceof Admin && 
            ($user->hasRole('admin') || $user->hasRole('super-admin'));
        });

        // Define gates for specific admin actions
        Gate::define('manage-users', function ($user) {
            return $user instanceof Admin && $user->hasRole('super-admin');
        });

        Gate::define('manage-settings', function ($user) {
            return $user instanceof Admin && $user->hasRole('super-admin');
        });
    }
}
