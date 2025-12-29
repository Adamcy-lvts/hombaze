<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Agent;
use App\Models\SalesAgreementTemplate;
use App\Models\User;
use Illuminate\Console\Command;

class SeedSalesAgreementTemplates extends Command
{
    protected $signature = 'sales-agreements:seed-templates {--landlords} {--agencies} {--agents} {--all}';

    protected $description = 'Seed default sales agreement templates for landlords, agencies, and agents.';

    public function handle(): int
    {
        $seedAll = $this->option('all') || (! $this->option('landlords') && ! $this->option('agencies') && ! $this->option('agents'));

        $landlordsSeeded = 0;
        $agenciesSeeded = 0;
        $agentsSeeded = 0;

        if ($seedAll || $this->option('landlords')) {
            User::where('user_type', 'property_owner')
                ->select('id')
                ->chunk(200, function ($users) use (&$landlordsSeeded) {
                    foreach ($users as $user) {
                        if (! SalesAgreementTemplate::getDefaultTemplate($user->id)) {
                            SalesAgreementTemplate::ensureDefaultForLandlord($user->id);
                            $landlordsSeeded++;
                        }
                    }
                });
        }

        if ($seedAll || $this->option('agencies')) {
            Agency::select('id')
                ->chunk(200, function ($agencies) use (&$agenciesSeeded) {
                    foreach ($agencies as $agency) {
                        if (! SalesAgreementTemplate::getDefaultTemplate(null, $agency->id)) {
                            SalesAgreementTemplate::ensureDefaultForAgency($agency->id);
                            $agenciesSeeded++;
                        }
                    }
                });
        }

        if ($seedAll || $this->option('agents')) {
            Agent::select('id')
                ->chunk(200, function ($agents) use (&$agentsSeeded) {
                    foreach ($agents as $agent) {
                        if (! SalesAgreementTemplate::getDefaultTemplate(null, null, $agent->id)) {
                            SalesAgreementTemplate::ensureDefaultForAgent($agent->id);
                            $agentsSeeded++;
                        }
                    }
                });
        }

        $this->info("Sales agreement templates ensured.");
        $this->line("Landlords: {$landlordsSeeded}");
        $this->line("Agencies: {$agenciesSeeded}");
        $this->line("Agents: {$agentsSeeded}");

        return self::SUCCESS;
    }
}
