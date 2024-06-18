<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Valida as credenciais
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tenta autenticar o usuário
        if (Auth::attempt($credentials)) {
            // Regenera a sessão para evitar ataques de fixação de sessão
            $request->session()->regenerate();

            // Obtém o usuário autenticado e cria um token
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'token' => $token,      // Token adicionado na resposta
                'user' => Auth::user(),
                'message' => 'Login realizado com sucesso',
                'status' => 200,
            ]);
        }

        // Retorna erro se as credenciais forem inválidas
        return response()->json([
            'message' => 'As credenciais informadas são inválidas',
        ], 400);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
