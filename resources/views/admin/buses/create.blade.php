<x-app-layout>
    @section('header-title', 'Registrar Autobús')

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-plus"></i> Nuevo Autobús</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.buses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre o Modelo *</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required placeholder="Ej. Irizar I8, Volvo 9800...">
                    @error('name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="description" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción Corta</label>
                    <textarea id="description" name="description" class="form-input" rows="3" placeholder="Ej. Asientos reclinables, clima, pantallas HD y sanitarios.">{{ old('description') }}</textarea>
                    @error('description') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="images" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Imágenes del Autobús</label>
                    <input type="file" id="images" name="images[]" class="form-input" multiple accept="image/*">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Puedes seleccionar varias imágenes al mismo tiempo (jpg, png, webp).</p>
                    @error('images.*') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked style="width: 1.2rem; height: 1.2rem;">
                        <span style="font-weight: 600;">Autobús Activo (Visible en público)</span>
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="{{ route('admin.buses.index') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border); text-decoration: none;">Cancelar</a>
                    <button type="submit" class="btn-action" style="background: var(--navy);"><i class="fa-solid fa-save"></i> Guardar Autobús</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
