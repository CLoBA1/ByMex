<x-app-layout>
    @section('header-title', 'Flota de Autobuses')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-bus"></i> Nuestra Flota</h2>
            <a href="{{ route('admin.buses.create') }}" class="btn-action"><i class="fa-solid fa-plus"></i> Registrar Autobús</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Autobús</th>
                            <th>Imágenes</th>
                            <th>Estado</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buses as $bus)
                            <tr>
                                <td style="font-weight: 700; color: var(--slate-500);">#{{ $bus->id }}</td>
                                <td>
                                    <div style="font-weight: 700; color: var(--navy);">{{ $bus->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ Str::limit($bus->description, 50) }}</div>
                                </td>
                                <td>
                                    <span style="background: var(--slate-100); padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                        <i class="fa-solid fa-images" style="color: var(--primary);"></i> {{ $bus->images->count() }}
                                    </span>
                                </td>
                                <td>
                                    @if($bus->is_active)
                                        <span style="color: #166534; font-weight: 600; font-size: 0.85rem; background: #dcfce7; padding: 0.2rem 0.5rem; border-radius: 4px;">Activo</span>
                                    @else
                                        <span style="color: #991b1b; font-weight: 600; font-size: 0.85rem; background: #fee2e2; padding: 0.2rem 0.5rem; border-radius: 4px;">Inactivo</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <a href="{{ route('admin.buses.edit', $bus->id) }}" class="btn-action" style="background: var(--gold);"><i class="fa-solid fa-pen"></i></a>
                                        <form action="{{ route('admin.buses.destroy', $bus->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action" style="background: var(--primary);" onclick="return confirm('¿Seguro que deseas eliminar este autobús? Se borrarán todas sus imágenes.')"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">No hay autobuses registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
