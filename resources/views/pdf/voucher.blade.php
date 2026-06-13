<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago - ByMex</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .ticket-wrapper {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #0f172a;
            padding: 20px;
            box-sizing: border-box;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0f172a;
            margin: 0 0 5px 0;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .col-left {
            width: 50%;
            float: left;
        }
        .col-right {
            width: 50%;
            float: right;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .section-title {
            font-size: 14px;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .info-block {
            margin-bottom: 15px;
        }
        .info-block strong {
            display: inline-block;
            width: 130px;
            color: #555;
        }
        .total-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="ticket-wrapper">
        <div class="header">
            @php
                $logoPath = public_path('img/logobymex.jpeg');
                $logoBase64 = '';
                if(file_exists($logoPath)) {
                    $logoData = file_get_contents($logoPath);
                    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
                }
            @endphp
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Viajes By Mex Logo" style="max-height: 90px; margin-bottom: 10px; border-radius: 8px;">
            @else
                <h1>Viajes By Mex</h1>
            @endif
            <p>Agencia de Viajes y Excursiones Premium</p>
            <p style="margin-top:5px; font-size: 18px;"><strong>COMPROBANTE DE PAGO / ABONO</strong></p>
        </div>

        <div class="total-box">
            <div style="font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Monto del Movimiento</div>
            <div style="font-size: 36px; color: #0f172a; font-weight: bold; margin-bottom: 10px;">${{ number_format($payment->amount, 2) }} MXN</div>
            
            <div style="font-size: 14px; color: #475569;">
                <strong>Fecha:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}<br>
                <strong>Método:</strong> {{ $payment->payment_method === 'stripe' ? 'Pago en línea (Stripe)' : ($payment->payment_method === 'mercadopago' ? 'Pago en línea (Mercado Pago)' : 'Depósito / Transferencia') }}
                @if($payment->stripe_session_id)
                    <br><strong>Ref:</strong> {{ substr($payment->stripe_session_id, -12) }}
                @endif
                @if(!empty($payment->notes))
                    <br><strong>Concepto:</strong> <span style="font-style: italic;">📝 {{ $payment->notes }}</span>
                @endif
            </div>
        </div>

        <div class="grid">
            <div class="col-left">
                <div class="section-title">Datos del Viajero</div>
                <div class="info-block">
                    <strong>Titular:</strong> {{ $payment->reservation->client->name }}<br>
                    <strong>Reserva Folio:</strong> #{{ str_pad($payment->reservation->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <strong>Destino:</strong> <span style="font-weight: bold;">{{ $payment->reservation->tour->title }}</span><br>
                    <strong>Fecha de Salida:</strong> {{ \Carbon\Carbon::parse($payment->reservation->tour->departure_date)->format('d/m/Y') }}
                </div>
            </div>
            <div class="col-right">
                <div class="section-title">Resumen de la Reserva</div>
                <div class="info-block">
                    <strong>Total del Viaje:</strong> ${{ number_format($payment->reservation->total_amount, 2) }}<br>
                    <strong>Pagado/Abonado:</strong> ${{ number_format($payment->reservation->total_amount - $payment->reservation->balance_due, 2) }}<br>
                    <strong>Saldo Pendiente:</strong> <span style="color: #b91c1c; font-weight: bold;">${{ number_format($payment->reservation->balance_due, 2) }}</span>
                    
                    <div style="margin-top: 15px;">
                        @if($payment->reservation->balance_due <= 0)
                            <span class="badge" style="background: #dcfce7; color: #166534; border: 1px solid #22c55e; padding: 8px 15px; font-size: 14px;">VIAJE PAGADO TOTALMENTE</span>
                        @elseif($payment->reservation->total_amount > $payment->reservation->balance_due)
                            <span class="badge" style="background: #fef08a; color: #854d0e; border: 1px solid #fde047; padding: 8px 15px; font-size: 14px;">PAGO PARCIAL REGISTRADO</span>
                        @else
                            <span class="badge" style="background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; padding: 8px 15px; font-size: 14px;">PENDIENTE DE PAGO</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="footer">
            <p><strong>GRACIAS POR TU PAGO.</strong> Este documento avala únicamente la recepción del monto arriba mencionado para aplicarlo al folio correspondiente.</p>
            @php
                $cleanWhatsapp = '';
                if (isset($paymentSettings) && $paymentSettings->whatsapp_number) {
                    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $paymentSettings->whatsapp_number);
                    if (strlen($cleanWhatsapp) === 10) {
                        $cleanWhatsapp = '52' . $cleanWhatsapp;
                    }
                }
            @endphp
            @if(!empty($cleanWhatsapp))
                <p>Para dudas, comunícate a nuestro WhatsApp: <strong>{{ $cleanWhatsapp }}</strong></p>
            @endif
            <p>Viajes By Mex - Hecho en México</p>
        </div>
    </div>
</body>
</html>
