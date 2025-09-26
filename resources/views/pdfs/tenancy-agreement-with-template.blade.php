<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Agreement</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1f2937;
            margin: 0;
            padding: 0;
            font-weight: 400;
        }

        /* Custom print styles */
        @media print {
            .break-inside-avoid {
                break-inside: avoid;
            }

            .break-before-page {
                break-before: page;
            }

            .break-after-page {
                break-after: page;
            }
        }

        /* Enhanced typography */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Inter', system-ui, sans-serif;
            font-weight: 600;
        }

        /* Smooth borders and rounded corners for PDF */
        .rounded-sm {
            border-radius: 2px;
        }

        /* Fix for ordered list numbering in Tailwind CSS */
        ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
        }

        ol ol {
            list-style-type: lower-alpha;
            padding-left: 1.5rem;
        }

        ol ol ol {
            list-style-type: lower-roman;
            padding-left: 1.5rem;
        }

        ul {
            list-style-type: disc;
            padding-left: 1.5rem;
        }

        ul ul {
            list-style-type: circle;
            padding-left: 1.5rem;
        }

        li {
            margin-bottom: 0.25rem;
        }
    </style>
</head>

<body class="bg-white text-gray-900 antialiased">
    <div class="w-full">
        {{-- PDF Component-based Layout --}}
        @include('pdf-lease-components.header')
        @include('pdf-lease-components.property-info', ['property' => $record->property])
        @include('pdf-lease-components.parties-info', [
            'landlord' => $record->landlord,
            'tenant' => $record->tenant,
        ])
        @include('pdf-lease-components.lease-terms', ['lease' => $record])

        @include('pdf-lease-components.terms-conditions', ['content' => $content])

        @include('pdf-lease-components.signature', [
            'landlord' => $record->landlord,
            'tenant' => $record->tenant,
            'config' => ['show_witness' => false] // Set to true if witnesses needed
        ])

        <!-- Replace your template info section with this -->
        {{-- @if ($template)
            <div class="mt-3 py-1.5 px-2 bg-slate-50 border-l-4 border-slate-400 text-xs text-slate-700">
                <div class="flex justify-between items-center">
                    <span>Template: {{ $template->name }}</span>
                    <span class="text-slate-500">{{ now()->format('M j, Y g:i A') }}</span>
                </div>
            </div>
        @else
            <div class="mt-3 py-1.5 px-2 bg-slate-50 border-l-4 border-slate-400 text-xs text-slate-700">
                <div class="flex justify-between items-center">
                    <span>Template: Default</span>
                    <span class="text-slate-500">{{ now()->format('M j, Y g:i A') }}</span>
                </div>
            </div>
        @endif --}}

        <!-- Replace your footer section with this -->
        <div class="mt-2 pt-1.5 border-t border-gray-200 text-center text-xs text-gray-500">
            Generated via HomeBaze Property Management System
        </div>
    </div>
</body>

</html>
