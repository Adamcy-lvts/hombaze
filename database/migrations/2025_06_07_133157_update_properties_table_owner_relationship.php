<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, create PropertyOwner records for existing property owners in users table
        $this->migrateExistingPropertyOwners();
        
        // Check if foreign key exists and drop it
        $this->dropForeignKeyIfExists();
        
        Schema::table('properties', function (Blueprint $table) {
            // Change the foreign key to reference property_owners table
            $table->foreign('owner_id')->references('id')->on('property_owners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['owner_id']);
            
            // Restore the old foreign key to users
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Note: PropertyOwner records created during migration are not automatically removed
        // This is intentional to prevent data loss
    }

    /**
     * Migrate existing property owners from users table to property_owners table
     */
    private function migrateExistingPropertyOwners(): void
    {
        // Get all unique user IDs that are referenced as property owners
        $propertyOwnerUserIds = DB::table('properties')
            ->select('owner_id')
            ->whereNotNull('owner_id')
            ->distinct()
            ->pluck('owner_id');

        foreach ($propertyOwnerUserIds as $userId) {
            $user = DB::table('users')->where('id', $userId)->first();
            
            if (!$user) {
                continue; // Skip if user doesn't exist
            }

            // Create PropertyOwner record
            $propertyOwnerId = DB::table('property_owners')->insertGetId([
                'type' => 'individual',
                'first_name' => $this->extractFirstName($user->name),
                'last_name' => $this->extractLastName($user->name),
                'email' => $user->email,
                'phone' => $user->phone,
                'user_id' => $user->id, // Link back to user account if they want to access platform
                'notes' => 'Migrated from existing user account during database update',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update all properties owned by this user to reference the new PropertyOwner
            DB::table('properties')
                ->where('owner_id', $userId)
                ->update(['owner_id' => $propertyOwnerId]);
        }
    }

    /**
     * Extract first name from full name
     */
    private function extractFirstName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? 'Unknown';
    }

    /**
     * Extract last name from full name
     */
    private function extractLastName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        if (count($parts) > 1) {
            array_shift($parts); // Remove first name
            return implode(' ', $parts);
        }
        return '';
    }

    /**
     * Drop foreign key if it exists
     */
    private function dropForeignKeyIfExists(): void
    {
        // Get all foreign keys for the properties table
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'properties' 
            AND COLUMN_NAME = 'owner_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE properties DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            } catch (\Exception $e) {
                // Continue if foreign key doesn't exist
            }
        }
    }
};
