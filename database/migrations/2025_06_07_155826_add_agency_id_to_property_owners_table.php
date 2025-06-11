<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('property_owners', function (Blueprint $table) {
            // Add agency relationship to scope property owners by agency
            $table->foreignId('agency_id')->nullable()->after('user_id')->constrained('agencies')->onDelete('cascade');
            
            // Add index for performance
            $table->index(['agency_id', 'is_active']);
        });
        
        // Update existing property owners to have agency_id based on their properties
        $this->updateExistingPropertyOwners();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_owners', function (Blueprint $table) {
            $table->dropForeign(['agency_id']);
            $table->dropIndex(['agency_id', 'is_active']);
            $table->dropColumn('agency_id');
        });
    }
    
    /**
     * Update existing property owners to have agency_id based on their properties
     */
    private function updateExistingPropertyOwners(): void
    {
        // For each property owner, set their agency_id based on the first property they own
        $propertyOwners = \Illuminate\Support\Facades\DB::table('property_owners')->get();
        
        foreach ($propertyOwners as $owner) {
            $property = \Illuminate\Support\Facades\DB::table('properties')
                ->where('owner_id', $owner->id)
                ->first();
                
            if ($property && $property->agency_id) {
                \Illuminate\Support\Facades\DB::table('property_owners')
                    ->where('id', $owner->id)
                    ->update(['agency_id' => $property->agency_id]);
            }
        }
    }
};
