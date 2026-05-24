<x-app-layout>
    @section('header-title', 'Pasajeros por Viaje')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0;">
            <i class="fa-solid fa-users-line"></i> Lista de Pasajeros por Viaje
        </h2>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Viaje</th>
                            <th>Fechas</th>
                            <th style="text-align: center;">Pasajeros Adicionales</th>
                            <th style="text-align: center;">Total (Incluyendo Titulares)</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tours as $tour)
                            @php
                                $totalAdditionalPassengers = $tour->reservations->sum(function($res) {
                                    return $res->passengers->count();
                                });
                                $totalTitulares = $tour->reservations->count();
                                $totalOverall = $totalAdditionalPassengers + $totalTitulares;
                            @endphp
                            @if($totalOverall > 0)
                            <tr>
                                <td>
                                    <strong>{{ $tour->title }}</strong>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($tour->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tour->end_date)->format('d/m/Y') }}
                                </td>
                                <td style="text-align: center; font-weight: 600;">
                                    {{ $totalAdditionalPassengers }}
                                </td>
                                <td style="text-align: center; font-weight: 600; color: var(--primary);">
                                    {{ $totalOverall }}
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.tour-passengers.show', $tour->id) }}" class="btn-action" style="background: var(--navy); color: white; padding: 0.4rem 0.8rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">
                                        <i class="fa-solid fa-eye"></i> Ver Pasajeros
                                    </a>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; color: #cbd5e1;"></i><br>
                                    No hay viajes con reservaciones activas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
