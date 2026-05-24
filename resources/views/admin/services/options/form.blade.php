<x-app-layout>
    @section('header-title', isset($serviceOption) ? 'Editar Opción' : 'Nueva Opción')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.8rem; color: var(--navy); font-weight: 800; margin-bottom: 0;">
            <i class="fa-solid fa-{{ isset($serviceOption) ? 'pen' : 'plus' }}"></i> 
            {{ isset($serviceOption) ? 'Editar Opción' : 'Nueva Opción' }}
        </h2>
        <a href="{{ route('admin.service-options.index') }}" class="btn-action" style="background: var(--slate-200); color: var(--slate-700); padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
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
            <form action="{{ isset($serviceOption) ? route('admin.service-options.update', $serviceOption->id) : route('admin.service-options.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($serviceOption))
                    @method('PUT')
                @endif

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Categoría</label>
                    <select name="service_category_id" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('service_category_id', $serviceOption->service_category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Nombre de la Opción</label>
                    <input type="text" name="name" value="{{ old('name', $serviceOption->name ?? '') }}" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Descripción Corta</label>
                    <textarea name="description" rows="3" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">{{ old('description', $serviceOption->description ?? '') }}</textarea>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Mensaje de WhatsApp pre-armado</label>
                    <textarea name="whatsapp_message" rows="4" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;" placeholder="Ej: Hola, me interesa el destino Argentina, quisiera recibir más información sobre fechas y precios.">{{ old('whatsapp_message', $serviceOption->whatsapp_message ?? '') }}</textarea>
                    <small style="color: var(--text-muted); display: block; margin-top: .25rem;">Este es el mensaje que le aparecerá al cliente en su WhatsApp listo para enviar cuando haga clic en el botón de esta opción.</small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Imagen (Opcional)</label>
                    @if(isset($serviceOption) && $serviceOption->image)
                        <div style="margin-bottom: 1rem;">
                            <img src="{{ Storage::url($serviceOption->image) }}" alt="Preview" style="height: 100px; border-radius: 6px; border: 1px solid var(--border);">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px; background: white;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Orden de visualización</label>
                        <input type="number" name="order" value="{{ old('order', $serviceOption->order ?? 0) }}" min="0" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                        <small style="color: var(--text-muted);">Los números menores se muestran primero.</small>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Estatus</label>
                        <select name="status" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                            <option value="active" {{ old('status', $serviceOption->status ?? 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status', $serviceOption->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div style="text-align: right; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    <button type="submit" style="background: var(--primary); color: white; border: none; padding: 0.75rem 2rem; border-radius: 6px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-save"></i> Guardar Opción
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
