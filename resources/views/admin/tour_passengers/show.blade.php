<x-app-layout>
    @section('header-title', 'Pasajeros del Viaje')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0.25rem;">
                <i class="fa-solid fa-users-line"></i> Pasajeros: {{ $tour->title }}
            </h2>
            <p style="color: var(--text-muted); margin: 0;">
                {{ \Carbon\Carbon::parse($tour->start_date)->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($tour->end_date)->format('d/m/Y') }}
            </p>
        </div>
        <a href="{{ route('admin.tour-passengers.index') }}" class="btn-action"
           style="background: var(--slate-200); color: var(--slate-700); padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fa-solid fa-arrow-left"></i> Volver a Viajes
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div style="background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-check-circle" style="font-size: 1.2rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div style="background: #dbeafe; border: 1px solid #60a5fa; color: #1e40af; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-circle-info" style="font-size: 1.2rem;"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <div class="card">
        <div class="card-header" style="background: #f8fafc; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; color: var(--navy); font-weight: 700;">
                Listado Total de Pasajeros
                <span style="background: var(--primary); color: white; font-size: 0.8rem; padding: 2px 8px; border-radius: 12px; margin-left: 0.5rem;">{{ $allPassengers->count() }}</span>
            </h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre del Pasajero</th>
                            <th>Tipo</th>
                            <th>WhatsApp</th>
                            <th>Punto de Abordaje</th>
                            <th>Reserva #</th>
                            <th>Estatus</th>
                            <th style="text-align: center; width: 170px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allPassengers as $passenger)
                            @php
                                // Status badge
                                $statusBg = '#f1f5f9'; $statusColor = '#64748b'; $statusLabel = ucfirst($passenger->status);
                                if (in_array($passenger->status, ['paid', 'confirmed', 'active'])) {
                                    $statusBg = '#dcfce7'; $statusColor = '#166534'; $statusLabel = 'Confirmado';
                                } elseif ($passenger->status === 'pending') {
                                    $statusBg = '#fef08a'; $statusColor = '#854d0e'; $statusLabel = 'Pendiente';
                                } elseif ($passenger->status === 'partial') {
                                    $statusBg = '#dbeafe'; $statusColor = '#1e40af'; $statusLabel = 'Abono parcial';
                                } elseif ($passenger->status === 'cancelled') {
                                    $statusBg = '#fee2e2'; $statusColor = '#991b1b'; $statusLabel = 'Cancelado';
                                }
                            @endphp
                            <tr>
                                {{-- Nombre --}}
                                <td>
                                    <strong>{{ $passenger->name }}</strong>
                                    @if($passenger->is_titular)
                                        <span style="margin-left: 0.4rem; font-size: 0.7rem; background: var(--primary); color: white; padding: 2px 7px; border-radius: 12px; font-weight: 700; vertical-align: middle;">
                                            Titular
                                        </span>
                                    @endif
                                </td>

                                {{-- Tipo --}}
                                <td style="font-size: 0.9rem;">{{ $passenger->type }}</td>

                                {{-- WhatsApp --}}
                                <td>
                                    @if($passenger->whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $passenger->whatsapp) }}"
                                           target="_blank"
                                           style="color: #25D366; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.9rem;">
                                            <i class="fa-brands fa-whatsapp"></i> {{ $passenger->whatsapp }}
                                        </a>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">No registrado</span>
                                    @endif
                                </td>

                                {{-- Punto de Abordaje --}}
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $passenger->boarding_point_name ?: '—' }}
                                </td>

                                {{-- Reserva # --}}
                                <td>
                                    <a href="{{ route('admin.reservations.show', $passenger->reservation_id) }}"
                                       style="color: var(--navy); font-weight: 600; text-decoration: none; font-size: 0.9rem;">#{{ $passenger->reservation_id }}</a>
                                </td>

                                {{-- Estatus --}}
                                <td>
                                    <span style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 4px 9px; border-radius: 4px; font-size: 0.78rem; font-weight: 700;">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td style="text-align: center;">
                                    @if($passenger->is_titular)
                                        {{-- Titulares siempre son clientes --}}
                                        <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700;">
                                            <i class="fa-solid fa-check"></i> Ya es Cliente
                                        </span>
                                    @elseif($passenger->client_id)
                                        {{-- Pasajero ya convertido a cliente --}}
                                        <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 700;">
                                            <i class="fa-solid fa-check"></i> Ya es Cliente
                                        </span>
                                    @else
                                        {{-- Botón para agregar como cliente --}}
                                        <form action="{{ route('admin.tour-passengers.add-client', $passenger->id) }}" method="POST"
                                              onsubmit="return confirm('¿Registrar a {{ addslashes($passenger->name) }} como cliente del portal?');">
                                            @csrf
                                            <button type="submit" class="btn-action"
                                                    style="background: var(--navy); color: white; border: none; padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                                                <i class="fa-solid fa-user-plus"></i> Agregar Cliente
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; margin-bottom: 1rem; color: #cbd5e1; display: block;"></i>
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
