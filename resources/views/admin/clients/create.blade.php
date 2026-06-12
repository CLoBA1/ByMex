<x-app-layout>
    @section('header-title', 'Nuevo Cliente')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.5rem; color: var(--navy); font-weight: 800;">
                <i class="fa-solid fa-user-plus"></i> Registrar Nuevo Cliente
            </h2>
        </div>
        <a href="{{ route('admin.clients.index') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
            <i class="fa-solid fa-arrow-left"></i> Volver al Listado
        </a>
    </div>

    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <strong><i class="fa-solid fa-circle-exclamation"></i> Hay errores en el formulario:</strong>
            <ul style="margin: 0.5rem 0 0 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.clients.store') }}" method="POST">
        @csrf

        {{-- DATOS PERSONALES --}}
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-address-card"></i> Datos Personales</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label for="name" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Nombre Completo *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid {{ $errors->has('name') ? '#ef4444' : 'var(--border)' }}; border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="phone" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Teléfono Principal *</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid {{ $errors->has('phone') ? '#ef4444' : 'var(--border)' }}; border-radius: 6px; font-size: 0.95rem;">
                        <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.5rem;">
                            Se usa como identificador único.
                        </p>
                    </div>
                    <div>
                        <label for="whatsapp" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">WhatsApp</label>
                        <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="email" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="origin_city" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Ciudad de Origen</label>
                        <input type="text" name="origin_city" id="origin_city" value="{{ old('origin_city') }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="birthdate" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Fecha de Nacimiento</label>
                        <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="curp" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">CURP</label>
                        <input type="text" name="curp" id="curp" value="{{ old('curp') }}" maxlength="18"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem; text-transform: uppercase;">
                    </div>
                    <div>
                        <label for="emergency_contact" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Contacto de Emergencia</label>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.clients.index') }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
                Cancelar
            </a>
            <button type="submit" class="btn-action" style="background: var(--navy); border: none; padding: 0.75rem 2rem;">
                <i class="fa-solid fa-save"></i> Guardar Cliente
            </button>
        </div>
    </form>
</x-app-layout>
