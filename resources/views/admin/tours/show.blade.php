<x-app-layout>
    @section('header-title', 'Gestión de Reservas')

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
                    {{ $tour->seats()->count() }} / {{ $tour->total_seats }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-users"></i> Lista de Pasajeros y Reservas</h3>
            <a href="{{ route('dashboard') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
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
                        @forelse($tour->reservations as $reservation)
                            <tr>
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
                                    @if($reservation->status == 'pending')
                                        <span class="badge badge-orange"><i class="fa-regular fa-clock"></i> Pendiente</span>
                                    @elseif($reservation->status == 'paid')
                                        <span class="badge badge-green"><i class="fa-solid fa-check"></i> Pagado</span>
                                    @else
                                        <span class="badge" style="background: var(--slate-100); color: var(--text-muted);"><i class="fa-solid fa-xmark"></i> Cancelado</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($reservation->status == 'pending')
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                            <form action="{{ route('admin.reservations.status', $reservation->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="paid">
                                                <button type="submit" class="btn-action" style="background: #166534;" onclick="return confirm('¿Confirmar que se recibió el pago completo?')">
                                                    <i class="fa-solid fa-money-bill-wave"></i> Aprobar
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
                                        <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-lock"></i> Sin acciones</span>
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
</x-app-layout>
