<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_search_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('searches_limit')->default(0);
            $table->unsignedInteger('duration_days')->default(0);
            $table->json('notification_channels')->nullable();
            $table->unsignedInteger('priority_order')->default(0);
            $table->unsignedInteger('delay_hours')->default(0);
            $table->unsignedInteger('exclusive_window_hours')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $tiers = [
            'starter' => [
                'name' => 'Starter',
                'price' => 10000,
                'searches' => 1,
                'duration_days' => 60,
                'channels' => ['email'],
                'priority_order' => 4,
                'delay_hours' => 48,
                'description' => 'Perfect for first-time searchers',
            ],
            'standard' => [
                'name' => 'Standard',
                'price' => 20000,
                'searches' => 3,
                'duration_days' => 90,
                'channels' => ['email', 'whatsapp'],
                'priority_order' => 3,
                'delay_hours' => 24,
                'description' => 'Most popular choice',
            ],
            'priority' => [
                'name' => 'Priority',
                'price' => 35000,
                'searches' => 5,
                'duration_days' => 90,
                'channels' => ['email', 'whatsapp', 'sms'],
                'priority_order' => 2,
                'delay_hours' => 0,
                'description' => 'Get matches before standard users',
            ],
            'vip' => [
                'name' => 'VIP',
                'price' => 50000,
                'searches' => 999,
                'duration_days' => 120,
                'channels' => ['email', 'whatsapp', 'sms'],
                'priority_order' => 1,
                'delay_hours' => 0,
                'exclusive_window_hours' => 3,
                'description' => 'First Dibs - exclusive 3-hour access to new matches',
            ],
        ];

        $now = now();
        $rows = [];
        $sort = 1;
        foreach ($tiers as $slug => $tier) {
            $rows[] = [
                'slug' => $slug,
                'name' => $tier['name'] ?? ucfirst($slug),
                'price' => $tier['price'] ?? 0,
                'searches_limit' => $tier['searches'] ?? 0,
                'duration_days' => $tier['duration_days'] ?? 0,
                'notification_channels' => json_encode($tier['channels'] ?? []),
                'priority_order' => $tier['priority_order'] ?? 0,
                'delay_hours' => $tier['delay_hours'] ?? 0,
                'exclusive_window_hours' => $tier['exclusive_window_hours'] ?? null,
                'description' => $tier['description'] ?? null,
                'is_active' => $tier['is_active'] ?? true,
                'sort_order' => $tier['sort_order'] ?? $sort,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $sort++;
        }

        DB::table('smart_search_plans')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_search_plans');
    }
};
