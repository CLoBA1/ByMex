<x-app-layout>
    @section('header-title', 'Editar Autobús')

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-pen"></i> Editar Autobús: {{ $bus->name }}</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.buses.update', $bus->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre o Modelo *</label>
                    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $bus->name) }}" required>
                    @error('name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="description" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción Corta</label>
                    <textarea id="description" name="description" class="form-input" rows="3">{{ old('description', $bus->description) }}</textarea>
                    @error('description') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ $bus->is_active ? 'checked' : '' }} style="width: 1.2rem; height: 1.2rem;">
                        <span style="font-weight: 600;">Autobús Activo (Visible en público)</span>
                    </label>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border); margin: 2rem 0;">

                <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; color: var(--navy);">Galería de Imágenes</h3>
                
                @if($bus->images->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                        @foreach($bus->images as $img)
                            <div style="border: 1px solid var(--border); border-radius: 8px; overflow: hidden; position: relative; background: #f8fafc;">
                                <img src="{{ Storage::url($img->image_path) }}" style="width: 100%; height: 120px; object-fit: cover;">
                                
                                @if($img->is_primary)
                                    <div style="position: absolute; top: 5px; left: 5px; background: var(--primary); color: white; padding: 0.2rem 0.5rem; font-size: 0.7rem; border-radius: 4px; font-weight: bold;">
                                        <i class="fa-solid fa-star"></i> Portada
                                    </div>
                                @endif
                                
                                <div style="padding: 0.5rem; display: flex; justify-content: space-between; gap: 0.5rem;">
                                    @if(!$img->is_primary)
                                        <button type="submit" form="form-primary-{{ $img->id }}" class="btn-action" style="flex: 1; padding: 0.2rem; font-size: 0.75rem; background: var(--slate-600); justify-content: center;" title="Marcar como portada">
                                            <i class="fa-solid fa-star"></i>
                                        </button>
                                    @else
                                        <div style="flex: 1;"></div>
                                    @endif
                                    
                                    <button type="submit" form="form-delete-{{ $img->id }}" class="btn-action" style="flex: 1; padding: 0.2rem; font-size: 0.75rem; background: var(--red); justify-content: center;" title="Eliminar imagen" onclick="return confirm('¿Eliminar esta imagen?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: var(--text-muted); margin-bottom: 2rem; font-style: italic;">No hay imágenes cargadas para este autobús.</p>
                @endif

                <div style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px dashed var(--slate-300);">
                    <label for="images" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Agregar más imágenes</label>
                    <input type="file" id="images" name="images[]" class="form-input" multiple accept="image/*">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Puedes seleccionar varias imágenes al mismo tiempo.</p>
                    @error('images.*') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                    <a href="{{ route('admin.buses.index') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border); text-decoration: none;">Cancelar</a>
                    <button type="submit" class="btn-action" style="background: var(--navy);"><i class="fa-solid fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
            
            {{-- Formularios independientes para acciones de imagen --}}
            @foreach($bus->images as $img)
                <form id="form-delete-{{ $img->id }}" action="{{ route('admin.buses.images.destroy', $img->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                @if(!$img->is_primary)
                    <form id="form-primary-{{ $img->id }}" action="{{ route('admin.buses.images.primary', $img->id) }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>
