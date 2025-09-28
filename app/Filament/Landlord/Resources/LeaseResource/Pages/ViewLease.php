<?php

namespace App\Filament\Landlord\Resources\LeaseResource\Pages;

use App\Filament\Landlord\Resources\LeaseResource;
use App\Models\LeaseTemplate;
use App\Models\Lease;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ViewLease extends ViewRecord
{
    protected static string $resource = LeaseResource::class;

    protected static string $view = 'filament.landlord.pages.lease-view';

    public ?array $data = [];
    public ?array $leaseDocument = null;

    public function getTitle(): string
    {
        return 'Tenancy Agreement - ' . $this->record->property->title;
    }



    public function mount($record): void
    {
        parent::mount($record);

        $this->data = [
            'template_id' => null // Default to no template selected
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Empty schema - content handled in custom view
            ]);
    }

    public function generateLeaseDocument(): void
    {
        $data = $this->data;

        try {
            $lease = $this->record;
            $template = null;

            if ($data['template_id']) {
                $template = LeaseTemplate::where('id', $data['template_id'])
                    ->where('landlord_id', Auth::id())
                    ->first();
            }

            if ($template) {
                // Use template with variable substitution
                $leaseContent = $template->substituteVariables([
                    'property_title' => $lease->property->title,
                    'property_address' => $lease->property->address,
                    'property_type' => $lease->property->propertyType->name ?? '',
                    'property_subtype' => $lease->property->propertySubtype->name ?? '',
                    'property_area' => $lease->property->area->name ?? '',
                    'property_city' => $lease->property->city->name ?? '',
                    'property_state' => $lease->property->state->name ?? '',
                    'landlord_name' => $lease->landlord->name,
                    'landlord_email' => $lease->landlord->email,
                    'landlord_phone' => $lease->landlord->phone_number ?? '',
                    'tenant_name' => $lease->tenant->name,
                    'tenant_email' => $lease->tenant->email,
                    'tenant_phone' => $lease->tenant->phone_number ?? '',
                    'lease_start_date' => $lease->start_date ? $lease->start_date->format('F j, Y') : '',
                    'lease_end_date' => $lease->end_date ? $lease->end_date->format('F j, Y') : '',
                    'lease_duration_months' => $lease->start_date && $lease->end_date
                        ? $lease->start_date->diffInMonths($lease->end_date) : '',
                    'rent_amount' => $lease->yearly_rent,
                    'payment_frequency' => $lease->payment_frequency,
                    'security_deposit' => $lease->security_deposit ?? 0,
                    'service_charge' => $lease->service_charge ?? 0,
                    'legal_fee' => $lease->legal_fee ?? 0,
                    'agency_fee' => $lease->agency_fee ?? 0,
                    'caution_deposit' => $lease->caution_deposit ?? 0,
                    'grace_period_days' => $lease->grace_period_days ?? 30,
                    'renewal_option' => $lease->renewal_option,
                    'signed_date' => $lease->signed_date ? $lease->signed_date->format('F j, Y') : '',
                    'current_date' => now()->format('F j, Y'),
                    'current_year' => now()->year,
                    'lease_status' => ucfirst($lease->status),
                ]);

                $this->leaseDocument = [
                    'template' => $template,
                    'content' => $leaseContent,
                    'lease' => $lease,
                    'generated_at' => now()
                ];
            } else {
                // Use default (stored terms and conditions)
                $this->leaseDocument = [
                    'template' => null,
                    'content' => $lease->terms_and_conditions ?? $this->getDefaultLeaseContent(),
                    'lease' => $lease,
                    'generated_at' => now()
                ];
            }

            Notification::make()
                ->success()
                ->title('Lease Document Generated')
                ->body('Generated with ' . ($template ? $template->name : 'default template'))
                ->send();
        } catch (\Exception $e) {
            $this->leaseDocument = [
                'error' => 'Failed to generate lease document: ' . $e->getMessage()
            ];

            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to generate lease document: ' . $e->getMessage())
                ->send();
        }
    }

    private function getDefaultLeaseContent(): string
    {
        return '
<h3>Standard Lease Terms</h3>
<ol>
<li>The tenant agrees to pay rent <strong>as specified</strong> in the financial terms above.</li>
<li>The tenant shall use the premises <strong>solely for residential purposes</strong> and shall not conduct any business activities without prior written consent from the landlord.</li>
<li>The tenant shall <strong>maintain the premises in good condition</strong> and shall be responsible for any damages beyond normal wear and tear.</li>
<li>The tenant shall <strong>not sublease, assign, or transfer</strong> any rights under this agreement without written consent from the landlord.</li>
<li>The tenant shall <strong>comply with all applicable laws, regulations, and community rules</strong> and shall not engage in any illegal activities on the premises.</li>
<li>The landlord shall <strong>maintain the structural integrity</strong> of the property and ensure all major systems (plumbing, electrical, etc.) are in working order.</li>
<li>Either party may <strong>terminate this agreement with 30 days written notice</strong>, subject to applicable local laws and regulations.</li>
<li>This lease renewal option will be determined based on the renewal option setting for this specific lease agreement.</li>
</ol>
        ';
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn() => filled($this->leaseDocument))
                ->action('downloadPdf'),
        ];
    }

    public function downloadPdf()
    {
        if (!$this->leaseDocument) {
            Notification::make()
                ->warning()
                ->title('No Document Generated')
                ->body('Please generate a lease document first')
                ->send();
            return;
        }

        try {
            $lease = $this->record;
            $template = $this->leaseDocument['template'] ?? null;

            $fileName = sprintf(
                'tenancy-agreement-%s-%s-%s.pdf',
                Str::slug($lease->property->title),
                Str::slug($lease->tenant->name),
                now()->format('Ymd-His')
            );

            $directory = storage_path("app/public/tenancy-agreements/" . date('Y/m'));
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $fileName;

            if ($template) {
                // Use template PDF view
                $pdf = Pdf::view('pdfs.tenancy-agreement-with-template', [
                    'record' => $lease,
                    'content' => $this->leaseDocument['content'],
                    'template' => $template,
                ]);
            } else {
                // Use original default template
                $pdf = Pdf::view('pdfs.tenancy-agreement', [
                    'record' => $lease,
                ]);
            }

            $pdf->format('a4')
                ->withBrowsershot(function (Browsershot $browsershot) {
                    $browsershot->setChromePath(config('app.chrome_path'))
                        ->format('A4')
                        ->margins(3, 5, 3, 5, 'px')
                        ->showBackground()
                        ->waitUntilNetworkIdle()
                        ->scale(1)
                        ->preferCssPageSize(true)
                        ->timeout(120)
                        ->showBrowserHeaderAndFooter()
                        ->hideHeader()
                        ->footerHtml('<div style="text-align: center; font-size: 8px; color: #9ca3af; font-family: Inter, system-ui, sans-serif; font-weight: bold; opacity: 0.7; padding: 4px 0; width: 100%; display: block;">Generated via ' . config('app.name', 'HomeBaze Property Management System') . '</div>')
                        ->setNodeBinary(config('app.browsershot.node_binary', '/usr/bin/node'))
                        ->setNpmBinary(config('app.browsershot.npm_binary', '/usr/bin/npm'));
                });

            $pdf->save($filePath);

            if (!File::exists($filePath)) {
                throw new \Exception("PDF file was not created at: {$filePath}");
            }

            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('PDF Download Failed', [
                'error' => $e->getMessage(),
                'lease_id' => $lease->id,
            ]);

            Notification::make()
                ->danger()
                ->title('Error')
                ->body('An error occurred while generating the PDF.')
                ->send();
        }
    }
}
