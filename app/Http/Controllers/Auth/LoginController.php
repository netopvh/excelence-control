<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();
            // auth()->user()->createToken('auth_token')->plainTextToken;

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['username' => 'usuário ou senha inválidos'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        // auth()->user()->tokens()->delete();

        Auth::logout();

        $request->session()->invalidate();

        return redirect()->route('login');
    }
}
