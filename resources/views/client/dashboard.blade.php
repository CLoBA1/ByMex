@extends('layouts.public')

@section('title', 'Mi Tablero')

@section('extra-css')
    <style>
        .dashboard-container {
            max-width: 1100px;
            margin: 4rem auto;
            padding: 0 1.5rem;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .dashboard-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            color: var(--color-dark);
            font-weight: 800;
        }
        .profile-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            border-left: 5px solid var(--color-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        .profile-info h3 {
            font-size: 1.25rem;
            color: var(--color-dark);
            margin-bottom: 0.25rem;
        }
        .profile-info p {
            color: var(--color-dark-muted);
            font-size: 0.9rem;
            margin: 0;
        }
        .membership-badge {
            background: var(--color-dark);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.5rem;
            color: var(--color-dark);
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--color-border);
            padding-bottom: 0.5rem;
        }
        .trip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .trip-card {
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .trip-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .trip-header {
            background: var(--color-light);
            padding: 1rem;
            border-bottom: 1px solid var(--color-border);
        }
        .trip-header h4 {
            font-size: 1.1rem;
            color: var(--color-dark);
            margin: 0 0 0.25rem 0;
        }
        .trip-header p {
            font-size: 0.85rem;
            color: var(--color-dark-muted);
            margin: 0;
        }
        .trip-body {
            padding: 1.25rem;
        }
        .trip-stat {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .trip-stat strong {
            color: var(--color-dark);
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-partial { background: #fef08a; color: #854d0e; }
        .status-pending { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f1f5f9; color: #64748b; }
        .trip-footer {
            padding: 1rem;
            border-top: 1px solid var(--color-border);
            text-align: center;
        }
        .btn-view {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--color-dark);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        .btn-view:hover {
            background: #000;
            color: white;
        }
        .coming-soon {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px dashed #cbd5e1;
            border-radius: var(--radius-lg);
            padding: 2rem;
            text-align: center;
            color: var(--color-dark-muted);
            margin-top: 2rem;
        }
    </style>
@endsection

@section('content')
    <main class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">¡Hola, {{ explode(' ', $client->name)[0] }}!</h1>
            <form action="{{ route('client.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline" style="border-width: 1px; padding: 0.5rem 1rem;">
                    <i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form>
        </div>

        <div class="profile-card">
            <div class="profile-info">
                <h3><i class="fa-solid fa-user-circle" style="color: var(--color-primary);"></i> Mi Perfil</h3>
                <p><i class="fa-solid fa-phone" style="width: 16px;"></i> {{ $client->phone }}</p>
                @if($client->email)
                    <p><i class="fa-solid fa-envelope" style="width: 16px;"></i> {{ $client->email }}</p>
                @endif
            </div>
            
            <div class="membership-badge">
                <i class="fa-solid fa-crown" style="color: #fbbf24;"></i>
                {{ $client->membership_number ?? 'Cliente Standard' }}
            </div>
        </div>

        <h2 class="section-title"><i class="fa-solid fa-suitcase-rolling"></i> Mis Próximos Viajes</h2>
        
        @if($activeTrips->count() > 0)
            <div class="trip-grid">
                @foreach($activeTrips->sortBy('tour.departure_date') as $res)
                    @php
                        $paid = $res->payments->where('status', 'approved')->sum('amount');
                        
                        if ($res->status->value === 'paid') {
                            $badgeClass = 'status-paid'; $badgeLabel = 'Pagado';
                        } elseif ($res->status->value === 'partial') {
                            $badgeClass = 'status-partial'; $badgeLabel = 'Pago Parcial';
                        } else {
                            $badgeClass = 'status-pending'; $badgeLabel = 'Pago Pendiente';
                        }
                    @endphp
                    <div class="trip-card">
                        <div class="trip-header">
                            <h4>{{ $res->tour->title }}</h4>
                            <p><i class="fa-solid fa-calendar-days"></i> {{ \Carbon\Carbon::parse($res->tour->departure_date)->format('d/m/Y') }}</p>
                        </div>
                        <div class="trip-body">
                            <div class="trip-stat">
                                <span>Folio:</span>
                                <strong>RES-{{ str_pad($res->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            </div>
                            <div class="trip-stat">
                                <span>Total a pagar:</span>
                                <strong>${{ number_format($res->total_amount, 2) }}</strong>
                            </div>
                            <div class="trip-stat">
                                <span>Saldo pendiente:</span>
                                <strong style="color: {{ $res->balance_due > 0 ? '#dc2626' : '#16a34a' }};">${{ number_format($res->balance_due, 2) }}</strong>
                            </div>
                            <div class="status-badge {{ $badgeClass }}">{{ $badgeLabel }}</div>
                        </div>
                        <div class="trip-footer">
                            <a href="{{ route('client.reservation', $res->id) }}" class="btn-view">Ver Detalles y Documentos</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="background: #f8fafc; padding: 2rem; border-radius: var(--radius-md); text-align: center; color: var(--color-dark-muted); margin-bottom: 3rem;">
                <i class="fa-solid fa-plane-slash" style="font-size: 2rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
                <p>No tienes viajes próximos en este momento.</p>
                <a href="{{ route('tours.index') }}" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">Ver Destinos</a>
            </div>
        @endif

        @if($pastTrips->count() > 0)
            <h2 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Historial de Viajes</h2>
            <div class="trip-grid" style="opacity: 0.8;">
                @foreach($pastTrips->sortByDesc('tour.departure_date') as $res)
                    <div class="trip-card">
                        <div class="trip-header">
                            <h4>{{ $res->tour->title }}</h4>
                            <p><i class="fa-solid fa-calendar-days"></i> {{ \Carbon\Carbon::parse($res->tour->departure_date)->format('d/m/Y') }}</p>
                        </div>
                        <div class="trip-footer" style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.85rem; color: var(--color-dark-muted);">
                                @if($res->status->value === 'cancelled')
                                    <span class="status-badge status-cancelled" style="margin: 0;">Cancelado</span>
                                @else
                                    Completado
                                @endif
                            </span>
                            <a href="{{ route('client.reservation', $res->id) }}" class="btn-view" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Ver Detalles</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="coming-soon">
            <i class="fa-solid fa-star" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
            <h3 style="color: var(--color-dark); margin-bottom: 0.5rem;">Programa de Lealtad</h3>
            <p>Próximamente podrás ver aquí tus puntos acumulados, recompensas y bonificaciones por viajar con nosotros.</p>
        </div>

    </main>
@endsection
