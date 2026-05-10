<x-app-layout>
    @section('header-title', 'Solicitudes de Bonificaciones')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0;">
            <i class="fa-solid fa-gift"></i> Solicitudes de Bonificaciones
        </h2>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Tipo Solicitud</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th style="width: 25%;">Notas del Cliente</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            @php
                                $badgeBg = '#f1f5f9'; $badgeColor = '#64748b'; $badgeLabel = 'Desconocido';
                                if ($req->status === 'pending') {
                                    $badgeBg = '#fef08a'; $badgeColor = '#854d0e'; $badgeLabel = 'Pendiente';
                                } elseif ($req->status === 'approved') {
                                    $badgeBg = '#dcfce7'; $badgeColor = '#166534'; $badgeLabel = 'Aprobada';
                                } elseif ($req->status === 'rejected') {
                                    $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; $badgeLabel = 'Rechazada';
                                } elseif ($req->status === 'applied') {
                                    $badgeBg = '#eff6ff'; $badgeColor = '#1e3a8a'; $badgeLabel = 'Aplicada (Consumida)';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--navy);">{{ $req->client->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-phone"></i> {{ $req->client->phone }}</div>
                                </td>
                                <td>
                                    <strong>{{ $req->request_type }}</strong><br>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">Cant: {{ $req->requested_bonus_count }}</span>
                                </td>
                                <td>
                                    {{ $req->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; display: inline-block;">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $req->client_notes ?? 'Sin comentarios' }}
                                </td>
                                <td style="text-align: center;">
                                    <form action="{{ route('admin.bonus-requests.update-status', $req->id) }}" method="POST" style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                        @csrf
                                        <select name="status" class="form-control" style="width: auto; padding: 0.25rem 0.5rem; font-size: 0.85rem; display: inline-block;">
                                            <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="approved" {{ $req->status == 'approved' ? 'selected' : '' }}>Aprobar</option>
                                            <option value="rejected" {{ $req->status == 'rejected' ? 'selected' : '' }}>Rechazar</option>
                                            <option value="applied" {{ $req->status == 'applied' ? 'selected' : '' }}>Marcar Aplicada</option>
                                        </select>
                                        <button type="submit" class="btn-action" style="background: var(--navy); color: white; border: none; padding: 0.35rem 0.5rem;" title="Guardar Estado">
                                            <i class="fa-solid fa-save"></i>
                                        </button>
                                    </form>
                                    @if($req->admin_notes)
                                        <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-muted); text-align: left;">
                                            <strong>Nota Admin:</strong> {{ $req->admin_notes }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; color: #cbd5e1;"></i><br>
                                    No hay solicitudes de bonificaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($requests, 'links'))
                <div style="padding: 1rem;">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
