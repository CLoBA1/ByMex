<x-app-layout>
    @section('header-title', 'Perfil del Cliente')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0.25rem;">
                <i class="fa-solid fa-user-circle"></i> {{ $client->name }}
                @if($client->status === 'active')
                    <span style="font-size: 0.8rem; background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; vertical-align: middle; margin-left: 0.5rem;">● Activo</span>
                @else
                    <span style="font-size: 0.8rem; background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 6px; vertical-align: middle; margin-left: 0.5rem;">● Inactivo</span>
                @endif
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Miembro desde: {{ $client->created_at->translatedFormat('F Y') }}
                @if($client->membership_number) | Membresía: <span style="font-weight: 600; color: var(--primary);">{{ $client->membership_number }}</span> @endif
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            @if($client->status === 'active')
                <form action="{{ route('admin.clients.toggle', $client->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Seguro que deseas desactivar este cliente? No podrá iniciar sesión.');">
                    @csrf
                    <button type="submit" class="btn-action" style="background: #ef4444; color: #fff; border: none; cursor: pointer; padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius: 6px;">
                        <i class="fa-solid fa-user-slash"></i> Desactivar Cliente
                    </button>
                </form>
            @else
                <form action="{{ route('admin.clients.toggle', $client->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Seguro que deseas reactivar este cliente?');">
                    @csrf
                    <button type="submit" class="btn-action" style="background: #10b981; color: #fff; border: none; cursor: pointer; padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius: 6px;">
                        <i class="fa-solid fa-user-check"></i> Reactivar Cliente
                    </button>
                </form>
            @endif
            @if($client->whatsapp && $client->membership_number && $client->password && $client->temp_password)
                @php
                    $waMessage = "Hola {$client->name}, tus datos de acceso a ByMex Club son:\n🎫 Membresía: {$client->membership_number}\n📱 Usuario: {$client->whatsapp}\n🔑 Contraseña: {$client->temp_password}\n🌐 Ingresa en: https://viajesbymex.com/mi-cuenta\n¡Bienvenido al club! 🎉";
                    $waNumber = preg_replace('/[^0-9]/', '', $client->whatsapp);
                    $waLink = 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($waMessage);
                @endphp
                <a href="{{ $waLink }}" target="_blank" class="btn-action"
                   style="background: #25d366; color: #fff; border: none; text-decoration: none;">
                    <i class="fa-brands fa-whatsapp"></i> Enviar Credenciales
                </a>
            @endif
            <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn-action" style="background: var(--navy); color: #fff; border: none; text-decoration: none;">
                <i class="fa-solid fa-user-pen"></i> Editar Cliente
            </a>
            <a href="{{ route('admin.clients.index') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
                <i class="fa-solid fa-arrow-left"></i> Volver a Clientes
            </a>
        </div>

    </div>

    @if(session('success'))
        <div style="background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @php
        $totalPaid = 0;
        $totalDebt = 0;
        $delayedPayments = 0;

        foreach($client->reservations as $res) {
            $paid = $res->payments->where('status', 'approved')->sum('amount');
            $totalPaid += $paid;
            if($res->status->value !== 'cancelled') {
                $totalDebt += $res->balance_due;
                // Simple logic for delayed: if balance > 0 and expires_at is past
                if($res->balance_due > 0 && $res->expires_at && \Carbon\Carbon::parse($res->expires_at)->isPast()) {
                    $delayedPayments++;
                }
            }
        }
    @endphp

    <!-- Metrics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #eff6ff; color: #3b82f6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-suitcase-rolling"></i>
                </div>
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Viajes Activos</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--navy);">{{ $activeTrips->count() }}</div>
                </div>
            </div>
        </div>
        
        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #f0fdf4; color: #16a34a; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Total Pagado</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--navy);">${{ number_format($totalPaid, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #fef2f2; color: #ef4444; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Saldo Pendiente</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--navy);">${{ number_format($totalDebt, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #fefce8; color: #eab308; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <div style="flex-grow: 1;">
                    <div style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Programa Lealtad</div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: var(--navy);">
                        {{ $client->available_bonuses }} <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Bonos Activos</span>
                    </div>
                    
                    <!-- Barra de progreso eliminada -->
                </div>
            </div>
        </div>
    </div>

    @if($delayedPayments > 0)
        <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem 1.5rem; border-radius: 6px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Atención: Pagos Atrasados</strong><br>
                El cliente tiene {{ $delayedPayments }} reservación(es) con saldo pendiente cuya fecha límite de anticipo ya expiró.
            </div>
        </div>
    @endif

    <!-- Gestión de Bonificaciones -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-gift"></i> Gestión de Bonificaciones</h3>
        </div>
        <div class="card-body" style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            <!-- Formulario de Ajuste Manual -->
            <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 1rem;">Ajuste Manual de Bonos</h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Bonos disponibles: <strong>{{ $client->available_bonuses }}</strong></p>

                <form action="{{ route('admin.clients.bonus', $client->id) }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--navy);">Tipo de ajuste</label>
                        <select name="adjustment_type" required style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px;">
                            <option value="add">➕ Agregar bonos</option>
                            <option value="subtract">➖ Quitar bonos</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--navy);">Cantidad</label>
                        <input type="number" name="requested_bonus_count" min="1" max="100" required value="1" style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--navy);">Motivo / Nota (Opcional)</label>
                        <input type="text" name="admin_notes" placeholder="Ej: Premio especial, Corrección, etc." style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px;">
                    </div>
                    <button type="submit" class="btn-action" style="width: 100%; justify-content: center;"><i class="fa-solid fa-save"></i> Aplicar Ajuste</button>
                </form>
            </div>

            <!-- Historial de Movimientos -->
            <div>
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 1rem;">Historial de Movimientos</h4>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th style="text-align: center;">Cantidad</th>
                                <th>Motivo / Notas Admin</th>
                                <th style="text-align: center;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->bonusRequests()->orderByDesc('created_at')->get() as $br)
                                <tr>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $br->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($br->adjustment_type === 'add')
                                            <span style="color: #16a34a; font-weight: 600;"><i class="fa-solid fa-plus"></i> Agregado</span>
                                        @elseif($br->adjustment_type === 'subtract')
                                            <span style="color: #ef4444; font-weight: 600;"><i class="fa-solid fa-minus"></i> Descontado</span>
                                        @else
                                            <span style="color: #3b82f6; font-weight: 600;"><i class="fa-solid fa-trophy"></i> Por viaje / Canje</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 700;">
                                        @if($br->adjustment_type === 'subtract')
                                            <span style="color: #ef4444;">-{{ $br->requested_bonus_count }}</span>
                                        @else
                                            <span style="color: #16a34a;">+{{ $br->requested_bonus_count }}</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.85rem;">{{ $br->admin_notes ?: ($br->client_notes ?: '—') }}</td>
                                    <td style="text-align: center;">
                                        @php
                                            $bBg = '#f1f5f9'; $bCol = '#64748b'; $bTxt = ucfirst($br->status);
                                            if($br->status === 'approved') { $bBg = '#dcfce7'; $bCol = '#166534'; }
                                            elseif($br->status === 'pending') { $bBg = '#fef08a'; $bCol = '#854d0e'; }
                                            elseif($br->status === 'rejected') { $bBg = '#fee2e2'; $bCol = '#991b1b'; }
                                        @endphp
                                        <span style="background: {{ $bBg }}; color: {{ $bCol }}; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">{{ $bTxt }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted); font-style: italic;">
                                        Sin movimientos de bonificaciones aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        <!-- Contact Info -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-address-card"></i> Información de Contacto</h3>
            </div>
            <div class="card-body" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem;">Teléfono Principal</div>
                    <div style="font-weight: 600; color: var(--navy);"><i class="fa-solid fa-phone" style="color: #cbd5e1;"></i> {{ $client->phone }}</div>
                </div>
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem;">Email</div>
                    <div style="font-weight: 600; color: var(--navy);"><i class="fa-solid fa-envelope" style="color: #cbd5e1;"></i> {{ $client->email ?? 'No especificado' }}</div>
                </div>
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem;">WhatsApp</div>
                    <div style="font-weight: 600; color: var(--navy);"><i class="fa-brands fa-whatsapp" style="color: #25d366;"></i> {{ $client->whatsapp ?? 'No especificado' }}</div>
                </div>
                <div>
                    <div style="color: var(--text-muted); font-size: 0.85rem;">Ciudad de Origen</div>
                    <div style="font-weight: 600; color: var(--navy);"><i class="fa-solid fa-location-dot" style="color: #cbd5e1;"></i> {{ $client->origin_city ?? 'No especificado' }}</div>
                </div>
            </div>
        </div>

        <!-- Reservations List -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-ticket"></i> Historial de Reservaciones ({{ $client->reservations->count() }})</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Folio / Fecha</th>
                                <th>Tour</th>
                                <th style="text-align: right;">Total</th>
                                <th style="text-align: right;">Abonado</th>
                                <th style="text-align: right;">Saldo Pendiente</th>
                                <th style="text-align: center;">Estado</th>
                                <th style="text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->reservations->sortByDesc('created_at') as $res)
                                @php
                                    $paid = $res->payments->where('status', 'approved')->sum('amount');
                                    
                                    // Status styling
                                    if ($res->status->value === 'paid') {
                                        $badgeBg = '#dcfce7'; $badgeColor = '#166534'; $badgeLabel = 'Pagado'; $icon = 'fa-check';
                                    } elseif ($res->status->value === 'partial') {
                                        $badgeBg = '#fef08a'; $badgeColor = '#854d0e'; $badgeLabel = 'Anticipo'; $icon = 'fa-star-half-stroke';
                                    } elseif ($res->status->value === 'cancelled') {
                                        $badgeBg = '#f1f5f9'; $badgeColor = '#64748b'; $badgeLabel = 'Cancelado'; $icon = 'fa-xmark';
                                    } elseif ($res->status->value === 'expired') {
                                        $badgeBg = '#e2e8f0'; $badgeColor = '#475569'; $badgeLabel = 'Expirado'; $icon = 'fa-hourglass-end';
                                    } else {
                                        $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; $badgeLabel = 'Pendiente'; $icon = 'fa-clock';
                                    }
                                @endphp
                                <tr style="{{ $res->status->value === 'cancelled' ? 'opacity: 0.6;' : '' }}">
                                    <td>
                                        <strong style="color: var(--navy);">RES-{{ str_pad($res->id, 4, '0', STR_PAD_LEFT) }}</strong><br>
                                        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $res->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $res->tour->title }}</div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-calendar-days"></i> {{ \Carbon\Carbon::parse($res->tour->departure_date)->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td style="text-align: right; font-weight: 600;">${{ number_format($res->total_amount, 2) }}</td>
                                    <td style="text-align: right; color: #166534;">${{ number_format($paid, 2) }}</td>
                                    <td style="text-align: right; color: {{ $res->balance_due > 0 ? '#991b1b' : 'var(--text-muted)' }}; font-weight: 600;">
                                        ${{ number_format($res->balance_due, 2) }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; display: inline-block;">
                                            <i class="fa-solid {{ $icon }}"></i> {{ $badgeLabel }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.reservations.show', $res->id) }}" class="btn-action" style="padding: 0.35rem 0.75rem; font-size: 0.85rem; background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
                                            Ver Detalle <i class="fa-solid fa-chevron-right" style="margin-left: 0.25rem;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                        El cliente no tiene reservaciones asociadas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
