<x-app-layout>
    @section('header-title', 'Detalle de Reservación')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.5rem; color: var(--navy); font-weight: 800; margin-bottom: 0.25rem;">
                Reservación: RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Creada: {{ $reservation->created_at->translatedFormat('d \d\e F Y, H:i') }}
            </p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.tours.show', $reservation->tour_id) }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
                <i class="fa-solid fa-arrow-left"></i> Volver al Tour
            </a>
            @if($reservation->status->value == 'pending')
                <span class="badge badge-orange" style="font-size: 1rem;"><i class="fa-regular fa-clock"></i> Pendiente</span>
            @elseif($reservation->status->value == 'paid')
                <span class="badge badge-green" style="font-size: 1rem;"><i class="fa-solid fa-check"></i> Pagada</span>
            @else
                <span class="badge" style="background: var(--slate-100); color: var(--text-muted); font-size: 1rem;"><i class="fa-solid fa-xmark"></i> Cancelada</span>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        
        <!-- COLUMNA IZQUIERDA -->
        <div>
            <!-- Pasajeros -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-users"></i> Desglose de Pasajeros</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Asiento</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Estatus Validación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservation->passengers as $passenger)
                                <tr>
                                    <td style="font-weight: 700; color: var(--navy);">{{ $passenger->seat_number }}</td>
                                    <td>{{ $passenger->name }}</td>
                                    <td>
                                        <div>{{ ucfirst($passenger->passenger_type) }}</div>
                                        @if($passenger->benefit_label)
                                            <span style="font-size: 0.8rem; background: var(--slate-100); padding: 0.1rem 0.4rem; border-radius: 4px; color: var(--text-muted);">
                                                {{ $passenger->benefit_label }}
                                            </span>
                                        @endif
                                        @if($passenger->birthdate)
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                                Nac: {{ $passenger->birthdate }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($passenger->validation_status == 'pending')
                                            <span class="badge badge-orange">Pendiente</span>
                                        @elseif($passenger->validation_status == 'approved')
                                            <span class="badge badge-green">Aprobado</span>
                                        @elseif($passenger->validation_status == 'rejected')
                                            <span class="badge" style="background: var(--primary); color: white;">Rechazado</span>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 0.9rem;">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">Esta reservación legacy no tiene pasajeros desglosados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Información Operativa -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-bus"></i> Datos del Tour y Operación</h3>
                </div>
                <div class="card-body">
                    <p style="margin-bottom: 0.5rem;"><strong>Destino:</strong> {{ $reservation->tour->destination }} ({{ $reservation->tour->title }})</p>
                    <p style="margin-bottom: 0.5rem;"><strong>Salida:</strong> {{ $reservation->tour->departure_date->translatedFormat('d \d\e F Y - H:i') }} hrs</p>
                    <p style="margin-bottom: 0;"><strong>Asientos Reservados (Legacy/Total):</strong> <span style="font-weight: 700; color: var(--navy);">{{ $reservation->seats->pluck('seat_number')->implode(', ') }}</span></p>
                    
                    @if($reservation->status->value == 'pending' && $reservation->expires_at)
                        <div style="margin-top: 1rem; padding: 1rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; color: #b45309;">
                            <i class="fa-solid fa-triangle-exclamation"></i> <strong>Expiración:</strong> Esta reserva vencerá el {{ $reservation->expires_at->translatedFormat('d M, H:i') }} si no es pagada.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA -->
        <div>
            <!-- Contacto Principal -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-address-card"></i> Contacto Principal</h3>
                </div>
                <div class="card-body">
                    <h4 style="font-weight: 700; color: var(--navy); margin-bottom: 0.5rem; font-size: 1.1rem;">{{ $reservation->client->name }}</h4>
                    <p style="margin-bottom: 0.5rem; color: var(--slate-600);">
                        <i class="fa-regular fa-envelope" style="width: 20px;"></i> {{ $reservation->client->email }}
                    </p>
                    <p style="margin-bottom: 0.5rem; color: var(--slate-600);">
                        <i class="fa-solid fa-phone" style="width: 20px;"></i> {{ $reservation->client->phone }}
                    </p>
                    @if($reservation->client->whatsapp)
                        <p style="margin-bottom: 0; color: var(--slate-600);">
                            <i class="fa-brands fa-whatsapp" style="width: 20px; color: #25D366;"></i> {{ $reservation->client->whatsapp }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Finanzas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-file-invoice-dollar"></i> Resumen Financiero</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: var(--slate-600);">Subtotal:</span>
                        <span style="font-weight: 600;">${{ number_format($reservation->subtotal, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--primary);">
                        <span>Descuentos Aplicados:</span>
                        <span style="font-weight: 600;">-${{ number_format($reservation->discount_total, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 1.25rem; font-weight: 800; color: var(--navy); margin-bottom: 1rem;">
                        <span>Total:</span>
                        <span style="color: #166534;">${{ number_format($reservation->total_amount, 2) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: var(--slate-600);">Balance Pendiente:</span>
                        <span style="font-weight: 600; color: {{ $reservation->balance_due > 0 ? 'var(--primary)' : 'var(--text-muted)' }};">
                            ${{ number_format($reservation->balance_due, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas (Reservadas para después) -->
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-bolt"></i> Acciones Administrativas</h3>
                </div>
                <div class="card-body">
                    <p style="font-size: 0.9rem; color: var(--text-muted);">
                        <em>Las acciones de validación de pasajeros y pagos se implementarán en la siguiente etapa.</em>
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
