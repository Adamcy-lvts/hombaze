<?php

namespace App\Console\Commands;

use App\Models\LeaseTemplate;
use App\Models\User;
use Illuminate\Console\Command;

class SeedLeaseTemplates extends Command
{
    protected $signature = 'leases:seed-templates {--landlords} {--all}';

    protected $description = 'Seed default lease templates for landlords.';

    public function handle(): int
    {
        $seedAll = $this->option('all') || (! $this->option('landlords'));

        $landlordsSeeded = 0;

        if ($seedAll || $this->option('landlords')) {
            User::where('user_type', 'property_owner')
                ->select('id')
                ->chunk(200, function ($users) use (&$landlordsSeeded) {
                    foreach ($users as $user) {
                        if (! LeaseTemplate::getDefaultTemplate($user->id)) {
                            LeaseTemplate::ensureDefaultForLandlord($user->id);
                            $landlordsSeeded++;
                        }
                    }
                });
        }

        $this->info('Lease templates ensured.');
        $this->line("Landlords: {$landlordsSeeded}");

        return self::SUCCESS;
    }
}
