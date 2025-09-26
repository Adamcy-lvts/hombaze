<?php

namespace App\Filament\Tenant\Resources\RentPaymentResource\Pages;

use App\Models\RentPayment;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use NumberToWords\NumberToWords;
use Spatie\LaravelPdf\Facades\Pdf;

class ViewReceipt extends Page
{
    protected static string $resource = \App\Filament\Tenant\Resources\RentPaymentResource::class;
    
    protected static string $view = 'filament.tenant.resources.rent-payment-resource.pages.view-receipt';
    
    public RentPayment $receipt;
    public string $amountInWords = '';

    public function mount($record): void
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'Access denied - Tenant profile not found');
        }

        // Ensure the receipt belongs to the current tenant
        $this->receipt = RentPayment::where('id', $record)
            ->where('tenant_id', $tenant->id)
            ->with(['property', 'lease', 'tenant'])
            ->firstOrFail();

        // Convert amount to words
        $this->amountInWords = $this->convertAmountToWords($this->receipt->amount);
        
        // Auto-download PDF if requested
        if (request()->get('download') === 'pdf') {
            $this->downloadPdf();
            exit;
        }
    }

    /**
     * Convert amount to words
     */
    private function convertAmountToWords(float $amount): string
    {
        try {
            $numberToWords = new NumberToWords();
            $transformer = $numberToWords->getCurrencyTransformer('en');
            return ucfirst($transformer->toWords($amount * 100, 'NGN'));
        } catch (\Exception $e) {
            return 'Amount not available in words';
        }
    }

    /**
     * Generate QR Code for the receipt
     */
    public function generateQrCode(): string
    {
        try {
            return \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(100)
                ->encoding('UTF-8')
                ->generate(route('receipt.view', $this->receipt->id));
        } catch (\Exception $e) {
            return '<div class="w-24 h-24 bg-gray-200 flex items-center justify-center text-xs">QR Unavailable</div>';
        }
    }

    /**
     * Download receipt as PDF
     */
    public function downloadPdf()
    {
        try {
            $pdf = Pdf::view('filament.tenant.pages.rent-receipt-pdf', [
                'receipt' => $this->receipt,
                'amountInWords' => $this->amountInWords,
            ])
            ->format('a4')
            ->landscape()
            ->margins(10, 10, 10, 10);

            $filename = 'receipt-' . $this->receipt->receipt_number . '.pdf';
            
            return response()->streamDownload(
                fn() => print($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('PDF Generation Error')
                ->body('Unable to generate PDF: ' . $e->getMessage())
                ->send();
                
            return redirect()->back();
        }
    }

    /**
     * Download receipt as PNG
     */
    public function downloadPng()
    {
        try {
            // For PNG, we'll use the same PDF generation but convert to image
            // This is a simplified approach - for full PNG support, additional tools would be needed
            
            Notification::make()
                ->title('PNG Download')
                ->body('PNG download will be available soon. Please use PDF download for now.')
                ->warning()
                ->send();
                
            return $this->downloadPdf();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('PNG download is not available. Please use PDF download.')
                ->danger()
                ->send();
        }
    }

    public function getTitle(): string
    {
        return "Receipt #{$this->receipt->receipt_number}";
    }
}