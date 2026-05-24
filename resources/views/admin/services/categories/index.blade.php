<x-app-layout>
    @section('header-title', 'Categorías de Servicios')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0;">
            <i class="fa-solid fa-layer-group"></i> Categorías de Servicios
        </h2>
        <a href="{{ route('admin.service-categories.create') }}" class="btn-action" style="background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fa-solid fa-plus"></i> Nueva Categoría
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">ID</th>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Orden</th>
                            <th>Estatus</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td style="text-align: center; color: var(--slate-400);">{{ $category->id }}</td>
                                <td style="font-size: 1.5rem; color: var(--primary);">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }}"></i>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $category->name }}</strong><br>
                                    <small style="color: var(--slate-500);">{{ Str::limit($category->description, 50) }}</small>
                                </td>
                                <td>{{ $category->order }}</td>
                                <td>
                                    @if($category->status === 'active')
                                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Activo</span>
                                    @else
                                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Inactivo</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.service-categories.edit', $category->id) }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); padding: 0.4rem 0.6rem; border-radius: 4px; margin-right: 0.25rem;">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.service-categories.destroy', $category->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action" style="background: #fee2e2; color: #ef4444; border: none; padding: 0.4rem 0.6rem; border-radius: 4px; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; color: #cbd5e1;"></i><br>
                                    No hay categorías registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
