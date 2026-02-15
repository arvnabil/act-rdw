<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Click Events Analytics Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #3b82f6; padding-bottom: 20px; }
        .logo { font-size: 28px; font-weight: bold; color: #3b82f6; margin-bottom: 5px; }
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
        th { background: #3b82f6; color: #fff; text-align: left; padding: 10px; text-transform: uppercase; }
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
        <div class="subtitle">Global Click Events Report</div>
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
                <th width="12%">Time</th>
                <th width="10%">Type</th>
                <th width="20%">Label</th>
                <th width="8%">Clicks</th>
                <th width="12%">Source</th>
                <th width="10%">Device</th>
                <th width="28%">Location / Info</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->created_at->format('d/m/y H:i') }}</td>
                    <td>
                        <span class="badge {{ match($record->event_type) {
                            'whatsapp' => 'badge-success',
                            'call' => 'badge-info',
                            'share' => 'badge-warning',
                            'form_submit' => 'badge-danger',
                            default => 'badge-info'
                        } }}">
                            {{ strtoupper($record->event_type) }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: bold;">{{ $record->event_label ?? 'N/A' }}</div>
                        <div style="font-size: 8px; color: #888;">{{ $record->cta_position }}</div>
                    </td>
                    <td align="center">{{ $record->click_count }}</td>
                    <td>
                        <div style="font-size: 10px;">{{ $record->utm_source ?? 'Direct' }}</div>
                        <div style="font-size: 8px; color: #999;">{{ $record->utm_medium }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $record->device === 'mobile' ? 'badge-warning' : 'badge-info' }}">
                            {{ ucfirst($record->device ?? 'unknown') }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: bold;">{{ $record->city ?? 'Unknown' }}, {{ $record->country ?? 'Unknown' }}</div>
                        <div style="font-size: 8px; color: #aaa;">{{ $record->ip_address }}</div>
                        @if($record->deal_value)
                            <div style="font-size: 9px; color: #15803d; font-weight: bold;">Value: IDR {{ number_format($record->deal_value, 0, ',', '.') }}</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} PT Alfa Cipta Teknologi Virtual. All rights reserved. | Global Analytics Tracking System
    </div>
</body>
</html>
