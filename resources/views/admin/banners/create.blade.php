@extends('layouts.app')

@section('title', 'Nuevo Banner')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; color: var(--navy); margin-bottom: 0.25rem;"><i class="fa-solid fa-plus"></i> Nuevo Banner</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Agrega un banner para la página principal.</p>
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

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Título (Opcional)</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label>Subtítulo (Opcional)</label>
                    <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle') }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Imagen Desktop <span style="color:red">*</span></label>
                    <input type="file" name="image_desktop" class="form-control" required accept="image/*">
                    <small style="color:var(--text-muted);">Recomendado: 1920x1080px (Alta calidad)</small>
                </div>
                <div class="form-group">
                    <label>Imagen Móvil (Opcional)</label>
                    <input type="file" name="image_mobile" class="form-control" accept="image/*">
                    <small style="color:var(--text-muted);">Si no se sube, se adaptará la de desktop.</small>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Enlace (Opcional)</label>
                    <input type="text" name="link" class="form-control" value="{{ old('link') }}" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Texto del Botón (Opcional)</label>
                    <input type="text" name="button_text" class="form-control" value="{{ old('button_text') }}" placeholder="Ej: Ver Promo">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>Orden de aparición</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="is_active" value="1" checked style="width: 1.2rem; height: 1.2rem;">
                        <strong>Banner Activo</strong>
                    </label>
                </div>
            </div>

            <div style="text-align: right; margin-top: 2rem;">
                <button type="submit" class="btn-action" style="background: var(--navy); padding: 0.75rem 2rem; font-size: 1rem;">
                    <i class="fa-solid fa-save"></i> Guardar Banner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
