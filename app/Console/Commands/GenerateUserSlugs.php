<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateUserSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-slugs {--force : Force regeneration of existing slugs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique slugs for all users without slugs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        $query = User::query();

        if (!$force) {
            $query->whereNull('slug');
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info('No users need slug generation.');
            return 0;
        }

        $this->info("Processing {$users->count()} users...");

        $progressBar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            $baseSlug = Str::slug($user->name);
            $slug = $baseSlug;
            $counter = 1;

            // Ensure uniqueness
            while (User::where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $user->update(['slug' => $slug]);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully generated slugs for {$users->count()} users.");

        return 0;
    }
}
