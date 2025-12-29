@php
    $inlineLogo = function (string $relativePath): ?string {
        $fullPath = public_path($relativePath);
        if (! is_file($fullPath)) {
            return null;
        }
        $contents = file_get_contents($fullPath);
        if ($contents === false) {
            return null;
        }
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = $extension === 'svg' ? 'image/svg+xml' : "image/{$extension}";
        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    };

    $logoData = $inlineLogo('images/app-logo.svg');
@endphp

<div style="text-align: center; margin-bottom: 25px;">
    <div style="display: inline-flex; width: 50px; height: 50px; align-items: center; justify-content: center; margin-bottom: 10px;">
        @if ($logoData)
            <img src="{{ $logoData }}" alt="HomeBaze" style="width: 50px; height: 50px; display: block;" />
        @else
            <div style="display: inline-block; width: 45px; height: 45px; background-color: #3b82f6; border-radius: 10px; color: white; line-height: 45px; font-size: 24px; font-weight: 900; text-align: center;">
                <span style="opacity: 0.9;">H</span>
            </div>
        @endif
    </div>
    <h1 style="font-size: 20pt; font-weight: 900; color: #111827; margin: 0; text-transform: uppercase; letter-spacing: 2px; line-height: 1;">Sales Agreement</h1>
    <div style="margin-top: 5px; font-size: 9pt; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">
        Property: {{ $record->property?->title ?? 'Real Estate Asset' }}
    </div>
</div>
