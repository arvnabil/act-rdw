<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Event Ticket</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
            /* Using portrait to stack 2 tickets or landscape for single */
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #333;
        }

        /* Container for each ticket (half page or full page) */
        .ticket-container {
            width: 100%;
            height: 50vh;
            /* Half page for each ticket */
            position: relative;
            overflow: hidden;
            background: #0f172a;
            /* Dark Blue Slate */
            display: table;
            page-break-inside: avoid;
        }

        .ticket-content {
            display: table-cell;
            vertical-align: middle;
            padding: 40px;
            position: relative;
            z-index: 10;
        }

        /* Design Elements - Fun Tech Premium */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 1;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            background: rgba(59, 130, 246, 0.3);
            /* Blue */
            top: -100px;
            right: -50px;
        }

        .orb-2 {
            width: 200px;
            height: 200px;
            background: rgba(139, 92, 246, 0.3);
            /* Purple */
            bottom: -50px;
            left: -50px;
        }

        .grid-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: 0;
        }

        /* Ticket Layout */
        .ticket-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            width: 90%;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .header {
            padding: 30px 30px 0 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .official-tag {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            color: #fff;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: inline-block;
            margin-bottom: 15px;
        }

        .event-branding {
            font-size: 12px;
            letter-spacing: 4px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .body {
            padding: 30px;
            display: table;
            width: 100%;
        }

        .info-col {
            display: table-cell;
            width: 65%;
            vertical-align: top;
            padding-right: 20px;
        }

        .qr-col {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            text-align: right;
            border-left: 2px dashed rgba(255, 255, 255, 0.2);
            padding-left: 20px;
        }

        h1 {
            color: #fff;
            font-size: 28px;
            margin: 0 0 5px 0;
            line-height: 1.2;
        }

        .subtitle {
            color: #93c5fd;
            /* Soft Blue */
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 25px;
            font-style: italic;
        }

        .meta-row {
            margin-bottom: 15px;
        }

        .meta-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .meta-value {
            color: #fff;
            font-size: 14px;
            font-weight: bold;
        }

        .footer {
            background: rgba(0, 0, 0, 0.3);
            padding: 15px 30px;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
        }

        .qr-box {
            background: #fff;
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
        }

        .ticket-id {
            color: #fff;
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 2px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <!-- ENGLISH VERSION -->
    <div class="ticket-container">
        <div class="grid-bg"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        <div class="ticket-content">
            <div class="ticket-card">
                <div class="header">
                    <div class="official-tag">Official Ticket</div>
                    <div class="event-branding">ACT-RDW EVENTS</div>
                </div>

                <div class="body">
                    <div class="info-col">
                        <h1>{{ $event['title'] }}</h1>
                        <div class="subtitle">with ACTiV Teknologi</div>

                        <div class="meta-row">
                            <div class="meta-label">Date & Time</div>
                            <div class="meta-value">{{ $event['date_formatted'] }}</div>
                        </div>

                        <div class="meta-row" style="display: table; width: 100%;">
                            <div style="display: table-cell; width: 50%;">
                                <div class="meta-label">Attendee</div>
                                <div class="meta-value">{{ $registration['name'] }}</div>
                            </div>
                            <div style="display: table-cell; width: 50%;">
                                <div class="meta-label">Location</div>
                                <div class="meta-value">{{ $event['location'] }}</div>
                            </div>
                        </div>

                        <div class="meta-row">
                            <div class="meta-label">Ticket Type</div>
                            <div class="meta-value" style="color: #fbbf24;">{{ $event['ticket_type'] }}</div>
                        </div>
                    </div>

                    <div class="qr-col">
                        <div class="qr-box">
                            <img src="data:image/svg+xml;base64, {{ $qr_code }}" alt="QR Code" width="120">
                        </div>
                        <div class="ticket-id">{{ $registration['ticket_code'] }}</div>
                        <div style="font-size: 9px; color: rgba(255,255,255,0.5); margin-top: 5px;">Scan to Verify</div>
                    </div>
                </div>

                <div class="footer">
                    Present this ticket at the entrance. The QR code is unique to your registration.
                    <br><strong>Organizer: {{ $event['organizer'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- INDONESIAN VERSION -->
    <div class="ticket-container" style="page-break-before: always;">
        <div class="grid-bg"></div>
        <div class="orb orb-1" style="left: -50px; right: auto; background: rgba(16, 185, 129, 0.3);"></div>
        <div class="orb orb-2" style="right: -50px; left: auto; background: rgba(245, 158, 11, 0.3);"></div>

        <div class="ticket-content">
            <div class="ticket-card">
                <div class="header">
                    <div class="official-tag" style="background: linear-gradient(90deg, #10b981, #3b82f6);">Tiket Resmi
                    </div>
                    <div class="event-branding">ACT-RDW EVENTS</div>
                </div>

                <div class="body">
                    <div class="info-col">
                        <h1>{{ $event['title'] }}</h1>
                        <div class="subtitle">bersama ACTiV Teknologi</div>

                        <div class="meta-row">
                            <div class="meta-label">Tanggal & Waktu</div>
                            <div class="meta-value">{{ $event['date_formatted'] }}</div>
                        </div>

                        <div class="meta-row" style="display: table; width: 100%;">
                            <div style="display: table-cell; width: 50%;">
                                <div class="meta-label">Peserta</div>
                                <div class="meta-value">{{ $registration['name'] }}</div>
                            </div>
                            <div style="display: table-cell; width: 50%;">
                                <div class="meta-label">Lokasi</div>
                                <div class="meta-value">
                                    {{ $event['location'] == 'Online' ? 'Daring (Online)' : $event['location'] }}</div>
                            </div>
                        </div>

                        <div class="meta-row">
                            <div class="meta-label">Jenis Tiket</div>
                            <div class="meta-value" style="color: #fbbf24;">
                                {{ $event['ticket_type'] == 'Regular' ? 'Reguler' : $event['ticket_type'] }}</div>
                        </div>
                    </div>

                    <div class="qr-col">
                        <div class="qr-box">
                            <img src="data:image/svg+xml;base64, {{ $qr_code }}" alt="QR Code" width="120">
                        </div>
                        <div class="ticket-id">{{ $registration['ticket_code'] }}</div>
                        <div style="font-size: 9px; color: rgba(255,255,255,0.5); margin-top: 5px;">Pindai untuk
                            Verifikasi</div>
                    </div>
                </div>

                <div class="footer">
                    Tunjukkan tiket ini kepada panitia acara. Kode QR ini unik untuk pendaftaran Anda.
                    <br><strong>Penyelenggara: {{ $event['organizer'] }}</strong>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
