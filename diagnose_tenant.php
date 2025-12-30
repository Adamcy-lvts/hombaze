<?php

use App\Models\User;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'ahmadazizuahmad@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found: $email\n";
    exit;
}

echo "User ID: {$user->id}\n";
echo "User Type: {$user->user_type}\n";

$tenants = Tenant::where('user_id', $user->id)->get();
echo "Tenant records linked to this User ID: " . $tenants->count() . "\n";
foreach ($tenants as $t) {
    echo " - Tenant ID: {$t->id} | Name: {$t->first_name} {$t->last_name} | Landlord ID: {$t->landlord_id}\n";
    
    $leases = Lease::where('tenant_id', $t->id)->get();
    echo "   Leases for this Tenant ID: " . $leases->count() . "\n";
    foreach ($leases as $l) {
        $p = Property::find($l->property_id);
        echo "   * Lease ID: {$l->id} | Property: " . ($p ? $p->title : 'N/A') . " | Status: {$l->status}\n";
    }
}

$allTenantsWithEmail = Tenant::where('email', $email)->get();
echo "\nAll Tenant records with email '$email': " . $allTenantsWithEmail->count() . "\n";
foreach ($allTenantsWithEmail as $t) {
    echo " - Tenant ID: {$t->id} | User ID: " . ($t->user_id ?: 'NULL') . " | Landlord ID: {$t->landlord_id}\n";
}

$allLeases = Lease::all();
echo "\nTotal Leases in DB: " . $allLeases->count() . "\n";

?>
