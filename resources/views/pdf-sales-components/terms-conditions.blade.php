<div style="background-color: #ffffff; border: 1px solid #f1f5f9; border-radius: 24px; padding: 40px; margin-bottom: 40px;">
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">
        <span style="font-size: 16px; color: #94a3b8; font-weight: bold;">§</span>
        <h3 style="font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin: 0;">Standard & Special Provisions</h3>
    </div>

    @php
        $sanitizedContent = $content ?? '';
        $sanitizedContent = preg_replace('/<h1\\b[^>]*>.*?<\\/h1>/is', '', $sanitizedContent);
    @endphp
    <div style="font-size: 10pt; line-height: 1.6; color: #334155;" class="agreement-body">
        {!! $sanitizedContent !!}
    </div>
</div>

<style>
    .agreement-body h1, .agreement-body h2, .agreement-body h3 { color: #1e293b; margin-top: 1.5em; margin-bottom: 0.5em; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
    .agreement-body p { margin-bottom: 1.2em; }
    .agreement-body ol, .agreement-body ul { margin-bottom: 1.2em; padding-left: 1.5em; }
    .agreement-body li { margin-bottom: 0.6em; }
    .agreement-body strong { color: #0f172a; font-weight: 800; }
</style>
