<?php

namespace App\Filament\Tenant\Resources\RentPaymentResource\Pages;

use App\Filament\Tenant\Resources\RentPaymentResource;
use Exception;
use App\Models\RentPayment;
use NumberToWords\NumberToWords;
use Filament\Resources\Pages\Page;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;
use Spatie\LaravelPdf\Enums\Orientation;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class ViewReceipt extends Page
{
    protected static string $resource = RentPaymentResource::class;
    
    protected string $view = 'filament.tenant.resources.rent-payment-resource.pages.view-receipt';
    
    public $receipt;
    public $amountInWords;

    public function mount($record)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'Access denied - Tenant profile not found');
        }

        // Ensure the receipt belongs to the current tenant
        $this->receipt = RentPayment::where('id', $record)
            ->where('tenant_id', $tenant->id)
            ->with(['property', 'lease', 'tenant', 'landlord'])
            ->firstOrFail();

        try {
            // Convert amount to words
            $this->amountInWords = $this->convertAmountToWords($this->receipt->amount);
        } catch (Exception $e) {
            Log::error('Error converting amount to words: ' . $e->getMessage());
            $this->amountInWords = 'Amount in words not available';
        }
    }

    public function getTitle(): string
    {
        return 'Receipt - ' . $this->receipt->receipt_number;
    }

    /**
     * Download receipt as PDF
     */
    public function downloadPdf()
    {
        try {
            // Set company logo to null - will use placeholder in template
            $companyLogo = null;

            // Generate QR code with view receipt URL
            $qrData = route('receipt.view', $this->receipt->id);
            $qrCodeSvg = QrCode::size(60)->encoding('UTF-8')->generate($qrData);

            // Create a unique filename
            $fileName = sprintf(
                'rent-receipt_%s_%s.pdf',
                $this->receipt->receipt_number,
                now()->format('Ymd-His')
            );

            // Create directory if it doesn't exist
            $directory = storage_path("app/public/rent-receipts/" . date('Y/m'));
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $fileName;

            try {
                // Generate PDF using the same template as landlord panel
                $pdf = Pdf::view('filament.tenant.pages.rent-receipt-pdf', [
                    'receipt' => $this->receipt,
                    'qrCode' => $qrCodeSvg,
                    'amountInWords' => $this->amountInWords,
                    'companyLogo' => $companyLogo,
                    'isPdfMode' => true,
                ])
                    ->format('a4')
                    // Set orientation to landscape
                    ->orientation(Orientation::Landscape)
                    ->withBrowsershot(function (Browsershot $browsershot) {
                        $browsershot->setChromePath(resolveChromePath())
                            ->setTemporaryDirectory(resolveBrowsershotTempDir())
                            ->format('A4')
                            ->landscape() // Ensure landscape mode is set
                            ->margins(5, 5, 5, 5) // Minimal margins to maximize content area
                            ->showBackground()
                            ->waitUntilNetworkIdle() // Wait for Tailwind to initialize
                            ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36')
                            ->deviceScaleFactor(1.5) // Higher resolution
                            ->timeout(120)
                            ->showBrowserHeaderAndFooter()
                            ->hideHeader()
                            ->footerHtml('<div style="text-align: center; font-size: 8px; color: #9ca3af; font-family: Inter, system-ui, sans-serif; font-weight: bold; opacity: 0.7; padding: 4px 0; width: 100%; display: block;">Generated via HomeBaze Property Management System | Powered by DevCentric</div>')
                            ->noSandbox();
                    });

                $pdf->save($filePath);

                if (!File::exists($filePath)) {
                    throw new Exception("PDF file was not created at: {$filePath}");
                }

                // Log successful PDF generation
                Log::info('PDF Rent Receipt Generated Successfully', [
                    'file' => $filePath,
                    'size' => File::size($filePath),
                    'receipt_id' => $this->receipt->id,
                    'receipt_number' => $this->receipt->receipt_number,
                    'logo_included' => !is_null($companyLogo)
                ]);

                Notification::make()
                    ->title('Rent Receipt PDF generated successfully')
                    ->success()
                    ->send();

                return response()->download($filePath, $fileName, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
                ])->deleteFileAfterSend(true);
            } catch (Exception $e) {
                Log::error('PDF Generation Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'receipt_id' => $this->receipt->id
                ]);

                throw $e;
            }
        } catch (Exception $e) {
            Log::error('Rent Receipt Download Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'receipt_id' => $this->receipt->id ?? null
            ]);

            Notification::make()
                ->title('Error generating rent receipt PDF')
                ->body('An error occurred while generating the PDF. Please try again.')
                ->danger()
                ->persistent()
                ->send();

            return null;
        }
    }

    /**
     * Download receipt as PNG
     */
    public function downloadPng()
    {
        try {
            // Set company logo to null - will use placeholder in template
            $companyLogo = null;

            // Generate QR code with view receipt URL
            $qrData = route('receipt.view', $this->receipt->id);
            $qrCodeSvg = QrCode::size(60)->encoding('UTF-8')->generate($qrData);

            // Create a unique filename
            $fileName = sprintf(
                'rent-receipt_%s_%s.png',
                $this->receipt->receipt_number,
                now()->format('Ymd-His')
            );

            // Create directory if it doesn't exist
            $directory = storage_path("app/public/rent-receipts/" . date('Y/m'));
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $fileName;

            try {
                // Add CSS to ensure no extra space at the bottom
                $customCss = '
                <style>
                    html, body {
                        margin: 0;
                        padding: 0;
                        overflow: hidden;
                        width: 100%;
                    }
                    #capture-area {
                        display: inline-block;
                        box-sizing: border-box;
                        width: 100%;
                        max-width: 1280px;
                    }
                    @media print {
                        body {
                            margin: 0;
                            padding: 0;
                        }
                        #capture-area {
                            page-break-inside: avoid;
                        }
                    }
                </style>
            ';

                // Create HTML content with wrapper already prepared in the view
                $html = view('filament.tenant.pages.rent-receipt-pdf', [
                    'receipt' => $this->receipt,
                    'qrCode' => $qrCodeSvg,
                    'amountInWords' => $this->amountInWords,
                    'companyLogo' => $companyLogo,
                    'isPngMode' => true,
                    'customCss' => $customCss
                ])->render();

                // Configure Browsershot for PNG
                $browsershot = Browsershot::html($html)
                    ->setChromePath(resolveChromePath())
                    ->setTemporaryDirectory(resolveBrowsershotTempDir())
                    ->windowSize(900, 500) // Wider window for wider receipt
                    ->waitUntilNetworkIdle() // Wait for Tailwind to initialize
                    ->showBackground()
                    ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36')
                    ->deviceScaleFactor(2) // Higher resolution for better quality
                    ->timeout(120)
                    ->noSandbox()
                    ->waitForFunction('document.fonts.ready') // Wait for fonts to load properly
                    // Use select to capture only the receipt itself
                    ->select('#capture-area')
                    ->ignoreHttpsErrors(); // Ignore SSL errors

                // Save PNG without any extra whitespace
                $browsershot->save($filePath);

                if (!File::exists($filePath)) {
                    throw new Exception("PNG file was not created at: {$filePath}");
                }

                // Log successful PNG generation
                Log::info('PNG Rent Receipt Generated Successfully', [
                    'file' => $filePath,
                    'size' => File::size($filePath),
                    'receipt_id' => $this->receipt->id,
                    'receipt_number' => $this->receipt->receipt_number,
                    'logo_included' => !is_null($companyLogo)
                ]);

                Notification::make()
                    ->title('Rent Receipt PNG generated successfully')
                    ->success()
                    ->send();

                return response()->download($filePath, $fileName, [
                    'Content-Type' => 'image/png',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
                ])->deleteFileAfterSend(true);
            } catch (Exception $e) {
                Log::error('PNG Generation Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'receipt_id' => $this->receipt->id
                ]);

                throw $e;
            }
        } catch (Exception $e) {
            Log::error('Rent Receipt PNG Download Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'receipt_id' => $this->receipt->id ?? null
            ]);

            Notification::make()
                ->title('Error generating rent receipt PNG')
                ->body('An error occurred while generating the PNG. Please try again.')
                ->danger()
                ->persistent()
                ->send();

            return null;
        }
    }

    /**
     * Generate QR code for the receipt
     */
    public function generateQrCode()
    {
        try {
            $qrData = route('receipt.view', $this->receipt->id);
            return QrCode::size(80)->encoding('UTF-8')->generate($qrData);
        } catch (Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert a decimal amount to words with proper currency formatting
     *
     * @param float $amount The amount to convert
     * @return string The amount in words
     */
    private function convertAmountToWords($amount)
    {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getCurrencyTransformer('en');

        // Convert to kobo (smallest unit) for the transformer
        $amountInKobo = (int)($amount * 100);

        // Convert to words
        $amountInWords = $numberTransformer->toWords($amountInKobo, 'NGN');

        // Replace any currency-specific text with our preferred format
        $amountInWords = str_replace('Nairas', 'Naira', $amountInWords);
        $amountInWords = str_replace('nairas', 'Naira', $amountInWords);
        $amountInWords = str_replace('cent', 'Kobo', $amountInWords);
        $amountInWords = str_replace('cents', 'Kobo', $amountInWords);

        return ucfirst($amountInWords);
    }

}
