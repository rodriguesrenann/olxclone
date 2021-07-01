<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|string|unique:users',
            'password' => 'required|min:4|same:password_confirmation',
            'password_confirmation' => 'required|min:4|',
            'state' => 'required|integer|exists:states,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400);
        }

        User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => password_hash($request['password'], PASSWORD_DEFAULT),
            'state' => $request['state']
        ]);

        $token = Auth::attempt([
            'email' => $request['email'],
            'password' => $request['password'],
        ]);

        if (!$token) {
            return response()->json([
                'error' => 'Erros internos',
            ], 400);
        }

        $info = Auth::user();
        return response()->json([
            'info' => $info,
            'token' => $token,
        ], 200);
    }

    public function login(Request $request)
    {
        $info = $request->all();

        $token = Auth::attempt([
            'email' => $info['email'],
            'password' => $info['password']
        ]);

        if (!$token) {
            return response()->json([
                'error' => 'e-mail e/ou senha incorreto(s)',
            ], 400);
        }

        $info = Auth::user();
        return response()->json([
            'info' => $info,
            'token' => $token,
        ], 200);
    }

    public function logout()
    {
        try{
            Auth::logout();

            return response()->json([
                'success' => 'Logout'
            ], 200);
            
        }catch(Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function unauthorized()
    {
        return response()->json([
            'error' => 'Acesso negado'
        ], 403);
    }
}
