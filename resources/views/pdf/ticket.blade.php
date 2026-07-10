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
            font-size: 13px;
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
            padding: 6px 4px;
            text-align: left;
            font-size: 13px;
        }
        table.passengers-table th {
            background-color: #f8f9fa;
            color: #555;
            text-transform: uppercase;
            font-size: 11px;
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
                    <div class="financial-status">
                        @if($reservation->balance_due <= 0)
                            <span class="badge" style="background: #dcfce7; color: #166534; border: 1px solid #22c55e; padding: 8px 15px; font-size: 16px;">PAGADO</span>
                        @elseif($reservation->total_amount > $reservation->balance_due)
                            <span class="badge" style="background: #fef08a; color: #854d0e; border: 1px solid #fde047; padding: 8px 15px; font-size: 16px;">PAGO PARCIAL</span>
                        @else
                            <span class="badge" style="background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; padding: 8px 15px; font-size: 16px;">PENDIENTE DE PAGO</span>
                        @endif
                    </div>
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
                <div class="info-block" style="font-size: 13px; line-height: 2;">
                    <div style="padding: 4px 0;">
                        <strong style="font-size: 12px; font-weight: bold;">Destino:</strong> 
                        <span style="font-size: 16px; font-weight: bold; color: #333;">{{ $reservation->tour->title }}</span>
                    </div>
                    
                    @php
                        $boardingPointsOrdenados = $reservation->tour->boardingPoints->sortBy('pivot.sort_order');
                        
                        $maxSortOrder = $reservation->passengers
                            ->map(fn($p) => 
                                $reservation->tour->boardingPoints
                                    ->firstWhere('id', $p->boarding_point_id)
                                    ?->pivot->sort_order ?? 0
                            )->max();

                        $puntosHastaCliente = $boardingPointsOrdenados
                            ->filter(fn($bp) => 
                                $bp->pivot->sort_order <= $maxSortOrder
                            );
                    @endphp

                    @if($puntosHastaCliente->isNotEmpty())
                        <div style="padding: 4px 0;">
                            <strong style="font-size: 12px; font-weight: bold;">Fecha:</strong> 
                            {{ \Carbon\Carbon::parse($reservation->tour->departure_date)->locale('es')->isoFormat('D [de] MMMM YYYY') }}
                        </div>
                        <div style="padding: 4px 0;">
                            <strong style="font-size: 12px; font-weight: bold;">Salida:</strong> 
                            @foreach($puntosHastaCliente as $bp)
                                {{ $bp->name }}
                                @if($bp->pivot->departure_time)
                                    {{ \Carbon\Carbon::parse($bp->pivot->departure_time)->format('H:i') }} hrs
                                @endif
                                @if(!$loop->last) &gt; @endif
                            @endforeach
                        </div>
                    @else
                        <div style="padding: 4px 0;">
                            <strong style="font-size: 12px; font-weight: bold;">Salida:</strong> 
                            {{ \Carbon\Carbon::parse($reservation->tour->departure_date)->locale('es')->isoFormat('D [de] MMMM YYYY - H:i') }} hrs
                        </div>
                    @endif
                    
                    <div style="padding: 4px 0;">
                        <strong style="font-size: 12px; font-weight: bold;">Vencimiento:</strong> 
                        Tienes hasta el {{ \Carbon\Carbon::parse($reservation->expires_at)->format('d/m/Y H:i') }} para realizar tu anticipo.
                    </div>
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
                    <th>Abordaje</th>
                    <th style="text-align: right;">Tarifa Final</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservation->passengers->where('status', '!=', 'cancelled') as $p)
                @php
                    $bpHorario = $reservation->tour->boardingPoints
                        ->firstWhere('id', $p->boarding_point_id);
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ str_pad($p->seat_number, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $p->name }}</td>
                    <td>
                        {{ $p->passenger_type }}
                        @if($p->benefit_label)
                            <br><small style="color: #666;">({{ $p->benefit_label }})</small>
                        @endif
                    </td>
                    <td>
                        @if($p->boardingPoint)
                            <strong>{{ $p->boardingPoint->name }}</strong>
                            @if($p->boardingSubPoint)
                                <br><small>↳ {{ $p->boardingSubPoint->name }}</small>
                            @endif
                        @else
                            —
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
                    <td colspan="5" style="text-align: center; font-style: italic; color: #888;">
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
            <table style="width: 300px; float: right; font-size: 15px; border-collapse: collapse;">
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
                    <td style="padding: 5px; color: #555; font-size: 14px;">Anticipo / Pagado:</td>
                    <td style="padding: 5px; text-align: right; font-size: 14px;">${{ number_format($reservation->total_amount - $reservation->balance_due, 2) }}</td>
                </tr>
                @php
                    $pagosConNota = $reservation->payments
                        ->where('status', 'approved')
                        ->whereNotNull('notes')
                        ->where('notes', '!=', '');
                @endphp
                @if($pagosConNota->isNotEmpty())
                    @foreach($pagosConNota as $pago)
                    <tr>
                        <td colspan="2" style="padding: 3px 5px; font-size: 11px; color: #666; font-style: italic; text-align: right;">
                            * {{ \Carbon\Carbon::parse($pago->uploaded_at ?? $pago->created_at)->format('d/m/Y') }} — {{ $pago->notes }}
                        </td>
                    </tr>
                    @endforeach
                @endif
                <tr>
                    <td style="padding: 5px; color: #555; font-size: 14px; font-weight: bold;">Saldo Pendiente:</td>
                    <td style="padding: 5px; text-align: right; font-size: 14px; font-weight: bold;">${{ number_format($reservation->balance_due, 2) }}</td>
                </tr>
            </table>
        <div class="clear"></div>

        @if(isset($paymentSettings))
            <div style="page-break-inside: avoid; margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px;">
                <div class="section-title">Datos para Pago / Depósito</div>
                <div class="info-block" style="font-size: 13px;">
                    @if($paymentSettings->general_instructions)
                        <p style="margin-bottom: 15px; color: #555;">{{ $paymentSettings->general_instructions }}</p>
                    @endif
                    
                    @if(isset($activeBanks) && $activeBanks->count() > 0)
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 13px;">
                            @foreach($activeBanks as $bank)
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; width: 50%; vertical-align: top; background: #fafafa;">
                                    <strong style="color:#d62828; font-size: 14px;">{{ $bank->bank_name }}</strong><br>
                                    <strong style="width: 60px;">Titular:</strong> {{ $bank->account_holder }}<br>
                                    @if($bank->label)<span style="color: #666; font-style: italic; font-size: 11px;">{{ $bank->label }}</span>@endif
                                </td>
                                <td style="padding: 10px; border: 1px solid #ddd; width: 50%; vertical-align: top;">
                                    @if($bank->account_number)<strong style="width: 60px;">Cuenta:</strong> {{ $bank->account_number }}<br>@endif
                                    @if($bank->clabe)<strong style="width: 60px;">CLABE:</strong> {{ $bank->clabe }}<br>@endif
                                    @if($bank->card_number)<strong style="width: 60px;">Tarjeta:</strong> {{ $bank->card_number }}<br>@endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    @endif

                    @if($paymentSettings->final_note)
                        <div class="note-alert" style="margin-top: 10px; margin-bottom: 0;">
                            {{ $paymentSettings->final_note }}
                        </div>
                    @endif
                </div>
            </div>

            @if($paymentSettings->reservation_policies || $paymentSettings->cancellation_policies)
                <div style="page-break-inside: avoid; margin-top: 20px;">
                    <div class="section-title">Políticas y Condiciones Generales</div>
                    <div style="font-size: 12px; color: #444; line-height: 1.5; text-align: left; background: #fafafa; padding: 15px; border: 1px solid #eee; border-radius: 5px;">
                        @if($paymentSettings->reservation_policies)
                            <strong style="color: #333; font-size: 13px; display: block; margin-bottom: 5px;">Condiciones de Reservación y Pago:</strong>
                            <div style="margin-bottom: 15px;">{!! nl2br(e($paymentSettings->reservation_policies)) !!}</div>
                        @endif
                        @if($paymentSettings->cancellation_policies)
                            <strong style="color: #d62828; font-size: 13px; display: block; margin-bottom: 5px;">Políticas de Cancelación:</strong>
                            <div>{!! nl2br(e($paymentSettings->cancellation_policies)) !!}</div>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        <div class="footer">
            <p><strong>IMPORTANTE:</strong> Este documento es un comprobante de apartado. Para asegurar tus lugares, es indispensable realizar el pago del anticipo antes de la fecha límite indicada.</p>
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
                <p>Para dudas o envío de comprobantes de pago, comunícate a nuestro WhatsApp: <strong>{{ $cleanWhatsapp }}</strong></p>
            @endif
            <p>Viajes By Mex - Hecho en México</p>
        </div>
    </div>
</body>
</html>
