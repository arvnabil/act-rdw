@php
    // Font Import Strategy for DomMpdf
    // We use @import inside style tag which is more robust for dompdf than link tags
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Certificate</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alex+Brush&family=Bebas+Neue&family=Cinzel:wght@400;700&family=Cormorant+Garamond:ital,wght@0,400;0,700;1,400&family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;700&family=Open+Sans:wght@300;400;700&family=Pinyon+Script&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap');

        @page {
            margin: 0;
            padding: 0;
            size: A4 landscape;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Montserrat', sans-serif;
            /* Default Fallback */
            -webkit-font-smoothing: antialiased;
        }

        .container {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        .element {
            position: absolute;
            line-height: 1.2;
            letter-spacing: normal;
        }
    </style>
</head>

<body style="width: 842pt; height: 595pt;"> <!-- Force A4 Landscape Dimensions in PT -->

    <div class="container"
        style="
        width: 842pt;
        height: 595pt;
        background-image: url('{{ $background ? public_path('storage/' . $background) : '' }}');
        background-color: #fff;
    ">
        @foreach ($layout as $element)
            @php
                // Process Styles
                $fontFamily = $element['fontFamily'] ?? 'Montserrat';
                // Remove generic fallback for dompdf mapping if necessary, or keep it.
                // Google Fonts usually work if installed/loaded correctly.

                $fontSize = $element['fontSize'] ?? 12;
                $color = $element['color'] ?? '#000000';
                $fontWeight = $element['fontWeight'] ?? 'normal';
                $fontStyle = $element['fontStyle'] ?? 'normal';
                $textDecoration = $element['textDecoration'] ?? 'none';
                $textAlign = $element['textAlign'] ?? 'left';

                // Calculate CSS properties
                // Convert % to pt for PDF static rendering (approximate: A4 Width 842pt, Height 595pt)
                // We use standard pt units for PDF to ensure wysiwyg
                $pageWidth = 842;
                $pageHeight = 595;

                $left = ($element['x'] / 100) * $pageWidth;
                $top = ($element['y'] / 100) * $pageHeight;

                // Handle Placeholders
                $text = $element['text'] ?? '';
                if ($element['type'] === 'variable') {
                    // Replace variables
                    $map = [
                        '{{ participant_name }}' => $data['participant_name'] ?? 'Participant Name',
                        '{{ event_title }}' => $data['event_title'] ?? 'Event Title',
                        '{{ date }}' => $data['date'] ?? 'Date',
                        '{{ certificate_code }}' => $data['certificate_code'] ?? 'CODE',
                    ];
                    $text = str_replace(array_keys($map), array_values($map), $text);
                }
            @endphp

            @if (in_array($element['type'], ['text', 'variable']))
                <div class="element"
                    style="
                    left: {{ $left }}pt;
                    top: {{ $top }}pt;
                    font-family: {{ $fontFamily }};
                    font-size: {{ $fontSize }}pt;
                    color: {{ $color }};
                    font-weight: {{ $fontWeight }};
                    font-style: {{ $fontStyle }};
                    text-decoration: {{ $textDecoration }};
                    text-align: {{ $textAlign }};
                    {{ isset($element['width']) ? 'width: ' . $element['width'] . 'pt; white-space: normal;' : 'white-space: nowrap;' }}
                ">
                    {{ $text }}
                </div>
            @elseif($element['type'] === 'qrcode')
                <div class="element"
                    style="
                    left: {{ $left }}pt;
                    top: {{ $top }}pt;
                    width: {{ $element['width'] ?? 100 }}pt;
                    height: {{ $element['width'] ?? 100 }}pt;
                    background-color: #ffffff;
                    padding: 5pt;
                    border-radius: 8pt;
                ">
                    <img src="{{ $data['qr_code'] }}" style="width: 100%; height: 100%;" />
                </div>
            @elseif($element['type'] === 'image' && !empty($element['src']))
                <div class="element"
                    style="
                    left: {{ $left }}pt;
                    top: {{ $top }}pt;
                    width: {{ $element['width'] ?? 100 }}pt;
                    height: {{ $element['height'] ?? 100 }}pt;
                ">
                    <img src="{{ public_path($element['src']) }}"
                        style="width: 100%; height: 100%; object-fit: contain;" />
                </div>
            @endif
        @endforeach
    </div>
</body>

</html>
