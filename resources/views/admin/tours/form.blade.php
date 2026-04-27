<x-app-layout>
    @section('header-title', isset($tour) ? 'Editar Tour' : 'Crear Nuevo Tour')

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title">{{ isset($tour) ? 'Editar: '.$tour->title : 'Información del Tour' }}</h2>
            <a href="{{ route('admin.tours.index') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">Cancelar</a>
        </div>
        <div class="card-body">
            <form action="{{ isset($tour) ? route('admin.tours.update', $tour->id) : route('admin.tours.store') }}" method="POST">
                @csrf
                @if(isset($tour)) @method('PUT') @endif

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

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Ruta de Imagen (Ej. img/tours/foto.png)</label>
                    <input type="text" name="image" value="{{ old('image', $tour->image ?? '') }}" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
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
