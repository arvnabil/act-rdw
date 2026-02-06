<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $settings = \App\Models\Setting::whereIn('key', ['seo_gtm_id', 'seo_ga4_id', 'seo_gsc_verification', 'seo_favicon'])->pluck(
            'value',
            'key',
        );
        $gtmId = $settings['seo_gtm_id'] ?? null;
        $ga4Id = $settings['seo_ga4_id'] ?? null;
        $gscCode = $settings['seo_gsc_verification'] ?? null;
        $favicon = $settings['seo_favicon'] ?? null;
    @endphp

    @if ($favicon)
        <link rel="shortcut icon" href="{{ asset('storage/' . $favicon) }}" type="image/x-icon" />
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $favicon) }}" />
    @else
        <link rel="shortcut icon" href="/assets/img/favicons/favicon.ico" type="image/x-icon" />
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicons/favicon-32x32.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicons/favicon-16x16.png" />
    @endif
    <link rel="manifest" href="/assets/img/favicons/manifest.json" />

    @if ($gscCode)
        <meta name="google-site-verification" content="{{ $gscCode }}" />
    @endif

    @if ($gtmId)
        <!-- Google Tag Manager -->
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ $gtmId }}');
        </script>
        <!-- End Google Tag Manager -->
    @endif

    @if ($ga4Id)
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $ga4Id }}');
        </script>
    @endif

    <!-- End of Qontak Webchat Script -->

    @routes
    @routes
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>

<body>
    @php
        $gtmIdBody = \App\Models\Setting::where('key', 'seo_gtm_id')->value('value');
    @endphp

    @if ($gtmIdBody)
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmIdBody }}" height="0"
                width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @endif

    @inertia


</body>

</html>
