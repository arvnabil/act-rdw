<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[PREVIEW] {{ $meta['title'] }}</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        #preview-banner {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 99999;
            background: #f59e0b;
            color: #1c1917;
            font-family: system-ui, sans-serif;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        #preview-banner .badge {
            background: #1c1917;
            color: #f59e0b;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        #preview-banner a {
            color: #1c1917;
            text-decoration: underline;
            font-size: 12px;
            margin-left: auto;
        }

        /* The iframe fills the entire viewport below the banner */
        #preview-frame {
            position: fixed;
            top: 40px; left: 0; right: 0; bottom: 0;
            width: 100%;
            height: calc(100vh - 40px);
            border: none;
        }
    </style>
</head>
<body>
    <div id="preview-banner">
        <span>⚠ PREVIEW MODE — Not published</span>
        <span class="badge">{{ strtoupper($campaign->status) }}</span>
        <span>{{ $campaign->name }}</span>
        <a href="{{ url()->previous(fallback: '/activioncms/campaigns') }}">← Back to Admin</a>
    </div>

    {{--
        Render campaign HTML inside a full-viewport iframe using srcdoc.
        This gives a pixel-perfect 1:1 preview of the campaign's own HTML,
        CSS variables, fonts, and animations — completely isolated from the
        admin panel styles. No sanitization here; preview is auth-protected.
    --}}
    <iframe
        id="preview-frame"
        srcdoc="{{ htmlspecialchars($rawHtml, ENT_QUOTES, 'UTF-8') }}"
        sandbox="allow-scripts allow-same-origin"
        title="Campaign Preview: {{ $campaign->name }}"
    ></iframe>
</body>
</html>