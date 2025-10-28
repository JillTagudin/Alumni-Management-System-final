<?php

namespace App\Providers;

use App\Models\Alumni;
use App\Policies\AlumniPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Alumni::class => AlumniPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Additional gates can be defined here if needed
        Gate::define('manage-users', function ($user) {
            return in_array($user->role, ['Admin', 'Staff']);
        });

        Gate::define('admin-only', function ($user) {
            // Line 34 - Update the gate definition
            return $user->role === 'Admin';
        });
    }
}