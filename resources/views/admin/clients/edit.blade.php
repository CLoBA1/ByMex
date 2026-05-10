<x-app-layout>
    @section('header-title', 'Editar Cliente')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-family: 'Montserrat', sans-serif; font-size: 1.5rem; color: var(--navy); font-weight: 800;">
                <i class="fa-solid fa-user-pen"></i> Editar: {{ $client->name }}
            </h2>
        </div>
        <a href="{{ route('admin.clients.show', $client->id) }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
            <i class="fa-solid fa-arrow-left"></i> Volver al Perfil
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

    <form action="{{ route('admin.clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- MEMBRESÍA --}}
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-id-badge"></i> Membresía</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
                    <div>
                        <label for="membership_number" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">
                            Código de Membresía
                        </label>
                        <input type="text" name="membership_number" id="membership_number"
                            value="{{ old('membership_number', $client->membership_number) }}"
                            placeholder="Ej: BYMEX-000001"
                            style="width: 100%; padding: 0.75rem; border: 1px solid {{ $errors->has('membership_number') ? '#ef4444' : 'var(--border)' }}; border-radius: 6px; font-size: 1rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">
                        @if($errors->has('membership_number'))
                            <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem;">{{ $errors->first('membership_number') }}</p>
                        @endif
                        <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.5rem;">
                            Identificador único del cliente. Déjalo vacío si aún no se asigna. Debe ser único en todo el sistema.
                        </p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 6px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;"><i class="fa-solid fa-circle-info"></i> Estado actual:</div>
                        @if($client->membership_number)
                            <span style="background: var(--navy); color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.9rem; font-weight: bold; letter-spacing: 1px;">
                                <i class="fa-solid fa-crown" style="color: #fbbf24; margin-right: 3px;"></i> {{ $client->membership_number }}
                            </span>
                        @else
                            <span style="background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                <i class="fa-solid fa-minus"></i> Sin membresía asignada
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ACCESOS DEL CLIENTE --}}
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-key"></i> Acceso al Portal</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
                    <div>
                        <label for="password" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">
                            Asignar o Cambiar Contraseña
                        </label>
                        <input type="text" name="password" id="password"
                            placeholder="Déjalo en blanco para no modificar"
                            style="width: 100%; padding: 0.75rem; border: 1px solid {{ $errors->has('password') ? '#ef4444' : 'var(--border)' }}; border-radius: 6px; font-size: 1rem;">
                        @if($errors->has('password'))
                            <p style="color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem;">{{ $errors->first('password') }}</p>
                        @endif
                        <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.5rem;">
                            Mínimo 8 caracteres. Escribe aquí para cambiar o establecer la contraseña del cliente.
                        </p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 6px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;"><i class="fa-solid fa-circle-info"></i> Estado de la cuenta:</div>
                        @if($client->password)
                            <span style="background: #16a34a; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                <i class="fa-solid fa-check"></i> El cliente ya tiene contraseña asignada
                            </span>
                        @else
                            <span style="background: #ef4444; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                <i class="fa-solid fa-xmark"></i> El cliente no tiene contraseña (no puede iniciar sesión)
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- DATOS PERSONALES --}}
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-address-card"></i> Datos Personales</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label for="name" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Nombre Completo *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $client->name) }}" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="phone" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Teléfono Principal *</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $client->phone) }}" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="email" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="whatsapp" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">WhatsApp</label>
                        <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $client->whatsapp) }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="origin_city" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Ciudad de Origen</label>
                        <input type="text" name="origin_city" id="origin_city" value="{{ old('origin_city', $client->origin_city) }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="curp" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">CURP</label>
                        <input type="text" name="curp" id="curp" value="{{ old('curp', $client->curp) }}" maxlength="18"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem; text-transform: uppercase;">
                    </div>
                    <div>
                        <label for="birthdate" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Fecha de Nacimiento</label>
                        <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate', $client->birthdate) }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label for="emergency_contact" style="display: block; font-weight: 600; color: var(--navy); margin-bottom: 0.5rem;">Contacto de Emergencia</label>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact', $client->emergency_contact) }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem;">
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.clients.show', $client->id) }}" class="btn-action" style="background: var(--slate-100); color: var(--navy); border: 1px solid var(--border);">
                Cancelar
            </a>
            <button type="submit" class="btn-action" style="background: var(--navy); border: none; padding: 0.75rem 2rem;">
                <i class="fa-solid fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</x-app-layout>
