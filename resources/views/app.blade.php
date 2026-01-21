<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>PT Alfa Cipta Teknologi Virtual - ACTiV</title>
    <meta name="author" content="ACTIV" />
    <meta name="description" content="ACTIV - IT Solution & Comprehensive Technology Partner" />
    <meta name="keywords" content="ACTIV, IT Solution, Technology, Software Development, System Integrator" />
    <meta name="robots" content="INDEX,FOLLOW" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="/assets/img/favicons/favicon.ico" type="image/x-icon" />
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicons/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicons/favicon-16x16.png" />
    <link rel="manifest" href="/assets/img/favicons/manifest.json" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet" />

    <!-- jQuery (Required for Magnific Popup & Plugins) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- WOW.js (Fix for Vite Production Build) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>

    <!-- Magnific Popup JS (Fix for Vite Production Build) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

    <!-- Magnific Popup CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" />

    @if (config('services.recaptcha.key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.key') }}"></script>
    @endif

    <!-- Start of Qontak Webchat Script -->
    <script>
        const qchatInit = document.createElement('script');
        qchatInit.src = "https://webchat.qontak.com/qchatInitialize.js";
        const qchatWidget = document.createElement('script');
        qchatWidget.src = "https://webchat.qontak.com/js/app.js";
        document.head.prepend(qchatInit);
        document.head.prepend(qchatWidget);
        qchatInit.onload = function() {
            qchatInitialize({
                id: '0f0c18dc-53e5-4efa-bb2f-d0fca4f40cc8',
                code: 'bzuTnIhEXwWufy2oRg6YBw'
            })
        };
    </script>
    <!-- End of Qontak Webchat Script -->

    @routes
    @routes
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>

<body>
    @inertia


</body>

</html>
