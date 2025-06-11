<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define roles with their permissions for agency panel
        $rolesWithPermissions = [
            [
                'name' => 'super_admin',
                'guard_name' => 'web',
                'permissions' => [
                    // Role permissions
                    'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role',
                    // Agent permissions
                    'view_agent', 'view_any_agent', 'create_agent', 'update_agent', 'restore_agent', 'restore_any_agent',
                    'replicate_agent', 'reorder_agent', 'delete_agent', 'delete_any_agent', 'force_delete_agent', 'force_delete_any_agent',
                    // Property permissions
                    'view_property', 'view_any_property', 'create_property', 'update_property', 'restore_property', 'restore_any_property',
                    'replicate_property', 'reorder_property', 'delete_property', 'delete_any_property', 'force_delete_property', 'force_delete_any_property',
                    // Property inquiry permissions
                    'view_property::inquiry', 'view_any_property::inquiry', 'create_property::inquiry', 'update_property::inquiry',
                    'restore_property::inquiry', 'restore_any_property::inquiry', 'replicate_property::inquiry', 'reorder_property::inquiry',
                    'delete_property::inquiry', 'delete_any_property::inquiry', 'force_delete_property::inquiry', 'force_delete_any_property::inquiry',
                    // Property viewing permissions
                    'view_property::viewing', 'view_any_property::viewing', 'create_property::viewing', 'update_property::viewing',
                    'restore_property::viewing', 'restore_any_property::viewing', 'replicate_property::viewing', 'reorder_property::viewing',
                    'delete_property::viewing', 'delete_any_property::viewing', 'force_delete_property::viewing', 'force_delete_any_property::viewing',
                    // Page and widget permissions
                    'page_AgencyDashboard', 'widget_AgencyStatsWidget', 'widget_PropertiesChartWidget',
                    // Tenant menu and profile permissions
                    'view_tenant_menu', 'update_agency_profile'
                ]
            ],
            [
                'name' => 'agent',
                'guard_name' => 'web',
                'permissions' => [
                    // Property permissions (limited)
                    'view_property', 'view_any_property', 'create_property', 'update_property',
                    // Property inquiry permissions
                    'view_property::inquiry', 'view_any_property::inquiry', 'create_property::inquiry', 'update_property::inquiry',
                    // Property viewing permissions
                    'view_property::viewing', 'view_any_property::viewing', 'create_property::viewing', 'update_property::viewing',
                    // Dashboard access
                    'page_AgencyDashboard', 'widget_AgencyStatsWidget', 'widget_PropertiesChartWidget',
                    // Basic tenant menu access (no profile editing)
                    'view_tenant_menu'
                ]
            ],
            [
                'name' => 'independent_agent',
                'guard_name' => 'web',
                'permissions' => [
                    // Property permissions - full CRUD for their own properties
                    'view_property', 'view_any_property', 'create_property', 'update_property', 'delete_property',
                    // Property inquiry permissions
                    'view_property::inquiry', 'view_any_property::inquiry', 'create_property::inquiry', 'update_property::inquiry', 'delete_property::inquiry',
                    // Property viewing permissions
                    'view_property::viewing', 'view_any_property::viewing', 'create_property::viewing', 'update_property::viewing', 'delete_property::viewing',
                    // Review permissions
                    'view_review', 'view_any_review', 'create_review', 'update_review', 'delete_review',
                    // Dashboard and widget access
                    'page_AgentDashboard', 'widget_AgentStatsWidget', 'widget_PropertiesChartWidget',
                    // Basic tenant menu access
                    'view_tenant_menu'
                ]
            ],
            [
                'name' => 'tenant',
                'guard_name' => 'web',
                'permissions' => [
                    // Property viewing permissions (read-only)
                    'view_property', 'view_any_property',
                    // Property inquiry permissions
                    'view_property::inquiry', 'create_property::inquiry', 'update_property::inquiry',
                    // Property viewing permissions
                    'view_property::viewing', 'create_property::viewing',
                    // Lease permissions (view own leases)
                    'view_lease', 'view_any_lease',
                    // Rent payment permissions (view own payments)
                    'view_rent::payment', 'view_any_rent::payment',
                    // Dashboard and tenant-specific access
                    'page_TenantDashboard', 'widget_TenantStatsWidget',
                    // Basic tenant menu access
                    'view_tenant_menu'
                ]
            ],
            [
                'name' => 'landlord',
                'guard_name' => 'web',
                'permissions' => [
                    // Property permissions - full CRUD for their own properties
                    'view_property', 'view_any_property', 'create_property', 'update_property', 'delete_property',
                    // Tenant permissions
                    'view_tenant', 'view_any_tenant', 'create_tenant', 'update_tenant', 'delete_tenant',
                    // Lease permissions
                    'view_lease', 'view_any_lease', 'create_lease', 'update_lease', 'delete_lease',
                    // Rent payment permissions
                    'view_rent::payment', 'view_any_rent::payment', 'create_rent::payment', 'update_rent::payment', 'delete_rent::payment',
                    // Maintenance request permissions
                    'view_maintenance::request', 'view_any_maintenance::request', 'create_maintenance::request', 'update_maintenance::request', 'delete_maintenance::request',
                    // Property inquiry permissions (to receive inquiries)
                    'view_property::inquiry', 'view_any_property::inquiry', 'update_property::inquiry',
                    // Property viewing permissions
                    'view_property::viewing', 'view_any_property::viewing', 'create_property::viewing', 'update_property::viewing', 'delete_property::viewing',
                    // Dashboard and widget access
                    'page_LandlordDashboard', 'widget_LandlordStatsWidget', 'widget_RentCollectionWidget'
                ]
            ]
        ];

        static::makeRolesWithPermissions(json_encode($rolesWithPermissions));
        static::makeDirectPermissions('[]');

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
