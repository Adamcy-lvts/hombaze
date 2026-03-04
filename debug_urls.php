<?php

use App\Models\PropertyOwner;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Debugging Profile Photos & Documents ---\n";

// 1. Check a Property Owner with media
$owner = PropertyOwner::with('user')->first();

if (!$owner) {
    echo "No Property Owner found.\n";
    exit;
}

echo "Owner ID: " . $owner->id . "\n";
echo "Name: " . $owner->display_name . "\n";
echo "Profile Photo Attr: " . ($owner->attributes['profile_photo'] ?? 'NULL') . "\n";
echo "Has Media (profile_photo): " . ($owner->hasMedia('profile_photo') ? 'YES' : 'NO') . "\n";

// Test Accessor
echo "Generated Profile URL: " . $owner->profile_photo_url . "\n";

// Test User Fallback
if ($owner->user) {
    echo "User Avatar Attr: " . ($owner->user->avatar ?? 'NULL') . "\n";
    // echo "User Avatar URL: " . $owner->user->filament_avatar_url . "\n"; 
}

echo "\n--- Documents ---\n";
echo "ID Document Attr: " . ($owner->attributes['id_document'] ?? 'NULL') . "\n";
echo "Has Media (id_document): " . ($owner->hasMedia('id_document') ? 'YES' : 'NO') . "\n";
echo "Generated ID URL: " . $owner->id_document_url . "\n";

echo "Proof Address Attr: " . ($owner->attributes['proof_of_address'] ?? 'NULL') . "\n";
echo "Has Media (proof_of_address): " . ($owner->hasMedia('proof_of_address') ? 'YES' : 'NO') . "\n";
echo "Generated Proof URL: " . $owner->proof_of_address_url . "\n";

echo "\n--- Storage Config ---\n";
echo "Public Disk URL: " . config('filesystems.disks.public.url') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";
