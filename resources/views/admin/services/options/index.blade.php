<x-app-layout>
    @section('header-title', 'Opciones de Servicios')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0;">
            <i class="fa-solid fa-list-check"></i> Opciones de Servicios
        </h2>
        <a href="{{ route('admin.service-options.create') }}" class="btn-action" style="background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fa-solid fa-plus"></i> Nueva Opción
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
                            <th>Imagen</th>
                            <th>Opción / Categoría</th>
                            <th>Mensaje WhatsApp</th>
                            <th>Orden</th>
                            <th>Estatus</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($options as $option)
                            <tr>
                                <td style="text-align: center; color: var(--slate-400);">{{ $option->id }}</td>
                                <td>
                                    @if($option->image)
                                        <img src="{{ Storage::url($option->image) }}" alt="{{ $option->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                    @else
                                        <div style="width: 60px; height: 60px; background: var(--slate-100); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--slate-400);">
                                            <i class="fa-regular fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $option->name }}</strong><br>
                                    <span style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">{{ $option->category->name ?? 'Sin categoría' }}</span><br>
                                    <small style="color: var(--slate-500);">{{ Str::limit($option->description, 50) }}</small>
                                </td>
                                <td>
                                    <small style="color: var(--text-muted); display: block; max-width: 250px;">
                                        {{ Str::limit($option->whatsapp_message, 80) }}
                                    </small>
                                </td>
                                <td>{{ $option->order }}</td>
                                <td>
                                    @if($option->status === 'active')
                                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Activo</span>
                                    @else
                                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Inactivo</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.service-options.edit', $option->id) }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); padding: 0.4rem 0.6rem; border-radius: 4px; margin-right: 0.25rem;">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.service-options.destroy', $option->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Seguro que deseas eliminar esta opción?');">
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
                                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; color: #cbd5e1;"></i><br>
                                    No hay opciones de servicios registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
