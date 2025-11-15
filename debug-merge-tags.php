<?php
// Debug script to test merge tag rendering

// Include Laravel bootstrap
require __DIR__ . '/bootstrap/app.php';

$app = \Illuminate\Foundation\Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {
        //
    })
    ->withExceptions(function (\Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        //
    })->create();

$app->boot();

// Test content
$testContent = '<p>This is a test with {{ Landlord Name }} and {{ Property Title }}.</p>';

// Test data
$testData = [
    'landlord_name' => 'John Doe',
    'property_title' => 'Beautiful 3BR Apartment'
];

echo "Original content:\n";
echo $testContent . "\n\n";

echo "Test data:\n";
print_r($testData);

echo "\nTesting renderWithMergeTags:\n";
$result = \App\Models\LeaseTemplate::renderWithMergeTags($testContent, $testData);
echo $result . "\n\n";

echo "Testing direct manual replace:\n";
$manualResult = str_replace([
    '{{ Landlord Name }}',
    '{{ Property Title }}'
], [
    'John Doe',
    'Beautiful 3BR Apartment'
], $testContent);
echo $manualResult . "\n\n";

echo "Available variables:\n";
print_r(\App\Models\LeaseTemplate::getAvailableVariables());