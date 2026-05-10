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

        <div class="loyalty-card" style="background: white; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 2rem; margin-top: 2rem; display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; box-shadow: var(--shadow-sm);">
            <div style="background: #fefce8; color: #eab308; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; flex-shrink: 0;">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div style="flex-grow: 1; min-width: 250px;">
                <h3 style="color: var(--color-dark); margin: 0 0 0.5rem 0; font-family: 'Montserrat', sans-serif; font-size: 1.5rem;">Programa de Lealtad</h3>
                <p style="color: var(--color-dark-muted); margin: 0 0 1rem 0;">Acumula {{ \App\Models\Client::TRIPS_FOR_BONUS }} viajes pagados y obtén una bonificación.</p>
                
                <div style="background: #f8fafc; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 600; color: var(--color-dark);">Progreso de Viajes</span>
                        <span style="font-weight: 700; color: var(--color-primary);">{{ $client->next_bonus_progress }} / {{ \App\Models\Client::TRIPS_FOR_BONUS }}</span>
                    </div>
                    <div style="width: 100%; background: #e2e8f0; border-radius: 6px; height: 10px; overflow: hidden;">
                        @php
                            $percentage = ($client->next_bonus_progress / \App\Models\Client::TRIPS_FOR_BONUS) * 100;
                        @endphp
                        <div style="background: #eab308; width: {{ $percentage }}%; height: 100%; transition: width 1s ease-in-out;"></div>
                    </div>
                </div>
            </div>
            <div style="text-align: center; min-width: 150px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem;">
                <div>
                    <div style="font-size: 3rem; font-weight: 900; color: var(--color-dark); line-height: 1;">{{ $client->available_bonuses }}</div>
                    <div style="font-size: 0.9rem; color: var(--color-dark-muted); text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-top: 0.5rem;">Bonos Disponibles</div>
                </div>
                
                @if(isset($activeBonusRequest))
                    @if($activeBonusRequest->status === 'pending')
                        <div style="background: #fef08a; color: #854d0e; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-size: 0.85rem; font-weight: 600; width: 100%;">
                            <i class="fa-solid fa-clock-rotate-left"></i> Solicitud en Revisión
                        </div>
                    @elseif($activeBonusRequest->status === 'approved')
                        <div style="background: #dcfce7; color: #166534; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-size: 0.85rem; font-weight: 600; width: 100%;">
                            <i class="fa-solid fa-check-circle"></i> Solicitud Aprobada
                        </div>
                    @endif
                @elseif($client->available_bonuses > 0)
                    <form action="{{ route('client.bonus.request') }}" method="POST" style="margin: 0; width: 100%;">
                        @csrf
                        <input type="hidden" name="request_type" value="Descuento en próximo viaje">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.9rem; font-weight: 600; background: var(--color-dark); border: none; border-radius: var(--radius-md); color: white; cursor: pointer; transition: background 0.2s;">
                            <i class="fa-solid fa-hand-pointer"></i> Solicitar Canje
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div style="background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 1rem; border-radius: 6px; margin-top: 1.5rem;">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 6px; margin-top: 1.5rem;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <h2 class="section-title" style="margin-top: 3rem;"><i class="fa-solid fa-list-check"></i> Historial de Solicitudes</h2>
        
        @if(isset($bonusRequestsHistory) && $bonusRequestsHistory->count() > 0)
            <div style="background: white; border-radius: var(--radius-md); border: 1px solid var(--color-border); overflow: hidden; box-shadow: var(--shadow-sm);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead style="background: var(--color-light); border-bottom: 1px solid var(--color-border);">
                            <tr>
                                <th style="padding: 1rem; font-size: 0.9rem; color: var(--color-dark-muted);">Fecha</th>
                                <th style="padding: 1rem; font-size: 0.9rem; color: var(--color-dark-muted);">Solicitud</th>
                                <th style="padding: 1rem; font-size: 0.9rem; color: var(--color-dark-muted);">Estado</th>
                                <th style="padding: 1rem; font-size: 0.9rem; color: var(--color-dark-muted);">Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonusRequestsHistory as $req)
                                @php
                                    $badgeBg = '#f1f5f9'; $badgeColor = '#64748b'; $badgeLabel = 'Desconocido';
                                    if ($req->status === 'pending') {
                                        $badgeBg = '#fef08a'; $badgeColor = '#854d0e'; $badgeLabel = 'En Revisión';
                                    } elseif ($req->status === 'approved') {
                                        $badgeBg = '#dcfce7'; $badgeColor = '#166534'; $badgeLabel = 'Aprobada';
                                    } elseif ($req->status === 'rejected') {
                                        $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; $badgeLabel = 'Rechazada';
                                    } elseif ($req->status === 'applied') {
                                        $badgeBg = '#eff6ff'; $badgeColor = '#1e3a8a'; $badgeLabel = 'Aplicada';
                                    }
                                @endphp
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: 1rem; font-size: 0.9rem;">{{ $req->created_at->format('d/m/Y') }}</td>
                                    <td style="padding: 1rem; font-size: 0.9rem; font-weight: 600;">{{ $req->request_type }}</td>
                                    <td style="padding: 1rem; font-size: 0.9rem;">
                                        <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">
                                            {{ $badgeLabel }}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; font-size: 0.85rem; color: var(--color-dark-muted);">
                                        @if($req->client_notes)
                                            <div style="margin-bottom: 0.25rem;"><strong>Tú:</strong> {{ $req->client_notes }}</div>
                                        @endif
                                        @if($req->admin_notes)
                                            <div style="color: var(--color-dark);"><strong>Admin:</strong> {{ $req->admin_notes }}</div>
                                        @endif
                                        @if(!$req->client_notes && !$req->admin_notes)
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div style="background: #f8fafc; padding: 2rem; border-radius: var(--radius-md); text-align: center; color: var(--color-dark-muted); border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
                <p>Aún no has realizado ninguna solicitud de bonificación.</p>
            </div>
        @endif

    </main>
@endsection
