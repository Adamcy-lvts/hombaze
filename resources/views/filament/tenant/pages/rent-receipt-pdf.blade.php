<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background-color: #fff;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e5e5;
        }

        .logo-section {
            flex: 2;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f97316, #dc2626);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .logo-icon span {
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
        }

        .company-tagline {
            font-size: 11px;
            color: #6b7280;
        }

        .company-info {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.5;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .receipt-number {
            font-size: 14px;
            color: #6b7280;
            font-family: monospace;
        }

        .qr-section {
            text-align: center;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            border: 1px solid #d1d5db;
            padding: 5px;
            background: white;
        }

        .qr-text {
            font-size: 9px;
            color: #6b7280;
            margin-top: 5px;
        }

        .content {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-section {
            flex: 1;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e5e5;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 11px;
        }

        .info-value {
            color: #1f2937;
            font-size: 11px;
            margin-top: 2px;
        }

        .payment-summary {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .summary-grid {
            display: flex;
            gap: 30px;
        }

        .amount-details {
            flex: 1;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .amount-row.total {
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            margin-top: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        .amount-row.total .amount {
            color: #059669;
            font-size: 16px;
        }

        .amount-words {
            font-size: 9px;
            color: #6b7280;
            margin-top: 10px;
        }

        .payment-methods {
            flex: 1;
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 11px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .checkbox {
            width: 12px;
            height: 12px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            background: #fff;
            position: relative;
        }

        .checkbox.checked::after {
            content: '✓';
            position: absolute;
            top: -2px;
            left: 1px;
            font-size: 10px;
            color: #2563eb;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                @php
                    $businessInfo = null;
                    $businessInitials = 'HB';

                    // Try to get agency info from property
                    if ($receipt->lease && $receipt->lease->property && $receipt->lease->property->agency) {
                        $agency = $receipt->lease->property->agency;
                        $businessInfo = [
                            'name' => $agency->name,
                            'email' => $agency->email ?? 'support@homebaze.com',
                            'phone' => $agency->phone ?? '+234 (0) 123-456-7890',
                            'website' => $agency->website ?? 'www.homebaze.com',
                            'tagline' => 'Real Estate Agency'
                        ];
                        $businessInitials = strtoupper(substr($agency->name, 0, 2));
                    } else {
                        // Fallback to HomeBaze default
                        $businessInfo = [
                            'name' => 'HomeBaze',
                            'email' => 'support@homebaze.com',
                            'phone' => '+234 (0) 123-456-7890',
                            'website' => 'www.homebaze.com',
                            'tagline' => 'Property Management Platform'
                        ];
                    }
                @endphp

                <div class="logo">
                    <div class="logo-icon">
                        <span>{{ $businessInitials }}</span>
                    </div>
                    <div>
                        <div class="company-name">{{ $businessInfo['name'] }}</div>
                        <div class="company-tagline">{{ $businessInfo['tagline'] }}</div>
                    </div>
                </div>
                <div class="company-info">
                    Email: {{ $businessInfo['email'] }}<br>
                    Phone: {{ $businessInfo['phone'] }}<br>
                    Website: {{ $businessInfo['website'] }}
                </div>
            </div>

            <div class="receipt-header">
                <div class="receipt-title">RENT PAYMENT RECEIPT</div>
                <div class="receipt-number">#{{ $receipt->receipt_number }}</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Property Information -->
            <div class="info-section">
                <div class="section-title">Property Information</div>
                <div class="info-item">
                    <div class="info-label">Property:</div>
                    <div class="info-value">{{ $receipt->property->title ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $receipt->property->address ?? 'N/A' }}</div>
                </div>
                @if($receipt->lease)
                    <div class="info-item">
                        <div class="info-label">Lease Period:</div>
                        <div class="info-value">
                            {{ $receipt->lease->start_date->format('M d, Y') }} - {{ $receipt->lease->end_date->format('M d, Y') }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Payment Details -->
            <div class="info-section">
                <div class="section-title">Payment Details</div>
                <div class="info-item">
                    <div class="info-label">Payment Date:</div>
                    <div class="info-value">{{ $receipt->payment_date->format('F d, Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Due Date:</div>
                    <div class="info-value">{{ $receipt->due_date->format('F d, Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Period:</div>
                    <div class="info-value">{{ $receipt->payment_for_period ?? 'N/A' }}</div>
                </div>
                @if($receipt->payment_reference)
                    <div class="info-item">
                        <div class="info-label">Reference:</div>
                        <div class="info-value">{{ $receipt->payment_reference }}</div>
                    </div>
                @endif
            </div>

            <!-- Tenant Information -->
            <div class="info-section">
                <div class="section-title">Tenant Information</div>
                <div class="info-item">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{ $receipt->tenant->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $receipt->tenant->email ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $receipt->tenant->phone ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="payment-summary">
            <div class="summary-grid">
                <div class="amount-details">
                    <div class="section-title">Amount Details</div>
                    <div class="amount-row">
                        <span>Net Amount:</span>
                        <span>₦{{ number_format($receipt->net_amount ?? $receipt->amount, 2) }}</span>
                    </div>
                    @if($receipt->deposit && $receipt->deposit > 0)
                        <div class="amount-row">
                            <span>Deposit:</span>
                            <span>₦{{ number_format($receipt->deposit, 2) }}</span>
                        </div>
                    @endif
                    @if($receipt->balance_due && $receipt->balance_due > 0)
                        <div class="amount-row">
                            <span>Balance Due:</span>
                            <span style="color: #dc2626;">₦{{ number_format($receipt->balance_due, 2) }}</span>
                        </div>
                    @endif
                    @if($receipt->late_fee && $receipt->late_fee > 0)
                        <div class="amount-row">
                            <span>Late Fee:</span>
                            <span>₦{{ number_format($receipt->late_fee, 2) }}</span>
                        </div>
                    @endif
                    <div class="amount-row total">
                        <span>Total Amount:</span>
                        <span class="amount">₦{{ number_format($receipt->amount, 2) }}</span>
                    </div>
                    <div class="amount-words">
                        <strong>Amount in Words:</strong> {{ $amountInWords }}
                    </div>
                </div>

                <div class="payment-methods">
                    <div class="section-title">Payment Method</div>
                    <div class="checkbox-grid">
                        <div class="checkbox-item">
                            <div class="checkbox {{ $receipt->payment_method === 'cash' ? 'checked' : '' }}"></div>
                            <span>Cash</span>
                        </div>
                        <div class="checkbox-item">
                            <div class="checkbox {{ $receipt->payment_method === 'transfer' ? 'checked' : '' }}"></div>
                            <span>Transfer</span>
                        </div>
                        <div class="checkbox-item">
                            <div class="checkbox {{ $receipt->payment_method === 'pos' ? 'checked' : '' }}"></div>
                            <span>POS</span>
                        </div>
                        <div class="checkbox-item">
                            <div class="checkbox {{ $receipt->payment_method === 'card' ? 'checked' : '' }}"></div>
                            <span>Card</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Section -->
        <div style="text-align: center; margin: 20px 0; padding: 15px;">
            <div style="display: inline-block; border: 1px solid #d1d5db; padding: 8px; border-radius: 6px; background: white;">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->generate(route('receipt.view', $receipt->id)) !!}
            </div>
            <div style="font-size: 10px; color: #6b7280; margin-top: 8px;">
                <strong>Scan to verify receipt</strong><br>
                Receipt #{{ $receipt->receipt_number }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This is an automated receipt generated by {{ $businessInfo['name'] }}.<br>
            For inquiries, please contact {{ $businessInfo['email'] }}
        </div>
    </div>
</body>
</html>