@extends('layouts.app')

@section('title', 'Banners Promocionales')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; color: var(--navy); margin-bottom: 0.25rem;"><i class="fa-solid fa-images"></i> Banners Promocionales</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Gestiona los banners de la página principal.</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn-action" style="background: var(--navy);">
            <i class="fa-solid fa-plus"></i> Nuevo Banner
        </a>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $banner)
                    <tr>
                        <td>{{ $banner->sort_order }}</td>
                        <td>
                            <img src="{{ Storage::url($banner->image_desktop) }}" alt="Banner" style="height: 60px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td>
                            <strong>{{ $banner->title ?? 'Sin título' }}</strong>
                            <br><small style="color: var(--text-muted);">{{ $banner->subtitle }}</small>
                        </td>
                        <td>
                            @if($banner->is_active)
                                <span style="background: #ecfdf5; color: #065f46; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Activo</span>
                            @else
                                <span style="background: #fef2f2; color: #991b1b; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn-action" style="background: var(--navy); padding: 0.4rem 0.6rem; font-size: 0.85rem;" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este banner?');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action" style="background: var(--primary); padding: 0.4rem 0.6rem; font-size: 0.85rem;" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">No hay banners registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
