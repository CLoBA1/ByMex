<x-app-layout>
    @section('header-title', 'Saldos a Favor')

    @section('extra-css')
    <style>
        .surplus-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .surplus-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid var(--border);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .surplus-card .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .surplus-card .card-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--navy);
            line-height: 1.2;
        }
        .surplus-card .card-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .filters-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .filters-bar input,
        .filters-bar select {
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            font-size: 0.9rem;
        }
        .filters-bar input:focus,
        .filters-bar select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(214, 40, 40, 0.1);
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th, .data-table td {
            padding: 1rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .data-table th {
            background: #f8fafc;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        .data-table tr:hover {
            background: #f8fafc;
        }
        .surplus-amount {
            font-weight: 700;
            color: #059669;
            background: #ecfdf5;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            font-size: 0.9rem;
            display: inline-block;
        }
        .badge-status {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .btn-view {
            background: var(--slate-100, #f1f5f9);
            color: var(--navy);
            border: 1px solid var(--border);
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.2s;
        }
        .btn-view:hover {
            background: #e2e8f0;
        }
        .btn-filter {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.6rem 1.25rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
        }
        .btn-filter:hover {
            background: #b51c1c;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            color: #cbd5e1;
        }
    </style>
    @endsection

    {{-- CARDS RESUMEN --}}
    <div class="surplus-cards">
        <div class="surplus-card">
            <div class="icon-box" style="background: #ecfdf5; color: #059669;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="card-value">{{ $totalCount }}</div>
                <div class="card-label">Reservaciones con Saldo a Favor</div>
            </div>
        </div>
        <div class="surplus-card">
            <div class="icon-box" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <div class="card-value">${{ number_format($totalSurplus, 2) }}</div>
                <div class="card-label">Monto Total Pendiente de Resolver</div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('admin.reservations.surplus') }}" class="filters-bar">
        <input type="text" name="search" placeholder="Buscar por folio, nombre o teléfono..." value="{{ request('search') }}" style="flex: 1; min-width: 200px;">
        <select name="status">
            <option value="">Todos los Estados</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Anticipo</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagada</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirada</option>
        </select>
        <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filtrar</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.reservations.surplus') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">
                <i class="fa-solid fa-xmark"></i> Limpiar
            </a>
        @endif
    </form>

    {{-- TABLA DE RESULTADOS --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-hand-holding-dollar"></i> Reservaciones con Excedente Pendiente</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Tour</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Total</th>
                        <th style="text-align: right;">Pagado</th>
                        <th style="text-align: right;">Saldo a Favor</th>
                        <th>Actualización</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $r)
                        @php
                            $paid = (float) ($r->amount_paid ?? 0);
                            $total = (float) $r->total_amount;
                            $adjustments = (float) ($r->adjustments_total ?? 0);
                            $surplusBruto = max(0, $paid - $total);
                            $availableSurplus = max(0, $surplusBruto - $adjustments);

                            $statusLabels = [
                                'pending'   => ['Pendiente', '#fef3c7', '#92400e'],
                                'partial'   => ['Anticipo',  '#fef08a', '#854d0e'],
                                'paid'      => ['Pagada',    '#dcfce7', '#166534'],
                                'cancelled' => ['Cancelada', '#f1f5f9', '#64748b'],
                                'expired'   => ['Expirada',  '#e2e8f0', '#475569'],
                            ];
                            $st = $statusLabels[$r->status->value] ?? ['—', '#f1f5f9', '#64748b'];
                        @endphp
                        <tr>
                            <td style="font-weight: 700; color: var(--navy);">RES-{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $r->tour->title ?? '—' }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $r->client->name ?? '—' }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $r->client->phone ?? '' }}</div>
                            </td>
                            <td>
                                <span class="badge-status" style="background: {{ $st[1] }}; color: {{ $st[2] }};">
                                    {{ $st[0] }}
                                </span>
                            </td>
                            <td style="text-align: right;">${{ number_format($total, 2) }}</td>
                            <td style="text-align: right; font-weight: 600;">${{ number_format($paid, 2) }}</td>
                            <td style="text-align: right;">
                                <span class="surplus-amount">${{ number_format($availableSurplus, 2) }}</span>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $r->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.reservations.show', $r->id) }}" class="btn-view">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <h3>Sin saldos a favor pendientes</h3>
                                    <p>No hay reservaciones con excedente por resolver en este momento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 1.5rem; padding: 1rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; color: #92400e; font-size: 0.85rem;">
        <i class="fa-solid fa-circle-info"></i>
        <strong>Nota:</strong> Este listado es informativo. Para gestionar el saldo a favor de cada reservación (devolución, penalización o nota), ingresa al detalle con el botón "Ver".
    </div>

</x-app-layout>
