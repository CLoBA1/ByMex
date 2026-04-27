<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Corporativo | Viajes By Mex</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #D62828;
            --navy: #0D1B2A;
            --gold: #F4A261;
            --slate-100: #F1F5F9;
            --slate-500: #64748B;
            --slate-800: #1E293B;
            --white: #FFFFFF;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: var(--white); height: 100vh; display: flex; overflow: hidden; }
        
        .login-split { display: flex; width: 100%; height: 100%; }
        
        .login-image { flex: 1; position: relative; display: none; }
        @media(min-width: 900px) { .login-image { display: block; } }
        .login-image img { width: 100%; height: 100%; object-fit: cover; }
        .login-image .overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(13,27,42,0.4), rgba(13,27,42,0.9)); }
        .login-image .content { position: absolute; bottom: 10%; left: 10%; right: 10%; color: var(--white); }
        .login-image h2 { font-family: 'Montserrat', sans-serif; font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; line-height: 1.2; }
        .login-image p { font-size: 1.1rem; color: #E2E8F0; max-width: 450px; line-height: 1.6; }
        
        .login-form-wrapper { flex: 1; max-width: 600px; padding: 4rem; display: flex; flex-direction: column; justify-content: center; background: var(--white); }
        @media(max-width: 900px) { .login-form-wrapper { max-width: 100%; padding: 2rem; } }
        
        .brand-header { margin-bottom: 3rem; }
        .brand-header img { height: 60px; margin-bottom: 1.5rem; }
        .brand-header h1 { font-family: 'Montserrat', sans-serif; color: var(--navy); font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem; }
        .brand-header p { color: var(--slate-500); font-size: 0.95rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; color: var(--slate-800); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.85rem 1rem; border: 2px solid var(--slate-100); border-radius: 8px; font-size: 1rem; color: var(--navy); font-family: 'Inter', sans-serif; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: var(--primary); }
        
        .btn-primary { background: var(--primary); color: var(--white); border: none; width: 100%; padding: 1rem; border-radius: 8px; font-size: 1rem; font-weight: 600; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: background 0.3s; margin-top: 1rem; }
        .btn-primary:hover { background: #B21F1F; }
        
        .auth-footer { margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; }
        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
        .checkbox-group input { width: 16px; height: 16px; accent-color: var(--primary); }
        
        .back-link { position: absolute; top: 2rem; right: 2rem; color: var(--slate-500); text-decoration: none; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s; }
        .back-link:hover { color: var(--navy); }
        
        .error-msg { color: var(--primary); font-size: 0.85rem; margin-top: 0.5rem; display: block; }
    </style>
</head>
<body>

    <div class="login-split">
        <!-- Lado Izquierdo (Imagen Inmersiva) -->
        <div class="login-image">
            <img src="https://images.unsplash.com/photo-1518105779142-d975f22f1b0a?auto=format&fit=crop&q=80&w=1920" alt="México">
            <div class="overlay"></div>
            <div class="content">
                <h2>Panel Corporativo<br><span style="color:var(--gold);">Viajes By Mex</span></h2>
                <p>Gestión integral de reservaciones, tours y operaciones. Acceso exclusivo para administradores y coordinadores.</p>
            </div>
        </div>

        <!-- Lado Derecho (Formulario Limpio) -->
        <div class="login-form-wrapper" style="position: relative;">
            <a href="{{ url('/') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Volver al sitio</a>
            
            <div class="brand-header">
                <img src="{{ asset('img/logobymex.jpeg') }}" alt="By Mex" style="border-radius: 8px;">
                <h1>Iniciar Sesión</h1>
                <p>Ingresa tus credenciales para acceder al sistema.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@bymex.com">
                    @error('email')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me / Forgot Password -->
                <div class="auth-footer">
                    <label class="checkbox-group">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span style="color: var(--slate-500);">Recordarme</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <button type="submit" class="btn-primary">
                    Ingresar al Panel <i class="fa-solid fa-right-to-bracket" style="margin-left: .5rem;"></i>
                </button>
            </form>
        </div>
    </div>

</body>
</html>
