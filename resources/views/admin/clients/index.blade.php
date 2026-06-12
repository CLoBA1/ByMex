<x-app-layout>
    @section('header-title', 'Directorio de Clientes')

    <div class="card">
        <div class="card-header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h2 class="card-title" style="margin: 0;"><i class="fa-solid fa-users"></i> Historial de Clientes</h2>
                <a href="{{ route('admin.clients.create') }}" class="btn-action" style="background: var(--navy); color: white; border: none; text-decoration: none; padding: 0.4rem 1rem; font-size: 0.9rem;">
                    <i class="fa-solid fa-plus"></i> Nuevo Cliente
                </a>
            </div>
            <div class="search-box">
                <input type="text" id="clientSearch" placeholder="Buscar cliente..." style="padding: .5rem 1rem; border: 1px solid var(--border); border-radius: 6px; width: 250px;">
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
                            <th style="text-align: center;">Acciones</th>
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
                            <tr style="{{ $client->status === 'inactive' ? 'opacity: 0.5;' : '' }}">
                                <td style="font-weight: 700;">
                                    <a href="{{ route('admin.clients.show', $client->id) }}" style="color: var(--navy); text-decoration: none;">
                                        {{ $client->name }}
                                    </a>
                                    @if($client->status === 'active')
                                        <span style="font-size: 0.7rem; background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px; vertical-align: middle; margin-left: 0.25rem;">● Activo</span>
                                    @else
                                        <span style="font-size: 0.7rem; background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; vertical-align: middle; margin-left: 0.25rem;">● Inactivo</span>
                                    @endif
                                    <br>
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
                                <td style="text-align: center; white-space: nowrap;">
                                    <a href="{{ route('admin.clients.show', $client->id) }}" class="btn-action" style="padding: 0.35rem 0.75rem; font-size: 0.85rem; background: var(--slate-100); color: var(--navy); border: 1px solid var(--border); display: inline-block; margin-right: 0.25rem;" title="Ver Detalle">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn-action" style="padding: 0.35rem 0.75rem; font-size: 0.85rem; background: var(--navy); color: white; border: none; display: inline-block;" title="Editar Cliente">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
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

<script>
document.getElementById('clientSearch')
    .addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.data-table tbody tr');

        rows.forEach(function (row) {
            // Ignorar la fila de "no hay clientes"
            if (row.querySelector('td[colspan]')) {
                row.style.display = '';
                return;
            }
            // Buscar en: nombre, teléfono, email, 
            // membresía y ciudad de origen
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });

        // Si no hay resultados visibles, mostrar mensaje
        const visibleRows = [...rows].filter(r => 
            !r.querySelector('td[colspan]') && 
            r.style.display !== 'none'
        );
        const tbody = document.querySelector('.data-table tbody');
        const noResults = document.getElementById('no-search-results');
        
        if (visibleRows.length === 0 && query !== '') {
            if (!noResults) {
                const tr = document.createElement('tr');
                tr.id = 'no-search-results';
                tr.innerHTML = `<td colspan="8" style="text-align:center; 
                    padding: 2rem; color: var(--text-muted);">
                    <i class="fa-solid fa-magnifying-glass" 
                       style="font-size:1.5rem; display:block; 
                              margin-bottom:0.5rem;"></i>
                    Sin resultados para "<strong>${this.value}</strong>"
                </td>`;
                tbody.appendChild(tr);
            }
        } else {
            if (noResults) noResults.remove();
        }
    });
</script>
</x-app-layout>
