@extends('layouts.app')

@section('title', 'Editar Banner')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; color: var(--navy); margin-bottom: 0.25rem;"><i class="fa-solid fa-pen"></i> Editar Banner</h2>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="btn-action" style="background: var(--slate-600);">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card">
        @if ($errors->any())
            <div style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Título (Opcional)</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}">
                </div>
                <div class="form-group">
                    <label>Subtítulo (Opcional)</label>
                    <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $banner->subtitle) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Imagen Desktop (Dejar vacío para no cambiar)</label>
                    <input type="file" name="image_desktop" class="form-control" accept="image/*">
                    @if($banner->image_desktop)
                        <div style="margin-top: 0.5rem;">
                            <img src="{{ Storage::url($banner->image_desktop) }}" style="max-width: 100%; border-radius: 4px; border: 1px solid var(--border);">
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label>Imagen Móvil (Dejar vacío para no cambiar)</label>
                    <input type="file" name="image_mobile" class="form-control" accept="image/*">
                    @if($banner->image_mobile)
                        <div style="margin-top: 0.5rem;">
                            <img src="{{ Storage::url($banner->image_mobile) }}" style="max-width: 100%; border-radius: 4px; border: 1px solid var(--border);">
                        </div>
                    @endif
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Enlace (Opcional)</label>
                    <input type="text" name="link" class="form-control" value="{{ old('link', $banner->link) }}" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Texto del Botón (Opcional)</label>
                    <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $banner->button_text) }}" placeholder="Ej: Ver Promo">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Orden de aparición</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order) }}">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} style="width: 1.2rem; height: 1.2rem;">
                        <strong>Banner Activo</strong>
                    </label>
                </div>
            </div>

            <div style="text-align: right; margin-top: 2rem;">
                <button type="submit" class="btn-action" style="background: var(--navy); padding: 0.75rem 2rem; font-size: 1rem;">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
