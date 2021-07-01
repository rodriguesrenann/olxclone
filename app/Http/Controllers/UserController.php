<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getLoggedUserInfo()
    {
        $info = User::with('state')->where('id', Auth::id())->first();

        return response()->json([
            'info' => $info,
            'ads' => Ad::with('state')->where('user_id', Auth::id())->get(),
        ], 200);
    }

    public function editLoggedUserInfo(Request $request)
    {
        //todo 
        //trocar senha so ao colocar senha antiga
        $info = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'string|min:3',
            'email' => 'email|string',
            'password' => 'string|min:4',
            'state' => 'exists:states,id|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $user = User::where('id', $info['id'])->first();

        if ($request['email'] !== $user['email']) {
            $emailExists = User::where('email', $request['email'])->first();

            if ($emailExists) {
                return response()->json([
                    'error' => 'E-mail já cadastrado no sistema'
                ], 409);
            }

            $user->email = $request['email'] ?? $user['email'];
        }

        $user->name = $request['name'] ?? $user['name'];
        $user->password = $request['password'] ? password_hash($request['password'], PASSWORD_DEFAULT) : $user['password'];
        $user->state = $request['state'] ?? $user['state'];
        $user->save();
        
        return response()->json([
            'sucess' => 'Dados alterados com sucesso'
        ], 200);
    }
}
