@extends('layouts.public')

@section('title', 'Mi Cuenta')

@section('extra-css')
    <style>
        .login-container {
            max-width: 460px;
            margin: 6rem auto 4rem;
            padding: 0 1.5rem;
        }
        .login-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 2.5rem;
            box-shadow: var(--shadow-xl);
            border-top: 5px solid var(--color-primary);
        }
        .login-card h1 {
            font-family: 'Montserrat', sans-serif;
            color: var(--color-dark);
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }
        .login-card .subtitle {
            color: var(--color-dark-muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--color-dark);
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(214, 40, 40, 0.1);
        }
        .form-error {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.35rem;
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .remember-row label {
            font-size: 0.9rem;
            color: var(--color-dark-muted);
            cursor: pointer;
        }
        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-login:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')
    <main class="login-container">
        <div class="login-card">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-user-circle" style="font-size: 3rem; color: var(--color-primary); margin-bottom: 0.5rem;"></i>
                <h1>Mi Cuenta</h1>
                <p class="subtitle">Accede a tu portal de viajes</p>
            </div>

            @if($errors->any())
                <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 0.75rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('client.login.submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="login_input"><i class="fa-solid fa-user" style="color: #94a3b8;"></i> Correo o WhatsApp</label>
                    <input type="text" name="login_input" id="login_input" value="{{ old('login_input') }}" required autofocus placeholder="Tu correo o número de WhatsApp">
                    @error('login_input')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock" style="color: #94a3b8;"></i> Contraseña</label>
                    <input type="password" name="password" id="password" required placeholder="••••••••">
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Recordarme en este dispositivo</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
                </button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: var(--color-dark-muted);">
                ¿No tienes cuenta? Solicita tus accesos con nuestro equipo al momento de tu próxima reservación.
            </p>
        </div>
    </main>
@endsection
