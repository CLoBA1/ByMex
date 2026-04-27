<x-app-layout>
    @section('header-title', 'Catálogo de Viajes')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-list"></i> Todos los Tours</h2>
            <a href="{{ route('admin.tours.create') }}" class="btn-action"><i class="fa-solid fa-plus"></i> Crear Nuevo Tour</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título / Destino</th>
                            <th>Salida</th>
                            <th>Precio</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tours as $tour)
                            <tr>
                                <td style="font-weight: 700; color: var(--slate-500);">#{{ $tour->id }}</td>
                                <td>
                                    <div style="font-weight: 700; color: var(--navy);">{{ $tour->title }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> {{ $tour->destination }}</div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($tour->departure_date)->format('d M Y, H:i') }}</td>
                                <td style="font-weight: 700; color: #166534;">${{ number_format($tour->price, 0) }}</td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <a href="{{ route('admin.tours.show', $tour->id) }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);"><i class="fa-solid fa-eye"></i></a>
                                        <a href="{{ route('admin.tours.edit', $tour->id) }}" class="btn-action" style="background: var(--gold);"><i class="fa-solid fa-pen"></i></a>
                                        <form action="{{ route('admin.tours.destroy', $tour->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action" style="background: var(--primary);" onclick="return confirm('¿Seguro que deseas eliminar este tour?')"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">No hay tours registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
