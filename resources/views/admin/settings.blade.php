<x-app-layout>
    @section('header-title', 'Configuración del Sistema')

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-gear"></i> Ajustes Generales</h2>
        </div>
        <div class="card-body">
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Gestiona las credenciales de acceso administrativo y parámetros generales de la plataforma By Mex.</p>

            <form action="#" method="POST">
                @csrf
                <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                    <h3 style="font-size: 1.1rem; color: var(--navy); font-weight: 700; margin-bottom: 1rem;"><i class="fa-solid fa-shield-halved" style="color: var(--gold);"></i> Credenciales del Administrador</h3>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Correo de Acceso</label>
                        <input type="email" value="{{ Auth::user()->email }}" disabled style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px; background: var(--bg-body); color: var(--text-muted);">
                        <small style="color: var(--text-muted); display: block; margin-top: .25rem;">Para cambiar el correo principal, contacte a soporte técnico.</small>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Nueva Contraseña</label>
                            <input type="password" name="password" placeholder="Mínimo 8 caracteres" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: .5rem; font-weight: 600; font-size: .85rem;">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" placeholder="Mínimo 8 caracteres" style="width: 100%; padding: .75rem; border: 1px solid var(--border); border-radius: 6px;">
                        </div>
                    </div>
                </div>

                <div style="border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                    <h3 style="font-size: 1.1rem; color: var(--navy); font-weight: 700; margin-bottom: 1rem;"><i class="fa-solid fa-bell" style="color: var(--primary);"></i> Preferencias de Notificación</h3>
                    
                    <label style="display: flex; align-items: center; gap: .75rem; margin-bottom: 1rem; cursor: pointer;">
                        <input type="checkbox" checked style="width: 18px; height: 18px; accent-color: var(--primary);">
                        <span style="font-weight: 500;">Recibir email por cada nueva reserva</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: .75rem; margin-bottom: 1rem; cursor: pointer;">
                        <input type="checkbox" style="width: 18px; height: 18px; accent-color: var(--primary);">
                        <span style="font-weight: 500;">Alerta cuando un viaje esté al 90% de capacidad</span>
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="button" class="btn-action" style="padding: .75rem 2rem; font-size: 1rem;" onclick="alert('Demostración UI: Funciones de guardado desactivadas en modo prueba.')"><i class="fa-solid fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
