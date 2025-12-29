<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Agent;
use App\Models\SalesAgreementTemplate;
use App\Models\User;
use Illuminate\Console\Command;

class RefreshSalesAgreementDefaults extends Command
{
    protected $signature = 'sales-agreements:refresh-defaults {--landlords} {--agencies} {--agents} {--all} {--overwrite}';

    protected $description = 'Ensure the latest default sales agreement template exists, and optionally overwrite existing defaults.';

    public function handle(): int
    {
        $seedAll = $this->option('all') || (! $this->option('landlords') && ! $this->option('agencies') && ! $this->option('agents'));
        $overwrite = $this->option('overwrite');

        $landlordsCreated = 0;
        $landlordsUpdated = 0;
        $agenciesCreated = 0;
        $agenciesUpdated = 0;
        $agentsCreated = 0;
        $agentsUpdated = 0;

        if ($seedAll || $this->option('landlords')) {
            User::where('user_type', 'property_owner')
                ->select('id')
                ->chunk(200, function ($users) use (&$landlordsCreated, &$landlordsUpdated, $overwrite) {
                    foreach ($users as $user) {
                        $template = SalesAgreementTemplate::getDefaultTemplate($user->id);
                        if (! $template) {
                            SalesAgreementTemplate::ensureDefaultForLandlord($user->id);
                            $landlordsCreated++;
                            continue;
                        }

                        if ($overwrite) {
                            $this->updateDefaultTemplate($template);
                            $landlordsUpdated++;
                        }
                    }
                });
        }

        if ($seedAll || $this->option('agencies')) {
            Agency::select('id')
                ->chunk(200, function ($agencies) use (&$agenciesCreated, &$agenciesUpdated, $overwrite) {
                    foreach ($agencies as $agency) {
                        $template = SalesAgreementTemplate::getDefaultTemplate(null, $agency->id);
                        if (! $template) {
                            SalesAgreementTemplate::ensureDefaultForAgency($agency->id);
                            $agenciesCreated++;
                            continue;
                        }

                        if ($overwrite) {
                            $this->updateDefaultTemplate($template);
                            $agenciesUpdated++;
                        }
                    }
                });
        }

        if ($seedAll || $this->option('agents')) {
            Agent::select('id')
                ->chunk(200, function ($agents) use (&$agentsCreated, &$agentsUpdated, $overwrite) {
                    foreach ($agents as $agent) {
                        $template = SalesAgreementTemplate::getDefaultTemplate(null, null, $agent->id);
                        if (! $template) {
                            SalesAgreementTemplate::ensureDefaultForAgent($agent->id);
                            $agentsCreated++;
                            continue;
                        }

                        if ($overwrite) {
                            $this->updateDefaultTemplate($template);
                            $agentsUpdated++;
                        }
                    }
                });
        }

        $this->info('Sales agreement defaults refreshed.');
        $this->line("Landlords: created {$landlordsCreated}, updated {$landlordsUpdated}");
        $this->line("Agencies: created {$agenciesCreated}, updated {$agenciesUpdated}");
        $this->line("Agents: created {$agentsCreated}, updated {$agentsUpdated}");

        if (! $overwrite) {
            $this->comment('Tip: re-run with --overwrite to replace existing default template content.');
        }

        return self::SUCCESS;
    }

    private function updateDefaultTemplate(SalesAgreementTemplate $template): void
    {
        $template->terms_and_conditions = SalesAgreementTemplate::getDefaultContent();
        $template->available_variables = $template->extractUsedVariables();
        $template->save();
    }
}
