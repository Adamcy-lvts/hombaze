<?php

namespace App\Filament\Agent\Resources\SalesAgreementResource\Pages;

use App\Filament\Agent\Resources\SalesAgreementResource;
use App\Models\SalesAgreement;
use App\Models\SalesAgreementTemplate;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class ViewSalesAgreement extends ViewRecord
{
    protected static string $resource = SalesAgreementResource::class;

    protected string $view = 'filament.agent.pages.sales-agreement-view';

    public ?array $data = [];
    public ?array $salesAgreementDocument = null;
    public int $debugClicks = 0;

    public function getTitle(): string
    {
        return 'Sales Agreement - ' . $this->record->property->title;
    }

    public function mount($record): void
    {
        parent::mount($record);

        $agentId = Auth::user()?->agentProfile?->id;
        $defaultTemplate = SalesAgreementTemplate::getDefaultTemplate(null, null, $agentId);
        $this->data = [
            'template_id' => $defaultTemplate?->id,
        ];
    }

    public function getAvailableTemplatesProperty()
    {
        $agentId = Auth::user()?->agentProfile?->id;

        if (! $agentId) {
            return collect();
        }

        return SalesAgreementTemplate::where('agent_id', $agentId)
            ->where('is_active', true)
            ->get();
    }

    public function generateSalesAgreementDocument(): void
    {
        $data = $this->data;

        try {
            /** @var SalesAgreement $agreement */
            $agreement = $this->record;
            $template = null;

            if (! empty($data['template_id'])) {
                $agentId = Auth::user()?->agentProfile?->id;
                $template = SalesAgreementTemplate::where('id', $data['template_id'])
                    ->where('agent_id', $agentId)
                    ->first();
            }

            $templateData = $this->getTemplateData($agreement);

            if ($template) {
                $content = $template->substituteVariables($templateData);
            } else {
                $contentToRender = $agreement->terms_and_conditions ?? $this->getDefaultSalesAgreementContent();
                $content = SalesAgreementTemplate::renderWithMergeTags($contentToRender, $templateData);
            }

            $this->salesAgreementDocument = [
                'template_id' => $template?->id,
                'template_name' => $template?->name,
                'template_description' => $template?->description,
                'content' => $content,
                'agreement_id' => $agreement->id,
                'generated_at' => now()->format('M d, Y'),
            ];

            Notification::make()
                ->success()
                ->title('Sales Agreement Generated')
                ->body('Generated with ' . ($template ? $template->name : 'default template'))
                ->send();
        } catch (Exception $e) {
            $this->salesAgreementDocument = [
                'error' => 'Failed to generate agreement: ' . $e->getMessage(),
            ];

            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to generate agreement: ' . $e->getMessage())
                ->send();
        }
    }

    private function getTemplateData(SalesAgreement $agreement): array
    {
        $property = $agreement->property;
        $buyer = $agreement->buyer;
        $owner = $property?->owner;
        $agent = $property?->agent;
        $agency = $property?->agency;

        $sellerName = $agreement->seller_name ?: $owner?->name;
        $sellerEmail = $agreement->seller_email ?: $owner?->email;
        $sellerPhone = $agreement->seller_phone ?: $owner?->phone;
        $sellerAddress = $agreement->seller_address ?: $owner?->address;

        $buyerName = $agreement->buyer_name ?: $buyer?->name;
        $buyerEmail = $agreement->buyer_email ?: $buyer?->email;
        $buyerPhone = $agreement->buyer_phone ?: $buyer?->phone;
        $buyerAddress = $agreement->buyer_address ?: $buyer?->address;

        return [
            'property_title' => $property?->title ?? '',
            'property_address' => $property?->address ?? '',
            'property_type' => $property?->propertyType?->name ?? '',
            'property_subtype' => $property?->propertySubtype?->name ?? '',
            'property_area' => $property?->area?->name ?? '',
            'property_city' => $property?->city?->name ?? '',
            'property_state' => $property?->state?->name ?? '',
            'seller_name' => $sellerName ?? '',
            'seller_email' => $sellerEmail ?? '',
            'seller_phone' => $sellerPhone ?? '',
            'seller_address' => $sellerAddress ?? '',
            'buyer_name' => $buyerName ?? '',
            'buyer_email' => $buyerEmail ?? '',
            'buyer_phone' => $buyerPhone ?? '',
            'buyer_address' => $buyerAddress ?? '',
            'sale_price' => $agreement->sale_price ?? $property?->price,
            'deposit_amount' => $agreement->deposit_amount ?? 0,
            'balance_amount' => $agreement->balance_amount ?? 0,
            'closing_date' => $agreement->closing_date ? $agreement->closing_date->format('F j, Y') : '',
            'signed_date' => $agreement->signed_date ? $agreement->signed_date->format('F j, Y') : '',
            'agreement_date' => $agreement->signed_date ? $agreement->signed_date->format('F j, Y') : now()->format('F j, Y'),
            'agent_name' => $agent?->user?->name ?? '',
            'agent_email' => $agent?->user?->email ?? '',
            'agent_phone' => $agent?->user?->phone ?? '',
            'agency_name' => $agency?->name ?? '',
            'agency_email' => $agency?->email ?? '',
            'agency_phone' => $agency?->phone ?? '',
            'current_date' => now()->format('F j, Y'),
            'current_year' => now()->year,
        ];
    }

    private function getDefaultSalesAgreementContent(): string
    {
        return SalesAgreementTemplate::getDefaultContent();
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('downloadPdf')
    //             ->label('Download PDF')
    //             ->icon('heroicon-o-arrow-down-tray')
    //             ->color('success')
    //             ->visible(fn () => filled($this->salesAgreementDocument))
    //             ->action('downloadPdf'),
    //     ];
    // }

    public function downloadPdf()
    {
        if (! $this->salesAgreementDocument) {
            Log::warning('Sales agreement download requested with no document (agent).', [
                'agreement_id' => $this->record?->id,
                'user_id' => Auth::id(),
            ]);
            Log::channel('single')->warning('Sales agreement download requested with no document (agent).', [
                'agreement_id' => $this->record?->id,
                'user_id' => Auth::id(),
            ]);
            Notification::make()
                ->warning()
                ->title('No Document Generated')
                ->body('Please generate a sales agreement first')
                ->send();
            return;
        }
        

        try {
            Log::info('Sales agreement download started (agent).', [
                'agreement_id' => $this->record?->id,
                'user_id' => Auth::id(),
                'template_id' => $this->salesAgreementDocument['template_id'] ?? null,
            ]);
            Log::channel('single')->info('Sales agreement download started (agent).', [
                'agreement_id' => $this->record?->id,
                'user_id' => Auth::id(),
                'template_id' => $this->salesAgreementDocument['template_id'] ?? null,
            ]);
            $agreement = $this->record;
            $templateId = $this->salesAgreementDocument['template_id'] ?? null;
            $template = null;
            if ($templateId) {
                $agentId = Auth::user()?->agentProfile?->id;
                $template = SalesAgreementTemplate::where('id', $templateId)
                    ->where('agent_id', $agentId)
                    ->first();
            }

            $fileName = sprintf(
                'sales-agreement-%s-%s-%s.pdf',
                Str::slug($agreement->property->title),
                Str::slug($agreement->buyer_name ?? 'buyer'),
                now()->format('Ymd-His')
            );

            $directory = storage_path('app/public/sales-agreements/' . date('Y/m'));
            if (! File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $fileName;

            if ($template) {
                $pdf = Pdf::view('pdfs.sales-agreement-with-template', [
                    'record' => $agreement,
                    'content' => $this->salesAgreementDocument['content'],
                    'template' => $template,
                ]);
            } else {
                $pdf = Pdf::view('pdfs.sales-agreement', [
                    'record' => $agreement,
                    'content' => $this->salesAgreementDocument['content'] ?? ($agreement->terms_and_conditions ?? null),
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

            if (! File::exists($filePath)) {
                throw new Exception("PDF file was not created at: {$filePath}");
            }

            Log::info('Sales agreement PDF generated (agent).', [
                'agreement_id' => $agreement->id ?? null,
                'file_path' => $filePath,
            ]);
            Log::channel('single')->info('Sales agreement PDF generated (agent).', [
                'agreement_id' => $agreement->id ?? null,
                'file_path' => $filePath,
            ]);

            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ])->deleteFileAfterSend(true);
        } catch (Exception $e) {
            Log::error('Sales Agreement PDF Download Failed', [
                'error' => $e->getMessage(),
                'agreement_id' => $agreement->id ?? null,
            ]);
            Log::channel('single')->error('Sales Agreement PDF Download Failed', [
                'error' => $e->getMessage(),
                'agreement_id' => $agreement->id ?? null,
            ]);

            Notification::make()
                ->danger()
                ->title('Error')
                ->body('An error occurred while generating the PDF.')
                ->send();
        }
    }

    public function handleDownload(): mixed
    {
        Log::channel('single')->info('Sales agreement handleDownload clicked (agent).', [
            'agreement_id' => $this->record?->id,
            'user_id' => Auth::id(),
        ]);

        return $this->downloadPdf();
    }

    public function debugPing(): mixed
    {
        $this->debugClicks++;
        Log::channel('single')->info('Sales agreement debug ping (agent).', [
            'agreement_id' => $this->record?->id,
            'user_id' => Auth::id(),
            'count' => $this->debugClicks,
        ]);
        return $this->downloadPdf();
    }
}
