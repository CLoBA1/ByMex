<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }
        return view('client.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_input' => 'required|string',
            'password'    => 'required',
        ], [
            'login_input.required' => 'Por favor ingresa tu correo o número de WhatsApp.',
            'password.required'    => 'Por favor ingresa tu contraseña.',
        ]);

        $input = trim($request->login_input);

        // 1. Buscar por email
        $client = Client::where('email', $input)->first();

        // 2. Si no encuentra, buscar por whatsapp
        if (!$client) {
            $client = Client::where('whatsapp', $input)->first();
        }

        // 3. Verificar contraseña si se encontró el cliente
        if ($client && Hash::check($request->password, $client->password)) {
            Auth::guard('client')->login($client, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('client.dashboard'));
        }

        return back()->withErrors([
            'login_input' => 'No encontramos una cuenta con ese correo o WhatsApp, o la contraseña es incorrecta.',
        ])->onlyInput('login_input');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('client.login');
    }
}

