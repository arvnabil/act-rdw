<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .field {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }

        .value {
            background: #f1f1f1;
            padding: 8px;
            border-radius: 4px;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>New Form Submission</h2>
            <p><strong>Source Page:</strong> {{ $page ?? 'N/A' }}</p>
        </div>

        <div class="content">
            @foreach ($data as $key => $value)
                <div class="field">
                    <span class="label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                    <div class="value">{!! nl2br(e($value)) !!}</div>
                </div>
            @endforeach
        </div>

        <div class="footer">
            <p>Submitted on: {{ now()->toDayDateTimeString() }}</p>
            <p>IP Address: {{ request()->ip() }}</p>
        </div>
    </div>
</body>

</html>
