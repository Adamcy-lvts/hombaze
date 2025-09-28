<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Lease;
use App\Models\RentPayment;
use App\Observers\LeaseObserver;
use App\Observers\RentPaymentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        // Register model observers
        Lease::observe(LeaseObserver::class);
        RentPayment::observe(RentPaymentObserver::class);
    }
}
