<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Lease;
use App\Models\RentPayment;
use App\Models\Property;
use App\Models\PropertyView;
use App\Models\PropertyInquiry;
use App\Models\SmartSearch;
use App\Observers\LeaseObserver;
use App\Observers\RentPaymentObserver;
use App\Observers\PropertyObserver;
use App\Observers\PropertyViewObserver;
use App\Observers\PropertyInquiryObserver;
use App\Observers\SmartSearchObserver;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;

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
        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        // Register model observers
        Lease::observe(LeaseObserver::class);
        RentPayment::observe(RentPaymentObserver::class);
        Property::observe(PropertyObserver::class);
        SmartSearch::observe(SmartSearchObserver::class);

        // SmartSearch claim detection observers
        PropertyView::observe(PropertyViewObserver::class);
        PropertyInquiry::observe(PropertyInquiryObserver::class);

        Section::configureUsing(fn (Section $section) => $section->columnSpanFull());
        Grid::configureUsing(fn (Grid $grid) => $grid->columnSpanFull());
        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset->columnSpanFull());

        Table::configureUsing(fn (Table $table) => $table->deferFilters(false));
    }
}
