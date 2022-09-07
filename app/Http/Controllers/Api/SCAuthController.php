<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SCAuthController extends Controller
{
    public function login(Request $request) {
        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json([
                "ok" => false,
                "message" => "Las Credenciales Suministradas NO son Válidas.",
            ], 422);
        }else{
            $user = User::where('username', $request['username'])->first();
            $token = $user->createToken($request->username."_auth_token")->plainTextToken;
            $role = $user->getRoleNames();

            if (sizeof($role) == 0) {
                return response()->json([
                    "ok" => true,
                    "message" => "Usuario Logeado Exitosamente.",
                    "name" => $user->firstname." ".$user->lastname,
                    "uuid" => $user->id,
                    "identification_document" => $user->identification_document,
                    "role" => NULL,
                    "token" => $token,
                ], 200);
            }else{
                return response()->json([
                    "ok" => true,
                    "message" => "Usuario Logeado Exitosamente.",
                    "name" => $user->firstname." ".$user->lastname,
                    "uuid" => $user->id,
                    "identification_document" => $user->identification_document,
                    "role" => $role[0],
                    "token" => $token,
                ], 200);
            }
        }
    }

    public function logout() {
            $id = auth()->id();
            if (isset($id)) {
                $user = User::find($id);
                $user->tokens()->delete();

                return response()->json([
                    "ok" => true,
                    "message" => "Cierre de Sesión Exitoso.",
                ], 200);
            }else{
                return response()->json([
                    "ok" => false,
                    "message" => "Cierre de Sesión Fallido.",
                ], 401);
            }
    }

    public function refresh() {
        $id = auth()->id();
        $user = User::find($id);
        $user->tokens()->delete();
        $token = $user->createToken($user->username."_auth_token")->plainTextToken;
        $role = $user->getRoleNames();

        if (sizeof($role) == 0) {
            return response()->json([
                "ok" => true,
                "message" => "Usuario Logeado Exitosamente.",
                "name" => $user->firstname." ".$user->lastname,
                "uuid" => $user->id,
                "identification_document" => $user->identification_document,
                "role" => NULL,
                "token" => $token,
            ], 200);
        }else{
            return response()->json([
                "ok" => true,
                "message" => "Usuario Logeado Exitosamente.",
                "name" => $user->firstname." ".$user->lastname,
                "uuid" => $user->id,
                "identification_document" => $user->identification_document,
                "role" => $role[0],
                "token" => $token,
            ], 200);
        }
    }
}
