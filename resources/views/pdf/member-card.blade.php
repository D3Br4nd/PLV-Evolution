<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }
        
        .card-wrapper {
            position: relative;
            width: 85.6mm;
            height: 54mm;
            margin-bottom: 20mm;
            margin-left: 8mm;
            margin-top: 8mm;
        }
        
        .card {
            width: 85.6mm;
            height: 54mm;
            overflow: hidden;
            position: relative;
            background: #ffffff;
        }
        
        /* Crop marks - 5mm lines, positioned 1mm outside card edges */
        /* Top-left corner */
        .crop-tl-h {
            position: absolute;
            top: -1mm;
            left: -6mm;
            width: 5mm;
            height: 0.2mm;
            background: #000;
        }
        .crop-tl-v {
            position: absolute;
            top: -6mm;
            left: -1mm;
            width: 0.2mm;
            height: 5mm;
            background: #000;
        }
        /* Top-right corner */
        .crop-tr-h {
            position: absolute;
            top: -1mm;
            left: 86.6mm;
            width: 5mm;
            height: 0.2mm;
            background: #000;
        }
        .crop-tr-v {
            position: absolute;
            top: -6mm;
            left: 86.6mm;
            width: 0.2mm;
            height: 5mm;
            background: #000;
        }
        /* Bottom-left corner */
        .crop-bl-h {
            position: absolute;
            top: 54.7mm;
            left: -6mm;
            width: 5mm;
            height: 0.2mm;
            background: #000;
        }
        .crop-bl-v {
            position: absolute;
            top: 55mm;
            left: -1mm;
            width: 0.2mm;
            height: 5mm;
            background: #000;
        }
        /* Bottom-right corner */
        .crop-br-h {
            position: absolute;
            top: 54.7mm;
            left: 86.6mm;
            width: 5mm;
            height: 0.2mm;
            background: #000;
        }
        .crop-br-v {
            position: absolute;
            top: 55mm;
            left: 86.6mm;
            width: 0.2mm;
            height: 5mm;
            background: #000;
        }
        
        /* Front card layout */
        .card-front-content {
            width: 100%;
            height: 100%;
        }
        .card-front-content td {
            vertical-align: middle;
        }
        .data-column {
            width: 55%;
            padding: 5mm;
        }
        .qr-column {
            width: 45%;
            text-align: center;
            padding: 3mm;
        }
        .field {
            margin-bottom: 4mm;
        }
        .label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
        }
        .value {
            font-size: 11pt;
            font-weight: normal;
            color: #1a1a1a;
        }
        .uuid {
            font-size: 6pt;
            color: #999;
            word-break: break-all;
            margin-top: 6mm;
        }
        .qr-code img {
            width: 28mm;
            height: 28mm;
        }
        
        /* Back card */
        .card-back img {
            width: 100%;
            height: 100%;
        }
        
        .card-label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 3mm;
            margin-left: 8mm;
        }
    </style>
</head>
<body>
    <div class="card-label">FRONTE (Dati)</div>
    <div class="card-wrapper">
        <div class="crop-tl-h"></div>
        <div class="crop-tl-v"></div>
        <div class="crop-tr-h"></div>
        <div class="crop-tr-v"></div>
        <div class="crop-bl-h"></div>
        <div class="crop-bl-v"></div>
        <div class="crop-br-h"></div>
        <div class="crop-br-v"></div>
        
        <div class="card">
            <table class="card-front-content" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="data-column">
                        <div class="field">
                            <div class="label">Nome</div>
                            <div class="value">{{ $user->first_name ?: $user->name }}</div>
                        </div>
                        <div class="field">
                            <div class="label">Cognome</div>
                            <div class="value">{{ $user->last_name ?: '' }}</div>
                        </div>
                        <div class="uuid">
                            ID: {{ $user->id }}
                        </div>
                    </td>
                    <td class="qr-column">
                        <div class="qr-code">
                            @if($qrCodeBase64)
                                <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code">
                            @else
                                <div style="width:28mm;height:28mm;background:#eee;text-align:center;line-height:28mm;font-size:8pt;color:#999;">QR</div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card-label">RETRO (Grafica)</div>
    <div class="card-wrapper">
        <div class="crop-tl-h"></div>
        <div class="crop-tl-v"></div>
        <div class="crop-tr-h"></div>
        <div class="crop-tr-v"></div>
        <div class="crop-bl-h"></div>
        <div class="crop-bl-v"></div>
        <div class="crop-br-h"></div>
        <div class="crop-br-v"></div>
        
        <div class="card card-back">
            @if($cardBackBase64)
                <img src="data:image/jpeg;base64,{{ $cardBackBase64 }}" alt="Pro Loco Venticanese">
            @else
                <div style="padding: 10mm; text-align: center; color: #999;">
                    Immagine non trovata
                </div>
            @endif
        </div>
    </div>
</body>
</html>
