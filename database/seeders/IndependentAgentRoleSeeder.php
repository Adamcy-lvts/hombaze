<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agent;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class IndependentAgentRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Independent Agent Role Assignment...');
        
        // Ensure the Independent Agent role exists
        $role = $this->ensureIndependentAgentRoleExists();
        
        // Find all independent agents (agents without agency_id)
        $independentAgents = Agent::whereNull('agency_id')
            ->with('user')
            ->get();
            
        $this->command->info("Found {$independentAgents->count()} independent agents");
        
        $assignedCount = 0;
        $alreadyHadRoleCount = 0;
        
        foreach ($independentAgents as $agent) {
            $user = $agent->user;
            
            if (!$user) {
                $this->command->warn("Agent ID {$agent->id} has no associated user");
                continue;
            }
            
            // Check if user already has the independent_agent role
            if ($user->hasRole('independent_agent')) {
                $alreadyHadRoleCount++;
                continue;
            }
            
            try {
                // Assign the role
                $user->assignRole($role);
                $assignedCount++;
                $this->command->info("✅ Assigned Independent Agent role to: {$user->email}");
                
            } catch (\Exception $e) {
                $this->command->error("❌ Failed to assign role to {$user->email}: " . $e->getMessage());
                Log::error("Failed to assign Independent Agent role to user {$user->email}: " . $e->getMessage());
            }
        }
        
        $this->command->info('');
        $this->command->info('📊 ASSIGNMENT SUMMARY:');
        $this->command->info("• Total independent agents found: {$independentAgents->count()}");
        $this->command->info("• New role assignments: {$assignedCount}");
        $this->command->info("• Already had role: {$alreadyHadRoleCount}");
        $this->command->info('');
        $this->command->info('✅ Independent Agent Role Assignment completed!');
    }
    
    /**
     * Ensure the Independent Agent role exists with proper permissions
     */
    private function ensureIndependentAgentRoleExists(): Role
    {
        // Check if the role already exists
        $role = Role::where('name', 'independent_agent')
            ->where('guard_name', 'web')
            ->whereNull('agency_id') // Global role, not agency-specific
            ->first();

        if (!$role) {
            $this->command->info('Creating Independent Agent role...');
            
            // Create the role
            $role = Role::create([
                'name' => 'independent_agent',
                'guard_name' => 'web',
                'agency_id' => null, // Global role
            ]);

            // Define permissions for independent agents
            $independentAgentPermissions = [
                // Property permissions - full CRUD for their own properties
                'view_property',
                'view_any_property',
                'create_property',
                'update_property',
                'delete_property',
                
                // Property inquiry permissions
                'view_property::inquiry',
                'view_any_property::inquiry',
                'create_property::inquiry',
                'update_property::inquiry',
                'delete_property::inquiry',
                
                // Property viewing permissions
                'view_property::viewing',
                'view_any_property::viewing',
                'create_property::viewing',
                'update_property::viewing',
                'delete_property::viewing',
                
                // Review permissions
                'view_review',
                'view_any_review',
                'create_review',
                'update_review',
                'delete_review',
                
                // Dashboard and widget access
                'page_AgentDashboard',
                'widget_AgentStatsWidget',
                'widget_PropertiesChartWidget',
                
                // Basic tenant menu access
                'view_tenant_menu',
            ];

            // Get existing permissions and assign them to the role
            $permissions = collect($independentAgentPermissions)->map(function ($permissionName) {
                return Permission::where('name', $permissionName)
                    ->where('guard_name', 'web')
                    ->first();
            })->filter(); // Remove any null permissions

            if ($permissions->isNotEmpty()) {
                $role->syncPermissions($permissions);
                $this->command->info("✅ Created Independent Agent role with " . $permissions->count() . " permissions");
                Log::info("Created Independent Agent role with " . $permissions->count() . " permissions");
            } else {
                $this->command->warn("⚠️ No permissions found for Independent Agent role");
                Log::warning("No permissions found for Independent Agent role");
            }
        } else {
            $this->command->info('Independent Agent role already exists');
        }

        return $role;
    }
}
