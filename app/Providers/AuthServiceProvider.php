<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\MapPoint as MapPointModel;
use App\Models\User as UserModel;
use App\Policies\PuncteHartaPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // UserModel::class => UserPolicy::class,
        MapPointModel::class => PuncteHartaPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
