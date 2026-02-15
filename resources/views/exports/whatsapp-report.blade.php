<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>WhatsApp Analytics Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #00a884; padding-bottom: 20px; }
        .logo { font-size: 28px; font-weight: bold; color: #00a884; margin-bottom: 5px; }
        .subtitle { font-size: 14px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        
        .summary-grid { display: block; margin-bottom: 40px; }
        .summary-card { 
            display: inline-block; 
            width: 21%; 
            background: #f9f9f9; 
            padding: 15px; 
            border-radius: 10px; 
            margin-right: 2%; 
            text-align: center;
            border: 1px solid #eee;
        }
        .summary-card .label { font-size: 10px; color: #888; text-transform: uppercase; margin-bottom: 5px; }
        .summary-card .value { font-size: 18px; font-weight: bold; color: #333; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }
        th { background: #00a884; color: #fff; text-align: left; padding: 10px; text-transform: uppercase; }
        td { border-bottom: 1px solid #eee; padding: 10px; }
        tr:nth-child(even) { background: #fafafa; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; padding: 10px 0; border-top: 1px solid #eee; }
        
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-success { background: #e6f7ed; color: #15803d; }
        .badge-info { background: #eff6ff; color: #1d4ed8; }
        .badge-warning { background: #fffbeb; color: #b45309; }
        .badge-danger { background: #fef2f2; color: #b91c1c; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ACTiV <span style="font-weight: normal; color: #333;">Analytics</span></div>
        <div class="subtitle">WhatsApp Interaction Report</div>
        <div style="font-size: 11px; font-weight: bold; margin-top: 5px; color: #555;">
            Period: {{ $dateRange['from'] ? $dateRange['from']->format('d M Y') : 'N/A' }} 
            - {{ $dateRange['to'] ? $dateRange['to']->format('d M Y') : 'N/A' }}
        </div>
        <div style="font-size: 10px; color: #999; margin-top: 5px;">
            Report Extracted on {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Logs</div>
            <div class="value">{{ count($records) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Clicks</div>
            <div class="value">{{ $records->sum('click_count') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Conversions</div>
            <div class="value">{{ $records->where('is_converted', true)->count() }}</div>
        </div>
        <div class="summary-card" style="margin-right: 0;">
            <div class="label">Human %</div>
            <div class="value">
                {{ count($records) > 0 ? round(($records->where('is_bot', false)->count() / count($records)) * 100) : 0 }}%
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Time</th>
                <th width="20%">Label / Campaign</th>
                <th width="10%">Clicks</th>
                <th width="15%">Source (UTM)</th>
                <th width="15%">Device</th>
                <th width="25%">Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->created_at->format('d/m/y H:i') }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $record->event_label ?? 'N/A' }}</div>
                        <div style="font-size: 9px; color: #888;">{{ $record->utm_campaign ?? '-' }}</div>
                    </td>
                    <td align="center">{{ $record->click_count }}</td>
                    <td>
                         <span class="badge {{ $record->is_bot ? 'badge-danger' : 'badge-success' }}">
                            {{ $record->utm_source ?? 'Direct' }}
                        </span>
                    </td>
                    <td>
                         <span class="badge {{ $record->device === 'mobile' ? 'badge-warning' : 'badge-info' }}">
                            {{ ucfirst($record->device ?? 'unknown') }}
                        </span>
                    </td>
                    <td>
                        {{ $record->city ?? 'Unknown' }}, {{ $record->country ?? 'Unknown' }}
                        @if($record->ip_address)
                            <div style="font-size: 8px; color: #aaa;">{{ $record->ip_address }}</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} PT Alfa Cipta Teknologi Virtual. All rights reserved. | WhatsApp Tracking System Enterprise
    </div>
</body>
</html>
