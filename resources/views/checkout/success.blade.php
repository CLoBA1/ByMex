@extends('layouts.public')

@section('title', 'Reserva Confirmada')

@section('extra-css')
    <style>
        .success-container {
            max-width: 800px;
            margin: 4rem auto;
            padding: 0 2rem;
        }
        .success-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 3rem;
            box-shadow: var(--shadow-xl);
            text-align: center;
            border-top: 6px solid var(--color-tertiary); /* Verde de éxito */
        }
        .success-icon {
            font-size: 4rem;
            color: var(--color-tertiary);
            margin-bottom: 1rem;
        }
        .res-details {
            background: #f8fafc;
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .detail-item h4 {
            color: var(--color-dark-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.25rem;
        }
        .detail-item p {
            color: var(--color-dark);
            font-size: 1.1rem;
            font-weight: 600;
        }
        .warning-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            text-align: left;
        }
    </style>
@endsection

@section('content')
    <main class="success-container">
            @if(session('success'))
                <div style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if($reservation->status->value === 'paid')
                <i class="fa-solid fa-check-double success-icon" style="color: #10b981;"></i>
                <h1 style="color: var(--color-dark); font-size: 2.5rem;">¡Pago Confirmado!</h1>
                <p style="color: var(--color-dark-muted); font-size: 1.1rem;">Tu reserva está pagada. ¡Prepara tus maletas!</p>
            @else
                <i class="fa-solid fa-circle-check success-icon"></i>
                <h1 style="color: var(--color-dark); font-size: 2.5rem;">¡Reserva Generada!</h1>
                <p style="color: var(--color-dark-muted); font-size: 1.1rem;">Tu lugar está apartado. Por favor completa tu pago para asegurar los asientos.</p>
            @endif
            
            <div class="res-details">
                <div class="detail-item">
                    <h4>Folio de Reserva</h4>
                    <p style="color: var(--color-primary);">#RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="detail-item">
                    <h4>Pasajero Titular</h4>
                    <p>{{ $reservation->client->name }}</p>
                </div>
                <div class="detail-item">
                    <h4>Tour</h4>
                    <p>{{ $reservation->tour->title }}</p>
                </div>
                <div class="detail-item">
                    <h4>Asientos</h4>
                    <p>{{ $reservation->seats->pluck('seat_number')->implode(', ') }}</p>
                </div>
                <div class="detail-item">
                    <h4>Estado</h4>
                    <p>
                        @if($reservation->status->value === 'paid')
                            <span style="background: #10b981; color: white; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.85rem;">PAGADO</span>
                        @elseif($reservation->status->value === 'cancelled')
                            <span style="background: #ef4444; color: white; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.85rem;">CANCELADO</span>
                        @else
                            <span style="background: #f59e0b; color: white; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.85rem;">PENDIENTE</span>
                        @endif
                    </p>
                </div>
                <div class="detail-item" style="grid-column: span 2; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                    <h4>Total a Pagar</h4>
                    <p style="font-size: 1.5rem; color: var(--color-dark);">${{ number_format($reservation->total_amount, 2) }} MXN</p>
                </div>
            </div>

            @if($reservation->status->value === 'pending')
                <div class="warning-box">
                    <i class="fa-solid fa-clock-rotate-left" style="font-size: 1.5rem; margin-top: 0.2rem;"></i>
                    <div>
                        <h3 style="margin-bottom: 0.25rem;">Atención: Tiempo Límite</h3>
                        <p style="font-size: 0.95rem;">Tienes hasta el <strong>{{ \Carbon\Carbon::parse($reservation->expires_at)->translatedFormat('d \d\e F \a \l\a\s H:i') }} hrs</strong> para realizar y reportar tu pago/anticipo. De lo contrario, el sistema liberará los asientos automáticamente.</p>
                    </div>
                </div>

                <h3 style="margin-bottom: 1rem; color: var(--color-dark);">Datos para Depósito</h3>
                <div style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 1.5rem; text-align:left; margin-bottom: 2rem;">
                    <p><strong>Banco:</strong> BBVA Bancomer</p>
                    <p><strong>CLABE:</strong> 012345678901234567</p>
                    <p><strong>A nombre de:</strong> Viajes By Mex S.A. de C.V.</p>
                </div>
            @endif

            @php
                $whatsappMsg = urlencode("Hola, mi número de reserva es RES-" . str_pad($reservation->id, 4, '0', STR_PAD_LEFT) . " para el tour {$reservation->tour->title}. Aquí envío mi comprobante de pago.");
            @endphp
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('reservations.ticket', $reservation->public_token) }}" class="btn btn-outline" style="border-width: 2px;">
                    <i class="fa-solid fa-file-pdf"></i> Descargar Ticket PDF
                </a>
                @if($reservation->status->value === 'pending')
                    <a href="https://wa.me/527441295026?text={{ $whatsappMsg }}" target="_blank" class="btn btn-primary">
                        <i class="fa-brands fa-whatsapp"></i> Enviar Comprobante
                    </a>
                @endif
            </div>

            @if($reservation->status->value === 'pending')
                <hr style="border:none; border-top: 1px dashed #e2e8f0; margin: 3rem 0;">
                
                {{-- STRIPE REAL PAYMENT --}}
                <div style="background: linear-gradient(135deg, #0f172a, #1e293b); padding: 2.5rem; border-radius: var(--radius-xl); text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: .5rem; margin-bottom: .75rem;">
                        <i class="fa-solid fa-shield-halved" style="color: #a78bfa; font-size: 1.2rem;"></i>
                        <span style="color: #94a3b8; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px;">Pago Seguro con Stripe</span>
                    </div>
                    <h3 style="color: white; font-size: 1.3rem; margin-bottom: .5rem;">Paga en línea al instante</h3>
                    <p style="color: #94a3b8; font-size: .88rem; margin-bottom: 1.5rem;">Aceptamos Visa, Mastercard, American Express. Tu información está protegida con encriptación SSL.</p>
                    
                    <a href="{{ route('reservations.pay', $reservation->public_token) }}" 
                       class="btn" 
                       style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 1rem 3rem; font-size: 1.05rem; border-radius: var(--radius-lg); box-shadow: 0 8px 24px rgba(99,102,241,.35);"
                       onclick="this.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin\'></i> Redirigiendo a Stripe...';">
                        <i class="fa-solid fa-credit-card"></i> Pagar ${{ number_format($reservation->total_amount, 0) }} MXN con Tarjeta
                    </a>
                    
                    <div style="display: flex; align-items: center; justify-content: center; gap: 1.5rem; margin-top: 1.5rem;">
                        <img src="https://img.icons8.com/color/36/visa.png" alt="Visa" style="height: 24px; opacity: .7;">
                        <img src="https://img.icons8.com/color/36/mastercard-logo.png" alt="Mastercard" style="height: 24px; opacity: .7;">
                        <img src="https://img.icons8.com/color/36/amex.png" alt="Amex" style="height: 24px; opacity: .7;">
                        <span style="color: #475569; font-size: .7rem;">Powered by Stripe</span>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
