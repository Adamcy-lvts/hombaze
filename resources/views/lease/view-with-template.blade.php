<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Agreement - {{ $record->property->title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        
        .header p {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 1.1em;
        }
        
        .property-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #28a745;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-section {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .info-section h3 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 1.2em;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 8px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            display: inline-block;
            width: 120px;
        }
        
        .info-value {
            color: #333;
        }
        
        .terms-section {
            margin-top: 40px;
            padding: 30px;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .terms-section h2 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 1.8em;
            text-align: center;
        }
        
        .terms-content {
            line-height: 1.8;
        }
        
        .terms-content h3 {
            color: #495057;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        
        .terms-content ol, .terms-content ul {
            margin-bottom: 20px;
            padding-left: 2rem;
        }

        .terms-content ol {
            list-style-type: decimal !important;
            list-style-position: outside !important;
            display: block !important;
        }

        .terms-content ol ol {
            list-style-type: lower-alpha !important;
            list-style-position: outside !important;
        }

        .terms-content ol ol ol {
            list-style-type: lower-roman !important;
            list-style-position: outside !important;
        }

        .terms-content ul {
            list-style-type: disc !important;
            list-style-position: outside !important;
            display: block !important;
        }

        .terms-content ul ul {
            list-style-type: circle !important;
            list-style-position: outside !important;
        }
        
        .terms-content li {
            margin-bottom: 8px;
            display: list-item !important;
            list-style-position: outside !important;
        }
        
        .template-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #e7f1ff;
            border-left: 4px solid #007bff;
            border-radius: 0 8px 8px 0;
            font-size: 0.9em;
            color: #004085;
        }
        
        .actions {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn-success {
            background-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #1e7e34;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active { background-color: #d4edda; color: #155724; }
        .status-draft { background-color: #d1ecf1; color: #0c5460; }
        .status-expired { background-color: #f8d7da; color: #721c24; }
        .status-terminated { background-color: #fff3cd; color: #856404; }
        
        @media print {
            body { background: white; }
            .container { box-shadow: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TENANCY AGREEMENT</h1>
            <p>Property Management Document</p>
        </div>

        <div class="property-info">
            <h2 style="margin: 0 0 15px 0; color: #495057;">Property Details</h2>
            <div style="font-size: 1.1em;">
                <strong>{{ $record->property->title }}</strong><br>
                {{ $record->property->address }}<br>
                @if($record->property->area){{ $record->property->area->name }}, @endif
                @if($record->property->city){{ $record->property->city->name }}, @endif
                @if($record->property->state){{ $record->property->state->name }}@endif
            </div>
        </div>

        <div class="info-grid">
            <div class="info-section">
                <h3>Landlord Information</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $record->landlord->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $record->landlord->email }}</span>
                </div>
            </div>

            <div class="info-section">
                <h3>Tenant Information</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $record->tenant->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $record->tenant->email }}</span>
                </div>
                @if($record->tenant->phone_number)
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $record->tenant->phone_number }}</span>
                </div>
                @endif
            </div>

            <div class="info-section">
                <h3>Lease Terms</h3>
                <div class="info-item">
                    <span class="info-label">Start Date:</span>
                    <span class="info-value">{{ $record->start_date?->format('F j, Y') ?? 'Not set' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">End Date:</span>
                    <span class="info-value">{{ $record->end_date?->format('F j, Y') ?? 'Not set' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Duration:</span>
                    <span class="info-value">
                        @if($record->start_date && $record->end_date)
                            {{ $record->start_date->diffInMonths($record->end_date) }} months
                        @else
                            Not calculated
                        @endif
                    </span>
                </div>
            </div>

            <div class="info-section">
                <h3>Financial Terms</h3>
                <div class="info-item">
                    <span class="info-label">Annual Rent:</span>
                    <span class="info-value">₦{{ number_format($record->monthly_rent, 2) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Payment:</span>
                    <span class="info-value">{{ ucfirst($record->payment_frequency) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-{{ $record->status }}">{{ ucfirst($record->status) }}</span>
                </div>
            </div>
        </div>

        <div class="terms-section">
            <h2>Terms and Conditions</h2>
            <div class="terms-content">
                {!! $content !!}
            </div>
        </div>

        @if($template)
        <div class="template-info">
            <strong>Template Used:</strong> {{ $template->name }}
            @if($template->description)
                <br><em>{{ $template->description }}</em>
            @endif
        </div>
        @else
        <div class="template-info">
            <strong>Template:</strong> Default Terms and Conditions
        </div>
        @endif

        <div class="actions">
            <a href="{{ route('landlord.lease.download-pdf', ['lease' => $record->id, 'template' => request('template')]) }}" 
               class="btn btn-success">
                Download PDF
            </a>
            <a href="javascript:window.close()" class="btn">Close</a>
        </div>
    </div>
</body>
</html>