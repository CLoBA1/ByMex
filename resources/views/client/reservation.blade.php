@extends('layouts.public')

@section('title', 'Detalle de Reservación')

@section('extra-css')
    <style>
        .res-container {
            max-width: 1100px;
            margin: 4rem auto;
            padding: 0 1.5rem;
        }
        .res-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .res-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            color: var(--color-dark);
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .res-subtitle {
            color: var(--color-dark-muted);
            font-size: 1rem;
        }
        .card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-border);
            margin-bottom: 1.5rem;
        }
        .card-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.25rem;
            color: var(--color-dark);
            margin-bottom: 1.25rem;
            border-bottom: 2px solid var(--color-border);
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--color-border);
        }
        .stat-row:last-child {
            border-bottom: none;
        }
        .payment-list {
            margin-top: 1rem;
        }
        .payment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            border: 1px solid var(--color-border);
        }
        .passenger-list {
            margin-top: 1rem;
        }
        .passenger-item {
            padding: 1rem;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            background: white;
        }
        .btn-doc {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid var(--color-dark);
            color: var(--color-dark);
            border-radius: var(--radius-md);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .btn-doc:hover {
            background: var(--color-dark);
            color: white;
        }
        .btn-primary-doc {
            background: #d62828;
            color: white;
            border-color: #d62828;
        }
        .btn-primary-doc:hover {
            background: #b91c1c;
        }
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-partial { background: #fef08a; color: #854d0e; }
        .status-pending { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f1f5f9; color: #64748b; }
    </style>
@endsection

@section('content')
    <main class="res-container">
        <div class="res-header">
            <div>
                <a href="{{ route('client.dashboard') }}" style="color: var(--color-dark-muted); text-decoration: none; font-size: 0.9rem; margin-bottom: 1rem; display: inline-block;">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Mi Tablero
                </a>
                <h1 class="res-title">{{ $reservation->tour->title }}</h1>
                <p class="res-subtitle">Folio: <strong>RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}</strong> | Fecha: {{ \Carbon\Carbon::parse($reservation->tour->departure_date)->format('d/m/Y H:i') }}</p>
            </div>
            
            @php
                if ($reservation->status->value === 'paid') {
                    $badgeClass = 'status-paid'; $badgeLabel = 'Pagado';
                } elseif ($reservation->status->value === 'partial') {
                    $badgeClass = 'status-partial'; $badgeLabel = 'Pago Parcial';
                } elseif ($reservation->status->value === 'cancelled') {
                    $badgeClass = 'status-cancelled'; $badgeLabel = 'Cancelado';
                } else {
                    $badgeClass = 'status-pending'; $badgeLabel = 'Pago Pendiente';
                }
            @endphp
            <div class="status-badge {{ $badgeClass }}">
                {{ $badgeLabel }}
            </div>
        </div>

        <div class="grid-2">
            <!-- Columna Izquierda: Finanzas -->
            <div>
                <div class="card">
                    <h2 class="card-title"><i class="fa-solid fa-file-invoice-dollar"></i> Resumen de Pago</h2>
                    
                    <div class="stat-row">
                        <span style="color: var(--color-dark-muted);">Total del Viaje</span>
                        <strong style="font-size: 1.1rem;">${{ number_format($reservation->total_amount, 2) }} MXN</strong>
                    </div>
                    
                    @php
                        $paid = $reservation->payments->where('status', 'approved')->sum('amount');
                    @endphp
                    
                    <div class="stat-row">
                        <span style="color: var(--color-dark-muted);">Total Abonado</span>
                        <strong style="color: #166534; font-size: 1.1rem;">${{ number_format($paid, 2) }} MXN</strong>
                    </div>
                    
                    <div class="stat-row" style="background: #f8fafc; padding: 1rem; margin-top: 1rem; border-radius: var(--radius-md);">
                        <span style="font-weight: 700; color: var(--color-dark);">Saldo Pendiente</span>
                        <strong style="color: {{ $reservation->balance_due > 0 ? '#dc2626' : '#16a34a' }}; font-size: 1.25rem;">
                            ${{ number_format($reservation->balance_due, 2) }} MXN
                        </strong>
                    </div>

                    <div style="margin-top: 1.5rem; text-align: center;">
                        <a href="{{ route('reservations.ticket', $reservation->public_token) }}" class="btn-doc btn-primary-doc" style="width: 100%; justify-content: center; padding: 0.75rem;">
                            <i class="fa-solid fa-file-pdf"></i> Descargar Ticket Principal
                        </a>
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title"><i class="fa-solid fa-receipt"></i> Historial de Abonos</h2>
                    @php
                        $approvedPayments = $reservation->payments->where('status', 'approved')->sortByDesc('created_at');
                    @endphp
                    
                    @if($approvedPayments->count() > 0)
                        <div class="payment-list">
                            @foreach($approvedPayments as $payment)
                                <div class="payment-item">
                                    <div>
                                        <strong style="display: block; color: var(--color-dark);">${{ number_format($payment->amount, 2) }} MXN</strong>
                                        <span style="font-size: 0.8rem; color: var(--color-dark-muted);">
                                            {{ $payment->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('reservations.voucher', [$reservation->public_token, $payment->id]) }}" class="btn-doc" title="Descargar Voucher">
                                        <i class="fa-solid fa-download"></i> Voucher
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color: var(--color-dark-muted); font-size: 0.9rem; text-align: center; margin-top: 1rem;">
                            Aún no hay abonos registrados para esta reservación.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Columna Derecha: Pasajeros -->
            <div>
                <div class="card">
                    <h2 class="card-title"><i class="fa-solid fa-users"></i> Pasajeros ({{ $reservation->passengers->where('status', '!=', 'cancelled')->count() }})</h2>
                    
                    <div class="passenger-list">
                        @foreach($reservation->passengers->where('status', '!=', 'cancelled') as $passenger)
                            <div class="passenger-item">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div>
                                        <strong style="color: var(--color-dark); display: block;">{{ $passenger->name }}</strong>
                                        <span style="font-size: 0.85rem; color: var(--color-dark-muted);">{{ ucfirst($passenger->passenger_type) }}</span>
                                    </div>
                                    <div style="text-align: right;">
                                        <span style="font-size: 0.8rem; background: var(--color-light); padding: 0.25rem 0.5rem; border-radius: 4px; color: var(--color-dark-muted);">
                                            Asiento: <strong>{{ $passenger->seat_number }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
