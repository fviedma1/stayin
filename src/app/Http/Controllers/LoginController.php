<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        Log::channel('info_log')->info('S\'ha accedit al formulari d\'inici de sessió.');
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $role_admin = 'admin';
        $role_secretary = 'secretary';

        Log::channel('info_log')->info('Intentant iniciar sessió amb el nom: ' . $request->input('name'));

        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            Log::channel('info_log')->info('Inici de sessió correcte per a l\'usuari: ' . $request->input('name'));

            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role->name == $role_admin) {
                return redirect()->intended(route('hotel.index'));
            } else if ($user->role->name == $role_secretary) {
                return redirect()->intended(route('recepcionist.gestor', ['hotelId' => $user->hotel->id]));
            }
        }

        Log::channel('errorlog')->info('Inici de sessió correcte per a l\'usuari: ' . $request->input('name'));
    
        session()->flash('status', 'name d\'usuari o contrasenya incorrectes!');

        return redirect(route('login'))->withErrors([
            'name' => 'Les credencials proporcionades no són correctes.',
            'password' => 'Les credencials proporcionades no són correctes.',
        ]);
    }

    public function logout()
    {
        if (Auth::check()) {
            Log::channel('info_log')->info('L\'usuari ' . Auth::user()->name . ' ha tancat la sessió.');
        } else {
            Log::channel('info_log')->info('Un usuari no autenticat ha intentat tancar la sessió.');
        }

        session()->flush();
        Auth::logout();

        return redirect('login');
    }
}
