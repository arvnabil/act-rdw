<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Arial', sans-serif;
        }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        .element {
            position: absolute;
            transform: translate(0, 0);
            /* Element x/y are top-left based */
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="certificate-container" style="background-image: url('{{ public_path('storage/' . $background) }}');">
        @foreach ($layout as $element)
            @php
                $text = $element['text'];
                // Simple variable replacement
                if ($element['type'] === 'variable') {
                    $key = str_replace(['{{ ', ' }}', '{{ ', ' }}'], '', $text);
                    $key = trim($key);
                    $text = $data[$key] ?? $text;
                }
            @endphp
            <div class="element"
                style="
                left: {{ $element['x'] }}%;
                top: {{ $element['y'] }}%;
                font-size: {{ $element['fontSize'] }}px;
                color: {{ $element['color'] }};
                font-weight: {{ $element['fontWeight'] }};
                font-family: {{ $element['fontFamily'] }};
            ">
                {{ $text }}
            </div>
        @endforeach
    </div>
</body>

</html>
