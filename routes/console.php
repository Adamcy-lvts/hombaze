<?php

use App\Jobs\ProcessVipExclusiveExpiry;
use App\Jobs\ProcessClaimPauseExpiry;
use App\Jobs\ProcessTierBatchNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// =========================================
// SmartSearch Matching Engine - Automated Hunting Schedule
// =========================================

// Process new properties for SmartSearch matches
Schedule::command('smartsearch:process-matches --new-properties')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/smart-search-matches.log'));

// Full rescan of all properties against all searches
Schedule::command('smartsearch:process-matches')
    ->everyFourHours()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/smart-search-matches.log'));

// =========================================
// SmartSearch First Dibs Cascade System
// =========================================

// Process expired VIP exclusive windows (every 5 minutes)
// When a VIP's 3-hour exclusive window expires, this advances to the next VIP
Schedule::job(new ProcessVipExclusiveExpiry())
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Process expired claim pauses (every 15 minutes)
// When a 24-hour claim pause expires, this resumes the cascade
Schedule::job(new ProcessClaimPauseExpiry())
    ->everyFifteenMinutes()
    ->withoutOverlapping();

// Process non-VIP batch notifications (every 30 minutes)
// Sends batch notifications to Priority, Standard, and Starter tiers
Schedule::job(new ProcessTierBatchNotification())
    ->everyThirtyMinutes()
    ->withoutOverlapping();

// =========================================
// SmartSearch Expiration
// =========================================

// Mark expired searches and send notifications daily
Schedule::command('smartsearch:expire-searches')
    ->dailyAt('08:00')
    ->withoutOverlapping();

// =========================================
// Queue Management
// =========================================

// Ensure queue workers are running
Schedule::command('queue:restart')
    ->dailyAt('03:00')
    ->runInBackground();
