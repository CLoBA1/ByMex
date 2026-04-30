<x-app-layout>
    @section('header-title', 'Puntos de Abordaje')

    @if (session('success'))
        <div style="background: #dcfce7; border: 1px solid #22c55e; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">

        <!-- LISTA ACTUAL -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-map-marker-alt"></i> Catálogo de Puntos</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Color</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($boardingPoints as $bp)
                            <tr>
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background: {{ $bp->color_hex }}; border: 2px solid rgba(0,0,0,0.1);"></span>
                                        {{ $bp->color_label }}
                                    </span>
                                </td>
                                <td style="font-weight: 600;">{{ $bp->name }}</td>
                                <td>
                                    @if($bp->is_active)
                                        <span class="badge badge-green">Activo</span>
                                    @else
                                        <span class="badge" style="background: var(--slate-100); color: var(--text-muted);">Inactivo</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <button type="button" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border); padding: 0.4rem;" onclick="editBp({{ $bp->id }}, '{{ $bp->name }}', '{{ $bp->color_label }}', '{{ $bp->color_hex }}', {{ $bp->is_active ? 'true' : 'false' }}, '{{ $bp->notes }}')" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.boarding-points.destroy', $bp->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('¿Eliminar o desactivar este punto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action" style="background: var(--primary); padding: 0.4rem;" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">No hay puntos de abordaje registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FORMULARIO CREAR / EDITAR -->
        <div>
            <!-- Crear -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-plus"></i> Agregar Punto</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.boarding-points.store') }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Nombre del Punto</label>
                            <input type="text" name="name" required placeholder="Ej. Taxco" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Etiqueta de Color</label>
                                <input type="text" name="color_label" required placeholder="Ej. Rojo" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Color (Hex)</label>
                                <input type="color" name="color_hex" value="#6b7280" style="width: 100%; height: 38px; border: 1px solid var(--border); border-radius: 4px; cursor: pointer;">
                            </div>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Notas (Opcional)</label>
                            <input type="text" name="notes" placeholder="Observaciones internas..." style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                        <button type="submit" class="btn-action" style="width: 100%; justify-content: center; padding: 0.6rem;">
                            <i class="fa-solid fa-plus"></i> Agregar Punto de Abordaje
                        </button>
                    </form>
                </div>
            </div>

            <!-- Editar (oculto por defecto) -->
            <div class="card" id="editCard" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-pen"></i> Editar Punto</h3>
                </div>
                <div class="card-body">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Nombre</label>
                            <input type="text" name="name" id="editName" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Etiqueta de Color</label>
                                <input type="text" name="color_label" id="editColorLabel" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Color (Hex)</label>
                                <input type="color" name="color_hex" id="editColorHex" style="width: 100%; height: 38px; border: 1px solid var(--border); border-radius: 4px; cursor: pointer;">
                            </div>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Estado</label>
                            <select name="is_active" id="editActive" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem;">Notas</label>
                            <input type="text" name="notes" id="editNotes" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" class="btn-action" style="flex: 1; justify-content: center; padding: 0.6rem;">
                                <i class="fa-solid fa-save"></i> Guardar Cambios
                            </button>
                            <button type="button" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border); padding: 0.6rem;" onclick="document.getElementById('editCard').style.display='none';">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        function editBp(id, name, colorLabel, colorHex, isActive, notes) {
            document.getElementById('editCard').style.display = 'block';
            document.getElementById('editForm').action = '/admin/boarding-points/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editColorLabel').value = colorLabel;
            document.getElementById('editColorHex').value = colorHex;
            document.getElementById('editActive').value = isActive ? '1' : '0';
            document.getElementById('editNotes').value = notes || '';
            document.getElementById('editCard').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</x-app-layout>
