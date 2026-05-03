<x-app-layout>
    @section('header-title', 'Gestión de Reservas')

    @section('extra-css')
    <style>
        .admin-bus-map-container {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .admin-bus-map {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            min-width: 300px;
        }
        
        .admin-bus-front {
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: var(--navy);
            border-bottom: 2px dashed var(--border);
            padding-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .admin-seat-grid {
            display: grid;
            grid-template-columns: repeat(4, 40px);
            gap: 10px;
            justify-content: center;
        }
        .admin-seat-grid > div:nth-child(4n+2) {
            margin-right: 20px; /* Pasillo */
        }
        
        .admin-seat {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            position: relative;
            cursor: help;
        }
        
        .admin-seat-free {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            color: var(--text-muted);
        }
        
        .admin-seat-occupied {
            color: #ffffff;
            text-shadow: 0 1px 1px rgba(0,0,0,0.5);
        }

        /* Tooltip simple sin romper layout */
        .admin-seat .tooltip-content {
            display: none;
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--navy);
            color: white;
            padding: 0.75rem;
            border-radius: 6px;
            width: max-content;
            max-width: 220px;
            font-size: 0.8rem;
            font-weight: normal;
            text-align: left;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            z-index: 50;
        }
        .admin-seat:hover .tooltip-content {
            display: block;
        }
        .tooltip-row { margin-bottom: 4px; }
        .tooltip-row strong { color: var(--gold); }

        .admin-side-panel {
            flex: 1;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .admin-legend-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .admin-legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .admin-stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .admin-stat-item {
            background: var(--bg-body);
            border: 1px solid var(--border);
            padding: 1rem;
            border-radius: 6px;
            text-align: center;
        }

        .admin-stat-item strong {
            display: block;
            font-size: 1.25rem;
            color: var(--navy);
        }

        .admin-stat-item span {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }
    </style>
    @endsection

    @php
        // Estructura de datos para el croquis
        $seatData = [];
        
        // Contadores Operativos
        $stats = [
            'occupied' => 0,
            'free' => 0,
            'pending' => 0,
            'paid' => 0,
        ];
        
        // Catálogo activo para leyendas y contadores
        $activeBoardingPoints = \App\Models\BoardingPoint::where('is_active', true)->orderBy('name')->get();
        
        $bpCounts = [];
        foreach($activeBoardingPoints as $bp) {
            $bpCounts[$bp->id] = 0;
        }
        $bpCounts['legacy'] = 0;

        foreach($tour->reservations as $reservation) {
            if ($reservation->status->value == 'cancelled') continue;
            
            $status = $reservation->status->value;
            
            $passengerBySeat = [];
            foreach($reservation->passengers as $p) {
                $passengerBySeat[$p->seat_number] = $p;
            }
            
            foreach($reservation->seats as $seat) {
                $sn = $seat->seat_number;
                $passenger = $passengerBySeat[$sn] ?? null;
                
                $bgColor = '#cbd5e1'; 
                $bpName = 'Sin Punto / Legacy';
                $pName = $reservation->client->name . ' (Legacy)';
                $pType = '';
                
                $stats['occupied']++;
                if ($status == 'pending') $stats['pending']++;
                if ($status == 'paid') $stats['paid']++;
                
                if ($passenger) {
                    $pName = $passenger->name;
                    $pType = ucfirst($passenger->passenger_type);
                    if ($passenger->boardingPoint) {
                        $bgColor = $passenger->boardingPoint->color_hex;
                        $bpName = $passenger->boardingPoint->name;
                        $bpCounts[$passenger->boardingPoint->id]++;
                    } else {
                        $bpCounts['legacy']++;
                    }
                } else {
                    $bpCounts['legacy']++;
                }
                
                $borderColor = ($status == 'pending') ? '#f59e0b' : '#166534';
                $borderWidth = '3px';
                
                $seatData[$sn] = [
                    'bg' => $bgColor,
                    'border' => $borderColor,
                    'borderWidth' => $borderWidth,
                    'name' => $pName,
                    'type' => $pType,
                    'bp' => $bpName,
                    'status' => $status == 'pending' ? 'Pendiente' : 'Pagada'
                ];
            }
        }
        
        $totalSeats = $tour->total_seats ?? 36;
        $stats['free'] = $totalSeats - $stats['occupied'];
        
        $rows = ceil($totalSeats / 4);
        $seatNum = 1;
    @endphp

    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.5rem; color: var(--navy); font-weight: 800; margin-bottom: 0.25rem;">
                    {{ $tour->title }}
                </h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">
                    <i class="fa-regular fa-calendar"></i> Salida: {{ \Carbon\Carbon::parse($tour->departure_date)->translatedFormat('d \d\e F Y - H:i') }} hrs | 
                    <i class="fa-solid fa-location-dot"></i> {{ $tour->destination }}
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Ocupación</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
                    {{ $stats['occupied'] }} / {{ $totalSeats }}
                </div>
            </div>
        </div>
    </div>

    <!-- CROQUIS OPERATIVO (ESTILO CARD ESTÁNDAR) -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-map-location-dot"></i> Croquis Operativo de Abordaje</h3>
        </div>
        <div class="card-body">
            <div class="admin-bus-map-container">
                
                <!-- MAPA DEL AUTOBÚS -->
                <div class="admin-bus-map">
                    <div class="admin-bus-front">
                        <i class="fa-solid fa-steering-wheel"></i> Frente
                    </div>
                    
                    @include('partials._bus_map', ['mode' => 'admin', 'seatData' => $seatData, 'tour' => $tour])
                </div>

                <!-- COLUMNA DERECHA: LEYENDAS Y RESUMEN -->
                <div class="admin-side-panel">
                    
                    <div>
                        <h4 style="font-weight: 700; font-size: 0.95rem; color: var(--navy); margin-bottom: 1rem;">
                            <i class="fa-solid fa-chart-pie" style="color: var(--primary);"></i> Ocupación
                        </h4>
                        <div class="admin-stat-grid">
                            <div class="admin-stat-item">
                                <strong>{{ $stats['free'] }}</strong>
                                <span>Libres</span>
                            </div>
                            <div class="admin-stat-item">
                                <strong>{{ $stats['occupied'] }}</strong>
                                <span>Ocupados</span>
                            </div>
                            <div class="admin-stat-item" style="border-color: #fcd34d;">
                                <strong style="color: #d97706;">{{ $stats['pending'] }}</strong>
                                <span>Pendientes</span>
                            </div>
                            <div class="admin-stat-item" style="border-color: #86efac;">
                                <strong style="color: #166534;">{{ $stats['paid'] }}</strong>
                                <span>Pagados</span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px;">
                            <h4 style="font-weight: 700; font-size: 0.95rem; color: var(--navy); margin-bottom: 1rem;">
                                <i class="fa-solid fa-palette" style="color: var(--gold);"></i> Puntos de Abordaje
                            </h4>
                            @foreach($activeBoardingPoints as $bp)
                                <div class="admin-legend-item">
                                    <div class="admin-legend-color" style="background: {{ $bp->color_hex }};"></div>
                                    <span style="flex: 1;">{{ $bp->name }}</span>
                                    <span style="font-weight: 700; color: var(--navy);">{{ $bpCounts[$bp->id] }}</span>
                                </div>
                            @endforeach
                            @if($bpCounts['legacy'] > 0)
                                <div class="admin-legend-item">
                                    <div class="admin-legend-color" style="background: #cbd5e1;"></div>
                                    <span style="flex: 1; color: var(--text-muted);">Sin Punto / Legacy</span>
                                    <span style="font-weight: 700; color: var(--navy);">{{ $bpCounts['legacy'] }}</span>
                                </div>
                            @endif
                        </div>

                        <div style="flex: 1; min-width: 200px;">
                            <h4 style="font-weight: 700; font-size: 0.95rem; color: var(--navy); margin-bottom: 1rem;">
                                <i class="fa-solid fa-money-check-dollar" style="color: #166534;"></i> Estados Financieros
                            </h4>
                            <div class="admin-legend-item">
                                <div class="admin-legend-color" style="background: #f8fafc; border: 3px solid #166534;"></div>
                                <span>Pagado / Confirmado</span>
                            </div>
                            <div class="admin-legend-item">
                                <div class="admin-legend-color" style="background: #f8fafc; border: 3px solid #f59e0b;"></div>
                                <span>Pendiente de Pago</span>
                            </div>
                            <div class="admin-legend-item">
                                <div class="admin-legend-color" style="background: #f8fafc; border: 2px solid #e2e8f0;"></div>
                                <span style="color: var(--text-muted);">Disponible</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-users"></i> Lista de Pasajeros y Reservas</h3>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; color: var(--text-muted); cursor: pointer;">
                    <input type="checkbox" id="showArchived" onchange="toggleArchived()" style="accent-color: var(--primary);">
                    Mostrar canceladas/expiradas
                </label>
                <a href="{{ route('dashboard') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Cliente Contacto</th>
                            <th style="text-align: center;">Asientos</th>
                            <th style="text-align: right;">Total MXN</th>
                            <th style="text-align: center;">Estado</th>
                            <th style="text-align: right;">Acciones de Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $archivedStatuses = ['cancelled', 'expired'];
                        @endphp
                        @forelse($tour->reservations as $reservation)
                            @php
                                $isArchived = in_array($reservation->status->value, $archivedStatuses);
                                $hasPayments = $reservation->payments->count() > 0;
                                $hasAdjustments = $reservation->adjustments->count() > 0;
                                $canDelete = $isArchived && !$hasPayments && !$hasAdjustments;
                            @endphp
                            <tr class="{{ $isArchived ? 'archived-row' : '' }}" style="{{ $isArchived ? 'display: none;' : '' }}">
                                <td style="font-family: monospace; font-weight: 700; color: var(--slate-500);">
                                    RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--navy);">{{ $reservation->client->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                                        <i class="fa-solid fa-phone"></i> {{ $reservation->client->phone }}<br>
                                        <i class="fa-regular fa-envelope"></i> {{ $reservation->client->email }}
                                    </div>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--navy);">
                                    {{ $reservation->seats->pluck('seat_number')->implode(', ') }}
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #166534;">
                                    ${{ number_format($reservation->total_amount, 2) }}
                                </td>
                                <td style="text-align: center;">
                                    @if($reservation->status->value == 'pending')
                                        <span class="badge badge-orange"><i class="fa-regular fa-clock"></i> Pendiente</span>
                                    @elseif($reservation->status->value == 'partial')
                                        <span class="badge" style="background: #fef08a; color: #854d0e; border: 1px solid #fde047;"><i class="fa-solid fa-star-half-stroke"></i> Anticipo</span>
                                    @elseif($reservation->status->value == 'paid')
                                        <span class="badge badge-green"><i class="fa-solid fa-check"></i> Pagado</span>
                                    @elseif($reservation->status->value == 'expired')
                                        <span class="badge" style="background: #e2e8f0; color: #475569;"><i class="fa-solid fa-hourglass-end"></i> Expirado</span>
                                    @else
                                        <span class="badge" style="background: var(--slate-100); color: var(--text-muted);"><i class="fa-solid fa-xmark"></i> Cancelado</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if(in_array($reservation->status->value, ['pending', 'partial']))
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                            <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);" title="Ver Detalle / Abonar">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.reservations.payment', $reservation->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="amount" value="{{ $reservation->balance_due }}">
                                                <button type="submit" class="btn-action" style="background: #166534;" onclick="return confirm('¿Liquidar saldo de ${{ number_format($reservation->balance_due, 2) }}?')">
                                                    <i class="fa-solid fa-money-bill-wave"></i> Liquidar
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.reservations.status', $reservation->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn-action" style="background: var(--primary);" onclick="return confirm('¿Cancelar reserva? Los asientos se liberarán para el público.')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                            <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);" title="Ver Detalle">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if($canDelete)
                                                <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action" style="background: #dc2626; color: white;" title="Eliminar definitivamente" onclick="return confirm('¿Eliminar esta reservación DEFINITIVAMENTE? Esta acción no se puede deshacer.')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span style="font-size: 0.75rem; color: var(--text-muted);" title="No se puede eliminar: tiene pagos o ajustes registrados">
                                                    <i class="fa-solid fa-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fa-regular fa-folder-open" style="font-size: 2rem; color: var(--border); margin-bottom: 1rem; display: block;"></i>
                                    Aún no hay reservaciones para este viaje.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @section('extra-js')
    <script>
        function toggleArchived() {
            const show = document.getElementById('showArchived').checked;
            document.querySelectorAll('.archived-row').forEach(row => {
                row.style.display = show ? '' : 'none';
            });
        }
    </script>
    @endsection
</x-app-layout>

