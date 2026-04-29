<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Reserva - ByMex</title>
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
            border: 2px dashed #d62828;
            padding: 20px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #d62828;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #d62828;
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
            color: #d62828;
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
            width: 120px;
            color: #555;
        }
        .seats-container {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .seats-list {
            font-size: 24px;
            font-weight: bold;
            color: #d62828;
            letter-spacing: 3px;
        }
        .total-box {
            background: #d62828;
            color: #fff;
            padding: 15px;
            text-align: right;
            font-size: 20px;
            font-weight: bold;
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
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
            border-radius: 3px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        table.passengers-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.passengers-table th, table.passengers-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }
        table.passengers-table th {
            background-color: #f8f9fa;
            color: #555;
            text-transform: uppercase;
        }
        .note-alert {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="ticket-wrapper">
        <div class="header">
            <h1>Viajes By Mex</h1>
            <p>Agencia de Viajes y Excursiones Premium</p>
            <p style="margin-top:5px;">Comprobante de Reserva: <strong>#{{ str_pad($reservation->id, 5, '0', STR_PAD_LEFT) }}</strong></p>
        </div>

        <div class="grid">
            <div class="col-left">
                <div class="section-title">Datos del Viajero</div>
                <div class="info-block">
                    <strong>Nombre:</strong> {{ $reservation->client->name }}<br>
                    <strong>Teléfono:</strong> {{ $reservation->client->phone }}<br>
                    <strong>Email:</strong> {{ $reservation->client->email }}
                </div>
            </div>
            <div class="col-right">
                <div class="section-title">Estado de la Reserva</div>
                <div class="info-block">
                    <span class="badge">
                        @if($reservation->status == 'pending')
                            PENDIENTE DE PAGO
                        @elseif($reservation->status == 'paid')
                            PAGADO
                        @else
                            CANCELADO
                        @endif
                    </span>
                    <br><br>
                    <small style="color:#666;">
                        <strong>Fecha de solicitud:</strong><br>
                        {{ $reservation->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="grid">
            <div class="col-left" style="width: 100%;">
                <div class="section-title">Detalles del Viaje</div>
                <div class="info-block">
                    <strong>Destino:</strong> <span style="font-size: 16px; font-weight: bold;">{{ $reservation->tour->title }}</span><br>
                    <strong>Salida:</strong> {{ \Carbon\Carbon::parse($reservation->tour->departure_date)->format('d/m/Y H:i') }} hrs<br>
                    <strong>Vencimiento:</strong> Tienes hasta el {{ \Carbon\Carbon::parse($reservation->expires_at)->format('d/m/Y H:i') }} para realizar tu anticipo.
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="section-title">Lista de Pasajeros</div>
        <table class="passengers-table">
            <thead>
                <tr>
                    <th>Asiento</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th style="text-align: right;">Tarifa Final</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservation->passengers as $p)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ str_pad($p->seat_number, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $p->name }}</td>
                    <td>
                        {{ $p->passenger_type }}
                        @if($p->benefit_label)
                            <br><small style="color: #666;">({{ $p->benefit_label }})</small>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($p->discount_amount > 0)
                            <del style="color: #999; font-size: 11px;">${{ number_format($p->base_price, 2) }}</del><br>
                        @endif
                        ${{ number_format($p->final_price, 2) }}
                    </td>
                </tr>
                @empty
                <!-- Fallback para reservaciones muy antiguas sin pasajeros en BD -->
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic; color: #888;">
                        Asientos: 
                        @foreach($reservation->seats as $seat)
                            {{ str_pad($seat->seat_number, 2, '0', STR_PAD_LEFT) }}{{ !$loop->last ? ' - ' : '' }}
                        @endforeach
                        (Pasajeros no detallados)
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($reservation->passengers && $reservation->passengers->where('discount_amount', '>', 0)->count() > 0)
        <div class="note-alert">
            <strong>AVISO IMPORTANTE:</strong> Las tarifas con descuento o beneficios especiales (Niños, INAPAM, Estudiantes, etc.) quedan estrictamente sujetas a validación documental antes del inicio del tour. Por favor, presente su identificación oficial al momento de abordar.
        </div>
        @endif

        <div style="width: 100%; text-align: right; margin-bottom: 20px;">
            <table style="width: 300px; float: right; font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px; color: #555;">Subtotal:</td>
                    <td style="padding: 5px; text-align: right;">${{ number_format($reservation->subtotal ?? $reservation->total_amount, 2) }}</td>
                </tr>
                @if($reservation->discount_total > 0)
                <tr>
                    <td style="padding: 5px; color: #d62828;">Descuentos Aplicados:</td>
                    <td style="padding: 5px; text-align: right; color: #d62828;">-${{ number_format($reservation->discount_total, 2) }}</td>
                </tr>
                @endif
                <tr style="font-weight: bold; font-size: 18px; background: #d62828; color: #fff;">
                    <td style="padding: 10px; border-radius: 5px 0 0 5px;">TOTAL FINAL:</td>
                    <td style="padding: 10px; text-align: right; border-radius: 0 5px 5px 0;">${{ number_format($reservation->total_amount, 2) }} MXN</td>
                </tr>
                <tr>
                    <td style="padding: 5px; color: #555; font-size: 13px;">Anticipo / Pagado:</td>
                    <td style="padding: 5px; text-align: right; font-size: 13px;">${{ number_format($reservation->total_amount - $reservation->balance_due, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px; color: #555; font-size: 13px; font-weight: bold;">Saldo Pendiente:</td>
                    <td style="padding: 5px; text-align: right; font-size: 13px; font-weight: bold;">${{ number_format($reservation->balance_due, 2) }}</td>
                </tr>
            </table>
            <div class="clear"></div>
        </div>

        <div class="footer">
            <p><strong>IMPORTANTE:</strong> Este documento es un comprobante de apartado. Para asegurar tus lugares, es indispensable realizar el pago del anticipo antes de la fecha límite indicada.</p>
            <p>Para dudas o envío de comprobantes de pago, comunícate a nuestro WhatsApp: <strong>744 129 5026</strong></p>
            <p>Viajes By Mex - Hecho en México</p>
        </div>
    </div>
</body>
</html>
