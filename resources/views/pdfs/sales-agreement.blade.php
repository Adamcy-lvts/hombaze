<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Agreement</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            margin: 10mm;
            size: A4;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .document-wrapper {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }

        {{-- Force page breaks before large sections if they are likely to be cut --}}
        .page-break {
            page-break-before: always;
        }

        {{-- Prevent titles from being separated from their content --}}
        h1, h2, h3, h4 {
            page-break-after: avoid;
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">
    <div class="document-wrapper">
        @include('pdf-sales-components.header', ['record' => $record])
        @include('pdf-sales-components.property-info', ['property' => $record->property])
        {{-- @include('pdf-sales-components.parties-info', ['record' => $record]) --}}
        {{-- @include('pdf-sales-components.sale-terms', ['record' => $record]) --}}
        @include('pdf-sales-components.terms-conditions', ['content' => $content])
        @include('pdf-sales-components.signature', ['record' => $record])
    </div>
</body>
</html>
