<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LandlordRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create landlord role
        $landlordRole = Role::firstOrCreate([
            'name' => 'landlord',
            'guard_name' => 'web',
        ]);

        // Define permissions for landlords
        $landlordPermissions = [
            // Property permissions - full CRUD for their own properties
            'view_property',
            'view_any_property',
            'create_property',
            'update_property',
            'delete_property',
            
            // Tenant permissions
            'view_tenant',
            'view_any_tenant',
            'create_tenant',
            'update_tenant',
            'delete_tenant',
            
            // Lease permissions
            'view_lease',
            'view_any_lease',
            'create_lease',
            'update_lease',
            'delete_lease',
            
            // Rent payment permissions
            'view_rent::payment',
            'view_any_rent::payment',
            'create_rent::payment',
            'update_rent::payment',
            'delete_rent::payment',
            
            // Maintenance request permissions
            'view_maintenance::request',
            'view_any_maintenance::request',
            'create_maintenance::request',
            'update_maintenance::request',
            'delete_maintenance::request',
            
            // Property inquiry permissions (to receive inquiries)
            'view_property::inquiry',
            'view_any_property::inquiry',
            'update_property::inquiry',
            
            // Property viewing permissions
            'view_property::viewing',
            'view_any_property::viewing',
            'create_property::viewing',
            'update_property::viewing',
            'delete_property::viewing',
            
            // Dashboard and widget access
            'page_LandlordDashboard',
            'widget_LandlordStatsWidget',
            'widget_RentCollectionWidget',
        ];

        // Create permissions if they don't exist and assign them to the role
        $permissions = collect($landlordPermissions)->map(function ($permissionName) {
            return Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        });

        $landlordRole->syncPermissions($permissions);

        $this->command->info('Landlord role and permissions created successfully!');
    }
}
