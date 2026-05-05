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
            @php
                $amountPaid = $reservation->payments->where('status', 'approved')->sum('amount');
                $surplus = $amountPaid > $reservation->total_amount ? $amountPaid - $reservation->total_amount : 0;
            @endphp
            
            @if($reservation->status->value == 'pending')
                <span class="badge badge-orange" style="font-size: 1rem;"><i class="fa-regular fa-clock"></i> Pendiente</span>
            @elseif($reservation->status->value == 'partial')
                @if($surplus > 0)
                    <span class="badge" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; font-size: 1rem;"><i class="fa-solid fa-hand-holding-dollar"></i> Pagada con Excedente</span>
                @else
                    <span class="badge" style="background: #fef08a; color: #854d0e; border: 1px solid #fde047; font-size: 1rem;"><i class="fa-solid fa-star-half-stroke"></i> Anticipo</span>
                @endif
            @elseif($reservation->status->value == 'paid')
                @if($surplus > 0)
                    <span class="badge" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; font-size: 1rem;"><i class="fa-solid fa-hand-holding-dollar"></i> Pagada con Excedente</span>
                @else
                    <span class="badge badge-green" style="font-size: 1rem;"><i class="fa-solid fa-check"></i> Pagada</span>
                @endif
            @elseif($reservation->status->value == 'expired')
                <span class="badge" style="background: #e2e8f0; color: #475569; font-size: 1rem;"><i class="fa-solid fa-hourglass-end"></i> Expirada</span>
            @else
                @if($surplus > 0)
                    <span class="badge" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; font-size: 1rem;"><i class="fa-solid fa-hand-holding-dollar"></i> Saldo a Favor</span>
                @else
                    <span class="badge" style="background: var(--slate-100); color: var(--text-muted); font-size: 1rem;"><i class="fa-solid fa-xmark"></i> Cancelada</span>
                @endif
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
                                <th>Abordaje</th>
                                <th>Estatus Validación</th>
                                <th>Estado Operativo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservation->passengers as $passenger)
                                <tr style="{{ $passenger->status->value == 'cancelled' ? 'opacity: 0.6; background-color: #f8fafc;' : '' }}">
                                    <td style="font-weight: 700; color: var(--navy); {{ $passenger->status->value == 'cancelled' ? 'text-decoration: line-through;' : '' }}">{{ $passenger->seat_number }}</td>
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
                                        @if($passenger->boardingPoint)
                                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.85rem;">
                                                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: {{ $passenger->boardingPoint->color_hex }};"></span>
                                                {{ $passenger->boardingPoint->name }}
                                            </span>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 0.85rem;">—</span>
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
                                    <td>
                                        @if($passenger->status->value == 'active')
                                            <span class="badge badge-green">Activo</span>
                                        @elseif($passenger->status->value == 'cancelled')
                                            <span class="badge" style="background: var(--slate-100); color: var(--text-muted);">Cancelado</span>
                                        @elseif($passenger->status->value == 'no_show')
                                            <span class="badge" style="background: #fef08a; color: #854d0e;">No Show</span>
                                        @elseif($passenger->status->value == 'boarded')
                                            <span class="badge" style="background: #e0e7ff; color: #3730a3;">Abordó</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($passenger->status->value != 'cancelled')
                                            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                                <form action="{{ route('admin.passengers.status', $passenger->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="boarded">
                                                    <button type="submit" class="btn-action" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #e0e7ff; color: #3730a3;" title="Marcar como Abordó">
                                                        <i class="fa-solid fa-person-walking-luggage"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.passengers.status', $passenger->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="no_show">
                                                    <button type="submit" class="btn-action" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #fef08a; color: #854d0e;" title="Marcar No Show">
                                                        <i class="fa-solid fa-user-slash"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.passengers.status', $passenger->id) }}" method="POST" style="display: inline;" onsubmit="
                                                    let reason = prompt('Motivo de cancelación para {{ $passenger->name }}:');
                                                    if(reason === null) return false;
                                                    this.action_notes.value = reason;
                                                    return true;
                                                ">
                                                    @csrf
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <input type="hidden" name="action_notes" value="">
                                                    <button type="submit" class="btn-action" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: var(--primary);" title="Cancelar Pasajero">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.passengers.type', $passenger->id) }}" method="POST" style="display: flex; gap: 0.25rem;" onsubmit="return confirm('¿Cambiar tipo de pasajero? Esto recalculará la reserva.');">
                                                    @csrf
                                                    <select name="passenger_type" style="padding: 0.25rem; font-size: 0.75rem; border: 1px solid var(--border); border-radius: 4px;" required>
                                                        <option value="Adulto" {{ $passenger->passenger_type == 'Adulto' ? 'selected' : '' }}>Adulto</option>
                                                        <option value="Niño" {{ $passenger->passenger_type == 'Niño' ? 'selected' : '' }}>Niño</option>
                                                        <option value="Adulto Mayor" {{ $passenger->passenger_type == 'Adulto Mayor' ? 'selected' : '' }}>Adulto Mayor</option>
                                                    </select>
                                                    <button type="submit" class="btn-action" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: var(--slate-600);">
                                                        <i class="fa-solid fa-save"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            @if($passenger->action_notes)
                                                <div style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">
                                                    Motivo: {{ $passenger->action_notes }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">Esta reservación legacy no tiene pasajeros desglosados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- DOCUMENTOS POR PASAJERO --}}
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-folder-open"></i> Documentos por Pasajero</h3>
                </div>
                <div class="card-body">
                    @forelse($reservation->passengers as $passenger)
                        <div style="border: 1px solid var(--border); border-radius: 6px; padding: 1rem; margin-bottom: 1rem; {{ $passenger->status->value == 'cancelled' ? 'opacity: 0.5;' : '' }}">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <div>
                                    <strong style="color: var(--navy);">Asiento {{ $passenger->seat_number }} — {{ $passenger->name }}</strong>
                                    <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: 0.5rem;">{{ ucfirst($passenger->passenger_type) }}</span>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $passenger->documents->count() }} archivo(s)</span>
                            </div>

                            {{-- Archivos existentes --}}
                            @if($passenger->documents->count() > 0)
                                <div style="margin-bottom: 0.75rem;">
                                    @foreach($passenger->documents as $doc)
                                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.4rem 0.6rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 0.35rem; font-size: 0.8rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0; flex: 1;">
                                                @if(str_contains($doc->mime_type ?? '', 'pdf'))
                                                    <i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i>
                                                @else
                                                    <i class="fa-solid fa-file-image" style="color: #3b82f6;"></i>
                                                @endif
                                                <a href="{{ route('admin.documents.download', $doc->id) }}" target="_blank" style="color: var(--navy); text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $doc->original_name }}">
                                                    {{ $doc->original_name }}
                                                </a>
                                                @if($doc->file_size)
                                                    <span style="color: var(--text-muted); flex-shrink: 0;">{{ number_format($doc->file_size / 1024, 0) }} KB</span>
                                                @endif
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; margin-left: 0.5rem;">
                                                <span style="color: var(--text-muted);">{{ $doc->created_at->format('d/m/y') }}</span>
                                                <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este documento?');" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0.2rem;" title="Eliminar">
                                                        <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Formulario de subida --}}
                            @if($passenger->status->value != 'cancelled')
                                <form action="{{ route('admin.passengers.document.upload', $passenger->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 0.5rem; align-items: center;">
                                    @csrf
                                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required style="font-size: 0.8rem; flex: 1; padding: 0.3rem; border: 1px solid var(--border); border-radius: 4px;">
                                    <button type="submit" class="btn-action" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: var(--navy); border: none;">
                                        <i class="fa-solid fa-upload"></i> Subir
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p style="color: var(--text-muted); text-align: center;">No hay pasajeros en esta reservación.</p>
                    @endforelse
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
                        <span style="color: var(--slate-600);">Pagado Acumulado:</span>
                        <span style="font-weight: 600;">${{ number_format($amountPaid, 2) }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: var(--slate-600);">Balance Pendiente:</span>
                        <span style="font-weight: 600; color: {{ $reservation->balance_due > 0 ? 'var(--primary)' : 'var(--text-muted)' }};">
                            ${{ number_format($reservation->balance_due, 2) }}
                        </span>
                    </div>

                    @if($surplus > 0)
                    @php
                        $adjustmentsTotal = $reservation->adjustments->sum('amount');
                        $availableSurplus = max(0, $surplus - $adjustmentsTotal);
                    @endphp
                    <div style="padding-top: 0.5rem; margin-top: 0.5rem; border-top: 1px dashed var(--border);">
                        <div style="display: flex; justify-content: space-between; color: #059669; margin-bottom: 0.25rem;">
                            <span style="font-weight: 600;">Excedente Bruto:</span>
                            <span style="font-weight: 700;">${{ number_format($surplus, 2) }}</span>
                        </div>
                        @if($adjustmentsTotal > 0)
                        <div style="display: flex; justify-content: space-between; color: var(--slate-500); margin-bottom: 0.25rem; font-size: 0.85rem;">
                            <span>Ajustes Registrados:</span>
                            <span>-${{ number_format($adjustmentsTotal, 2) }}</span>
                        </div>
                        @endif
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.05rem; color: {{ $availableSurplus > 0 ? '#059669' : 'var(--text-muted)' }};">
                            <span>Saldo a Favor Disponible:</span>
                            <span>${{ number_format($availableSurplus, 2) }}</span>
                        </div>
                    </div>

                    {{-- Formulario de Resolución --}}
                    @if($availableSurplus > 0)
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px dashed var(--border);">
                        <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--navy); margin-bottom: 1rem;">Resolución de Saldo a Favor</h4>
                        <form action="{{ route('admin.reservations.adjustment', $reservation->id) }}" method="POST" onsubmit="return confirm('¿Registrar este movimiento?');">
                            @csrf
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <select name="type" id="adj_type" required style="padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px; font-size: 0.85rem;" onchange="document.getElementById('adj_amount_wrap').style.display = this.value === 'note' ? 'none' : 'block';">
                                    <option value="refund">Devolución Manual</option>
                                    <option value="penalty">Penalización</option>
                                    <option value="note">Solo Nota / Observación</option>
                                </select>
                                <div id="adj_amount_wrap" style="position: relative; flex: 1;">
                                    <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">$</span>
                                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $availableSurplus }}" placeholder="Monto" style="width: 100%; padding: 0.5rem 0.5rem 0.5rem 1.5rem; border: 1px solid var(--border); border-radius: 4px; font-size: 0.85rem;">
                                </div>
                            </div>
                            <div style="margin-bottom: 0.75rem;">
                                <input type="text" name="notes" placeholder="Motivo o nota (ej. Devuelto en efectivo, Penalización por no show...)" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px; font-size: 0.85rem;">
                            </div>
                            <button type="submit" class="btn-action" style="background: var(--navy); border: none; width: 100%; justify-content: center;">
                                <i class="fa-solid fa-receipt"></i> Registrar Movimiento
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Historial de Ajustes --}}
                    @if($reservation->adjustments->count() > 0)
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--slate-500); margin-bottom: 0.75rem;">Historial de Ajustes</h4>
                        @foreach($reservation->adjustments->sortByDesc('created_at') as $adj)
                        <div style="padding: 0.5rem 0.75rem; margin-bottom: 0.5rem; border-radius: 4px; font-size: 0.8rem; background: {{ $adj->type === 'refund' ? '#f0fdf4' : ($adj->type === 'penalty' ? '#fff7ed' : '#f8fafc') }}; border-left: 3px solid {{ $adj->type === 'refund' ? '#059669' : ($adj->type === 'penalty' ? '#ea580c' : '#94a3b8') }};">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 600; color: {{ $adj->type === 'refund' ? '#059669' : ($adj->type === 'penalty' ? '#ea580c' : '#475569') }};">
                                    @if($adj->type === 'refund') <i class="fa-solid fa-arrow-rotate-left"></i> Devolución
                                    @elseif($adj->type === 'penalty') <i class="fa-solid fa-gavel"></i> Penalización
                                    @else <i class="fa-regular fa-note-sticky"></i> Nota
                                    @endif
                                </span>
                                @if($adj->amount > 0)
                                <span style="font-weight: 700;">${{ number_format($adj->amount, 2) }}</span>
                                @endif
                            </div>
                            @if($adj->notes)
                            <div style="color: var(--slate-500); margin-top: 0.2rem;">{{ $adj->notes }}</div>
                            @endif
                            <div style="color: var(--slate-400); margin-top: 0.2rem; font-size: 0.75rem;">
                                {{ $adj->created_at->format('d/m/Y H:i') }} — {{ $adj->user ? $adj->user->name : 'Sistema' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @endif

                    @if(in_array($reservation->status->value, ['pending', 'partial']) && $reservation->balance_due > 0)
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px dashed var(--border);">
                            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--navy); margin-bottom: 1rem;">Registrar Abono Manual</h4>
                            
                            <!-- Formulario de Abono Parcial/Total -->
                            <form action="{{ route('admin.reservations.payment', $reservation->id) }}" method="POST" style="display: flex; gap: 0.5rem; align-items: stretch; margin-bottom: 1rem;">
                                @csrf
                                <div style="position: relative; flex: 1;">
                                    <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">$</span>
                                    <input type="number" name="amount" step="0.01" min="1" max="{{ $reservation->balance_due }}" required placeholder="Monto a abonar" style="width: 100%; padding: 0.5rem 0.5rem 0.5rem 1.5rem; border: 1px solid var(--border); border-radius: 4px; font-size: 0.9rem;">
                                </div>
                                <button type="submit" class="btn-action" style="background: var(--navy); border: none;" onclick="return confirm('¿Registrar este abono manual?')">
                                    <i class="fa-solid fa-plus"></i> Abonar
                                </button>
                            </form>

                            <!-- Botón Liquidar Rápido -->
                            <form action="{{ route('admin.reservations.payment', $reservation->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $reservation->balance_due }}">
                                <button type="submit" class="btn-action" style="background: #166534; width: 100%; justify-content: center; border: none;" onclick="return confirm('¿Confirmar liquidación completa del saldo por ${{ number_format($reservation->balance_due, 2) }}?')">
                                    <i class="fa-solid fa-money-bill-wave"></i> Liquidar Saldo Completo
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- HISTORIAL COMBINADO DE MOVIMIENTOS --}}
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Historial de Movimientos</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    @php
                        // Combinar pagos y ajustes en una sola línea de tiempo
                        $timeline = collect();

                        foreach ($reservation->payments as $pay) {
                            $timeline->push([
                                'date'   => $pay->created_at,
                                'kind'   => 'payment',
                                'amount' => (float) $pay->amount,
                                'status' => $pay->status,
                                'notes'  => $pay->proof_image ? 'Comprobante adjunto' : null,
                                'user'   => $pay->approvedBy ? $pay->approvedBy->name : null,
                            ]);
                        }

                        foreach ($reservation->adjustments as $adj) {
                            $timeline->push([
                                'date'   => $adj->created_at,
                                'kind'   => 'adjustment',
                                'type'   => $adj->type,
                                'amount' => (float) $adj->amount,
                                'status' => null,
                                'notes'  => $adj->notes,
                                'user'   => $adj->user ? $adj->user->name : 'Sistema',
                            ]);
                        }

                        $timeline = $timeline->sortByDesc('date')->values();
                    @endphp

                    @if($timeline->isEmpty())
                        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            <i class="fa-regular fa-folder-open" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: #cbd5e1;"></i>
                            No hay movimientos registrados en esta reservación.
                        </div>
                    @else
                        <table class="data-table" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th style="width: 140px;">Fecha</th>
                                    <th>Tipo</th>
                                    <th style="text-align: right;">Monto</th>
                                    <th>Estado</th>
                                    <th>Nota / Referencia</th>
                                    <th>Operador</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timeline as $mov)
                                    @php
                                        // Estilos por tipo
                                        if ($mov['kind'] === 'payment') {
                                            $icon = 'fa-solid fa-circle-dollar-to-slot';
                                            $label = 'Pago';
                                            if ($mov['status'] === 'approved') {
                                                $iconColor = '#166534';
                                                $rowBg = '';
                                                $badgeBg = '#dcfce7'; $badgeColor = '#166534'; $badgeText = 'Aprobado';
                                            } elseif ($mov['status'] === 'pending') {
                                                $iconColor = '#d97706';
                                                $rowBg = '';
                                                $badgeBg = '#fef3c7'; $badgeColor = '#92400e'; $badgeText = 'Pendiente';
                                            } else {
                                                $iconColor = '#ef4444';
                                                $rowBg = 'opacity: 0.6;';
                                                $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; $badgeText = ucfirst($mov['status'] ?? 'Rechazado');
                                            }
                                            $amountColor = '#166534';
                                            $amountPrefix = '+';
                                        } else {
                                            // adjustment
                                            $subType = $mov['type'] ?? 'note';
                                            if ($subType === 'refund') {
                                                $icon = 'fa-solid fa-arrow-rotate-left';
                                                $label = 'Devolución';
                                                $iconColor = '#059669';
                                                $amountColor = '#059669';
                                                $amountPrefix = '-';
                                            } elseif ($subType === 'penalty') {
                                                $icon = 'fa-solid fa-gavel';
                                                $label = 'Penalización';
                                                $iconColor = '#ea580c';
                                                $amountColor = '#ea580c';
                                                $amountPrefix = '-';
                                            } else {
                                                $icon = 'fa-regular fa-note-sticky';
                                                $label = 'Nota';
                                                $iconColor = '#64748b';
                                                $amountColor = '#64748b';
                                                $amountPrefix = '';
                                            }
                                            $rowBg = '';
                                            $badgeBg = ''; $badgeColor = ''; $badgeText = '';
                                        }
                                    @endphp
                                    <tr style="{{ $rowBg }}">
                                        <td style="white-space: nowrap; color: var(--text-muted);">
                                            {{ $mov['date']->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <span style="color: {{ $iconColor }}; font-weight: 600;">
                                                <i class="{{ $icon }}"></i> {{ $label }}
                                            </span>
                                        </td>
                                        <td style="text-align: right; font-weight: 700; color: {{ $amountColor }};">
                                            @if($mov['amount'] > 0)
                                                {{ $amountPrefix }}${{ number_format($mov['amount'], 2) }}
                                            @else
                                                <span style="color: var(--text-muted);">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($badgeText))
                                                <span style="display: inline-block; padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                                                    {{ $badgeText }}
                                                </span>
                                            @else
                                                <span style="color: var(--text-muted);">—</span>
                                            @endif
                                        </td>
                                        <td style="color: var(--slate-500); font-size: 0.8rem;">
                                            {{ $mov['notes'] ?? '—' }}
                                        </td>
                                        <td style="color: var(--text-muted); font-size: 0.8rem;">
                                            {{ $mov['user'] ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
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
