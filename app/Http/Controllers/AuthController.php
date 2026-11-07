<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('pos.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $authData = [
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($authData, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.');
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang kembali, ' . $user->name . ' (Administrator)!');
            }

            return redirect()->intended(route('pos.index'))
                ->with('success', 'Selamat bertugas, ' . $user->name . ' (Kasir)!');
        }

        return back()->withInput($request->only('login', 'remember'))
            ->with('error', 'Login gagal! Username/Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
