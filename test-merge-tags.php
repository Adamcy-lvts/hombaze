<?php
// Simple test script

$testContent = '<p>This is a test with {{ Landlord Name }} and {{ Property Title }}.</p>';

$testData = [
    'landlord_name' => 'John Doe',
    'property_title' => 'Beautiful 3BR Apartment'
];

echo "Original content:\n";
echo $testContent . "\n\n";

echo "Manual replacement test:\n";
$result = str_replace([
    '{{ Landlord Name }}',
    '{{ Property Title }}'
], [
    'John Doe',
    'Beautiful 3BR Apartment'
], $testContent);
echo $result . "\n\n";