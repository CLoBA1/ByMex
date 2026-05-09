<x-app-layout>
    @section('header-title', 'Directorio de Clientes')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-users"></i> Historial de Clientes</h2>
            <div class="search-box">
                <input type="text" placeholder="Buscar cliente..." style="padding: .5rem 1rem; border: 1px solid var(--border); border-radius: 6px; width: 250px;">
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Membresía</th>
                            <th>Contacto</th>
                            <th>Lugar de Origen</th>
                            <th style="text-align: center;">Viajes (Completados)</th>
                            <th style="text-align: center;">Bonificaciones</th>
                            <th style="text-align: right;">Gasto Estimado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            @php
                                $totalGasto = 0;
                                foreach($client->reservations as $res) {
                                    if($res->status->value == 'paid') {
                                        $totalGasto += $res->total_amount;
                                    }
                                }
                            @endphp
                            <tr>
                                <td style="font-weight: 700; color: var(--navy);">
                                    {{ $client->name }}<br>
                                    <span style="font-size: 0.75rem; font-weight: 400; color: var(--text-muted);">CURP: {{ $client->curp ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span style="background: var(--navy); color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                        {{ $client->membership_number ?? 'SIN MEMBRESÍA' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem;"><i class="fa-solid fa-phone" style="color:var(--slate-400);"></i> {{ $client->phone }}</div>
                                    <div style="font-size: 0.85rem;"><i class="fa-solid fa-envelope" style="color:var(--slate-400);"></i> {{ $client->email }}</div>
                                </td>
                                <td>{{ $client->origin_city ?? 'No especificado' }}</td>
                                <td style="text-align: center;">
                                    <div style="font-size: 1.1rem; font-weight: 700; color: var(--primary);">{{ $client->completed_trips_count }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Reservas Confirmadas</div>
                                </td>
                                <td style="text-align: center;">
                                    @if($client->available_bonuses > 0)
                                        <span class="badge badge-success" style="background: #16a34a; color: white; padding: 4px 8px; font-size: 0.8rem;">
                                            <i class="fa-solid fa-gift"></i> {{ $client->available_bonuses }} Disponible(s)
                                        </span>
                                    @else
                                        <div style="font-size: 0.8rem; color: var(--slate-500); font-weight: 600;">
                                            {{ $client->next_bonus_progress }} / {{ \App\Models\Client::TRIPS_FOR_BONUS }} viajes
                                        </div>
                                        <div style="width: 100%; background-color: #e2e8f0; border-radius: 4px; height: 6px; margin-top: 4px;">
                                            @php
                                                $percentage = ($client->next_bonus_progress / \App\Models\Client::TRIPS_FOR_BONUS) * 100;
                                            @endphp
                                            <div style="background-color: var(--primary); height: 6px; border-radius: 4px; width: {{ $percentage }}%;"></div>
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #166534;">
                                    ${{ number_format($totalGasto, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-user-xmark" style="font-size: 2rem; color: var(--border); margin-bottom: 1rem; display: block;"></i>
                                    Aún no hay clientes registrados en el sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
