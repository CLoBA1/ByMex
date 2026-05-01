<x-app-layout>
    @section('header-title', isset($tour) ? 'Editar Tour' : 'Crear Nuevo Tour')

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title">{{ isset($tour) ? 'Editar: '.$tour->title : 'Información del Tour' }}</h2>
            <a href="{{ route('admin.tours.index') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">Cancelar</a>
        </div>
        <div class="card-body">
            <form action="{{ isset($tour) ? route('admin.tours.update', $tour->id) : route('admin.tours.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($tour)) @method('PUT') @endif

                @if ($errors->any())
                    <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Título del Viaje</label>
                        <input type="text" name="title" value="{{ old('title', $tour->title ?? '') }}" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Destino Principal</label>
                        <input type="text" name="destination" value="{{ old('destination', $tour->destination ?? '') }}" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Fecha y Hora de Salida</label>
                        <input type="datetime-local" name="departure_date" value="{{ old('departure_date', isset($tour) ? date('Y-m-d\TH:i', strtotime($tour->departure_date)) : '') }}" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Precio (MXN)</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $tour->price ?? '') }}" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Total Asientos</label>
                        <input type="number" name="total_seats" value="{{ old('total_seats', $tour->total_seats ?? 40) }}" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Estatus</label>
                        <select name="status" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                            <option value="active" {{ old('status', $tour->status->value ?? 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status', $tour->status->value ?? '') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            <option value="completed" {{ old('status', $tour->status->value ?? '') == 'completed' ? 'selected' : '' }}>Completado</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Horas previas para expiración</label>
                        <input type="number" name="expiration_hours" value="{{ old('expiration_hours', $tour->expiration_hours ?? 24) }}" required style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Imagen del Tour</label>
                    @if(isset($tour) && $tour->image)
                        <div style="margin-bottom: 1rem;">
                            <img src="{{ Storage::url($tour->image) }}" alt="Preview" style="max-height: 150px; border-radius: 6px; border: 1px solid var(--border);">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px; background: white;">
                    <small style="color: var(--text-muted);">Sube una nueva imagen si deseas cambiarla (Opcional).</small>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Descripción Corta</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">{{ old('description', $tour->description ?? '') }}</textarea>
                </div>

                <button type="submit" class="btn-action" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1rem;"><i class="fa-solid fa-save"></i> Guardar Tour</button>
            </form>
        </div>
    </div>
</x-app-layout>
