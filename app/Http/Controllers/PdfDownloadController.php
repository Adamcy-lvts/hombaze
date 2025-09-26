<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\LeaseTemplate;
use App\Models\RentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class PdfDownloadController extends Controller
{
    public function downloadLeasePdf(Lease $lease, Request $request)
    {
        // Ensure the user owns this lease
        if ($lease->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to lease document.');
        }

        $templateId = $request->get('template');
        $template = null;
        
        if ($templateId) {
            $template = LeaseTemplate::where('id', $templateId)
                ->where('landlord_id', Auth::id())
                ->first();
        }

        try {
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
                // Use custom template with variable substitution
                $leaseContent = $this->generateLeaseContent($lease, $template);
                $pdf = Pdf::view('pdfs.tenancy-agreement-with-template', [
                    'record' => $lease,
                    'content' => $leaseContent,
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
                    ->margins(5, 5, 5, 5)
                    ->showBackground()
                    ->waitUntilNetworkIdle()
                    ->scale(1)
                    ->preferCssPageSize(true)
                    ->timeout(120)
                    ->setNodeBinary(config('app.browsershot.node_binary', '/usr/bin/node'))
                    ->setNpmBinary(config('app.browsershot.npm_binary', '/usr/bin/npm'))
                    ->showBrowserHeaderAndFooter()
                    ->hideHeader()
                    ->footerHtml('<div style="text-align: center; font-size: 8px; color: #9ca3af; font-family: Inter, system-ui, sans-serif; opacity: 0.7; padding: 4px 0;">Generated via HomeBaze Property Management System</div>');
            });
            
            $pdf->save($filePath);

            if (!File::exists($filePath)) {
                throw new \Exception("PDF file was not created at: {$filePath}");
            }

            Log::info('PDF Generated Successfully', [
                'file' => $filePath,
                'size' => File::size($filePath),
                'lease_id' => $lease->id,
                'template_id' => $templateId
            ]);

            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('PDF Download Failed', [
                'error' => $e->getMessage(),
                'lease_id' => $lease->id,
                'template_id' => $templateId,
            ]);

            abort(500, 'An error occurred while generating the PDF.');
        }
    }

    public function viewLeaseWithTemplate(Lease $lease, Request $request)
    {
        // Ensure the user owns this lease
        if ($lease->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to lease document.');
        }

        $templateId = $request->get('template');
        $template = null;
        
        if ($templateId) {
            $template = LeaseTemplate::where('id', $templateId)
                ->where('landlord_id', Auth::id())
                ->first();
        }

        if ($template) {
            // Use custom template with variable substitution
            $leaseContent = $this->generateLeaseContent($lease, $template);
            return view('lease.view-with-template', [
                'record' => $lease,
                'content' => $leaseContent,
                'template' => $template,
            ]);
        } else {
            // Use original default view (just redirect to the main lease view)
            return redirect()->route('filament.landlord.resources.leases.view', $lease);
        }
    }

    private function generateLeaseContent(Lease $lease, ?LeaseTemplate $template): string
    {
        if ($template) {
            // Use template with variable substitution
            return $template->substituteVariables([
                'property_title' => $lease->property->title,
                'property_address' => $lease->property->address,
                'property_type' => $lease->property->propertyType->name ?? '',
                'property_subtype' => $lease->property->propertySubtype->name ?? '',
                'property_area' => $lease->property->area->name ?? '',
                'property_city' => $lease->property->city->name ?? '',
                'property_state' => $lease->property->state->name ?? '',
                'landlord_name' => $lease->landlord->name,
                'landlord_email' => $lease->landlord->email,
                'tenant_name' => $lease->tenant->name,
                'tenant_email' => $lease->tenant->email,
                'tenant_phone' => $lease->tenant->phone_number ?? '',
                'lease_start_date' => $lease->start_date ? $lease->start_date->format('F j, Y') : '',
                'lease_end_date' => $lease->end_date ? $lease->end_date->format('F j, Y') : '',
                'lease_duration_months' => $lease->start_date && $lease->end_date 
                    ? $lease->start_date->diffInMonths($lease->end_date) : '',
                'rent_amount' => $lease->monthly_rent,
                'payment_frequency' => $lease->payment_frequency,
                'renewal_option' => $lease->renewal_option ? 'Yes' : 'No',
                'signed_date' => $lease->signed_date ? $lease->signed_date->format('F j, Y') : '',
                'current_date' => now()->format('F j, Y'),
                'lease_status' => ucfirst($lease->status),
                'security_deposit' => $lease->security_deposit ?? 0,
                'service_charge' => $lease->service_charge ?? 0,
            ]);
        } else {
            // Use default lease content
            return $lease->terms_and_conditions ?? $this->getDefaultLeaseContent();
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

    public function downloadReceiptPdf(RentPayment $payment)
    {
        // Ensure the user owns this payment record
        if ($payment->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        try {
            $fileName = sprintf(
                'payment-receipt-%s-%s.pdf',
                $payment->receipt_number,
                now()->format('Ymd-His')
            );

            $directory = storage_path("app/public/receipts/" . date('Y/m'));
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $fileName;

            $pdf = Pdf::view('pdfs.payment-receipt', [
                'record' => $payment,
            ])
            ->format('a4')
            ->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot->setChromePath(config('app.chrome_path'))
                    ->format('A4')
                    ->margins(5, 5, 5, 5)
                    ->showBackground()
                    ->waitUntilNetworkIdle()
                    ->scale(1)
                    ->preferCssPageSize(true)
                    ->timeout(120)
                    ->setNodeBinary(config('app.browsershot.node_binary', '/usr/bin/node'))
                    ->setNpmBinary(config('app.browsershot.npm_binary', '/usr/bin/npm'))
                    ->showBrowserHeaderAndFooter()
                    ->hideHeader()
                    ->footerHtml('<div style="text-align: center; font-size: 8px; color: #9ca3af; font-family: Inter, system-ui, sans-serif; opacity: 0.7; padding: 4px 0;">Generated via HomeBaze Property Management System</div>');
            });
            
            $pdf->save($filePath);

            if (!File::exists($filePath)) {
                throw new \Exception("PDF file was not created at: {$filePath}");
            }

            Log::info('Receipt PDF Generated Successfully', [
                'file' => $filePath,
                'size' => File::size($filePath),
                'payment_id' => $payment->id
            ]);

            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Receipt PDF Download Failed', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);

            abort(500, 'An error occurred while generating the receipt PDF.');
        }
    }
}