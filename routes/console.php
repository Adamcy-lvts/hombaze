<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Saved Search Matching Engine - Automated Hunting Schedule
Schedule::command('searches:process-matches --new-properties')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/saved-search-matches.log'));

Schedule::command('searches:process-matches')
    ->everyFourHours()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/saved-search-matches.log'));

// Queue Management - Ensure queue workers are running
Schedule::command('queue:restart')
    ->dailyAt('03:00')
    ->runInBackground();
