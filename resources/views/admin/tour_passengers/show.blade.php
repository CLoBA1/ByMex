<x-app-layout>
    @section('header-title', 'Pasajeros del Viaje')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0.25rem;">
                <i class="fa-solid fa-users-line"></i> Pasajeros: {{ $tour->title }}
            </h2>
            <p style="color: var(--text-muted); margin: 0;">{{ \Carbon\Carbon::parse($tour->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($tour->end_date)->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('admin.tour-passengers.index') }}" class="btn-action" style="background: var(--slate-200); color: var(--slate-700); padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fa-solid fa-arrow-left"></i> Volver a Viajes
        </a>
    </div>

    <div class="card">
        <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; color: var(--navy); font-weight: 700;">Listado Total de Pasajeros ({{ $allPassengers->count() }})</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre del Pasajero</th>
                            <th>Tipo / Categoría</th>
                            <th>WhatsApp</th>
                            <th>Punto de Abordaje</th>
                            <th>Reserva #</th>
                            <th>Estatus</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allPassengers as $passenger)
                            @php
                                $statusBg = '#f1f5f9'; $statusColor = '#64748b'; $statusLabel = ucfirst($passenger->status);
                                if (in_array($passenger->status, ['paid', 'confirmed'])) {
                                    $statusBg = '#dcfce7'; $statusColor = '#166534'; $statusLabel = 'Confirmado';
                                } elseif ($passenger->status === 'pending') {
                                    $statusBg = '#fef08a'; $statusColor = '#854d0e'; $statusLabel = 'Pendiente';
                                } elseif ($passenger->status === 'partial') {
                                    $statusBg = '#dbeafe'; $statusColor = '#1e40af'; $statusLabel = 'Abono / Parcial';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $passenger->name }}</strong>
                                    @if($passenger->is_titular)
                                        <span style="margin-left: 0.5rem; font-size: 0.7rem; background: var(--primary); color: white; padding: 2px 6px; border-radius: 12px; font-weight: 600;">Titular</span>
                                    @endif
                                </td>
                                <td>
                                    {{ ucfirst($passenger->type) }}
                                </td>
                                <td>
                                    @if($passenger->whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $passenger->whatsapp) }}" target="_blank" style="color: #25D366; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i class="fa-brands fa-whatsapp"></i> {{ $passenger->whatsapp }}
                                        </a>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">No registrado</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.85rem;">
                                    {{ $passenger->boarding_point_name ?: 'No asignado' }}
                                </td>
                                <td style="color: var(--slate-500); font-weight: 600;">
                                    #{{ $passenger->reservation_id }}
                                </td>
                                <td>
                                    <span style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    @if(!$passenger->is_titular)
                                        <button disabled class="btn-action" style="background: var(--slate-200); color: var(--text-muted); padding: 0.4rem 0.6rem; border-radius: 4px; border: none; font-size: 0.8rem; cursor: not-allowed;" title="Ya es cliente o funcionalidad en desarrollo">
                                            <i class="fa-solid fa-user-plus"></i> Agregar Cliente
                                        </button>
                                    @else
                                        <span style="color: var(--slate-400); font-size: 0.8rem; font-weight: 600;">Ya es Cliente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; margin-bottom: 1rem; color: #cbd5e1;"></i><br>
                                    No hay pasajeros registrados en este viaje.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
