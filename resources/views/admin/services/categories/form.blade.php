<x-app-layout>
    @section('header-title', isset($serviceCategory) ? 'Editar Categoría' : 'Nueva Categoría')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0;">
            <i class="fa-solid fa-{{ isset($serviceCategory) ? 'pen' : 'plus' }}"></i> 
            {{ isset($serviceCategory) ? 'Editar Categoría' : 'Nueva Categoría' }}
        </h2>
        <a href="{{ route('admin.service-categories.index') }}" class="btn-action" style="background: var(--slate-200); color: var(--slate-700); padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    @if($errors->any())
        <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="max-width: 800px;">
        <div class="card-body">
            <form action="{{ isset($serviceCategory) ? route('admin.service-categories.update', $serviceCategory->id) : route('admin.service-categories.store') }}" method="POST">
                @csrf
                @if(isset($serviceCategory))
                    @method('PUT')
                @endif

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Nombre de la Categoría</label>
                    <input type="text" name="name" value="{{ old('name', $serviceCategory->name ?? '') }}" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Descripción</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">{{ old('description', $serviceCategory->description ?? '') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Ícono (Clase de FontAwesome)</label>
                        <input type="text" name="icon" value="{{ old('icon', $serviceCategory->icon ?? '') }}" placeholder="Ej: fa-solid fa-plane" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                        <small style="color: var(--text-muted);">Puedes buscar íconos en <a href="https://fontawesome.com/icons" target="_blank" style="color: var(--primary);">FontAwesome</a>.</small>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Orden de visualización</label>
                        <input type="number" name="order" value="{{ old('order', $serviceCategory->order ?? 0) }}" min="0" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                        <small style="color: var(--text-muted);">Los números menores se muestran primero.</small>
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Estatus</label>
                    <select name="status" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                        <option value="active" {{ old('status', $serviceCategory->status ?? 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="inactive" {{ old('status', $serviceCategory->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div style="text-align: right; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    <button type="submit" style="background: var(--primary); color: white; border: none; padding: 0.75rem 2rem; border-radius: 6px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-save"></i> Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
