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
                            <th>Contacto</th>
                            <th>Lugar de Origen</th>
                            <th style="text-align: center;">Total Reservas</th>
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
                                    <div style="font-size: 0.85rem;"><i class="fa-solid fa-phone" style="color:var(--slate-400);"></i> {{ $client->phone }}</div>
                                    <div style="font-size: 0.85rem;"><i class="fa-solid fa-envelope" style="color:var(--slate-400);"></i> {{ $client->email }}</div>
                                </td>
                                <td>{{ $client->origin_city ?? 'No especificado' }}</td>
                                <td style="text-align: center;">
                                    <span class="badge badge-blue">{{ $client->reservations_count }} viajes</span>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #166534;">
                                    ${{ number_format($totalGasto, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
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
