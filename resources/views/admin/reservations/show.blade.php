<x-app-layout>
    @section('header-title', 'Detalle de Reservación')

    @if (session('success'))
        <div style="background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

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
                                        @elseif($passenger->validation_status == 'validated')
                                            <span class="badge badge-green">Validado</span>
                                        @elseif($passenger->validation_status == 'rejected')
                                            <span class="badge" style="background: var(--primary); color: white;">Rechazado</span>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 0.9rem;">N/A</span>
                                        @endif
                                        
                                        @if($passenger->validation_notes)
                                            <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 0.35rem; font-style: italic;">
                                                <i class="fa-regular fa-comment"></i> {{ $passenger->validation_notes }}
                                            </div>
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

            <!-- Validación de Pasajeros -->
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-id-card"></i> Validación Pendiente</h3>
                </div>
                <div class="card-body">
                    @php
                        $pendingPassengers = $reservation->passengers->where('validation_status', 'pending');
                    @endphp
                    @if($pendingPassengers->isEmpty())
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0;">
                            <i class="fa-solid fa-check-circle" style="color: #166534;"></i> No hay pasajeros pendientes de validación en esta reserva.
                        </p>
                    @else
                        @foreach($pendingPassengers as $passenger)
                            <div style="border: 1px solid var(--border); border-radius: 6px; padding: 1rem; margin-bottom: 1rem;">
                                <div style="font-weight: 700; color: var(--navy); margin-bottom: 0.25rem;">
                                    Asiento {{ $passenger->seat_number }} - {{ $passenger->name }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">
                                    Solicita: <strong style="color: var(--navy);">{{ ucfirst($passenger->passenger_type) }} ({{ $passenger->benefit_label }})</strong>
                                </div>
                                <form action="{{ route('admin.passengers.validate', $passenger->id) }}" method="POST">
                                    @csrf
                                    <div style="margin-bottom: 0.75rem;">
                                        <input type="text" name="validation_notes" placeholder="Añadir nota opcional (ej. credencial borrosa)..." style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px; font-size: 0.85rem;">
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button type="submit" name="validation_status" value="validated" class="btn-action" style="background: #166534; padding: 0.5rem; font-size: 0.85rem; flex: 1; justify-content: center;" onclick="return confirm('¿Aprobar el descuento para este pasajero?');">
                                            <i class="fa-solid fa-check"></i> Validar
                                        </button>
                                        <button type="submit" name="validation_status" value="rejected" class="btn-action" style="background: var(--primary); padding: 0.5rem; font-size: 0.85rem; flex: 1; justify-content: center;" onclick="return confirm('¿Rechazar el descuento? El pago seguirá igual por ahora.');">
                                            <i class="fa-solid fa-xmark"></i> Rechazar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
