<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ResetPasswordRequests;

class newResetPasswordController extends Controller
{
    public function resetPassword(ResetPasswordRequests $request) {
        $validated = $request->safe()->only(['password', 'confirm_password']);

        //VERIFICACION DE TOKEN RECIBIDO POR LOS HEADERS
        $token = $request->header('Authorization');

        if (self::verifyPin($token)) {
            //ELIMINAR EL TOKEN DE LA TABLA password_resets
            $result = DB::table('password_resets')->where('token', $token)->first();
            $email = $result->email;
            DB::table('password_resets')->where('email', $email)->delete();

            $check = User::where('email', $email);

            if ($check->exists()) {
                $user = $check->first();
                $user->password = Hash::make($validated['password']);
                $user->update();

                return response()->json([
                    "ok" => true,
                    "message" => "La contraseña ha sido actualizada con éxito.",
                ]);
            }else{
                return response()->json([
                    "ok" => false,
                    "message" => "Usuario no encontrado.",
                ]);
            }
        }else{
            return response()->json([
                "ok" => false,
                "message" => "Token Inválido o Caducado.",
            ]);
        }
    }

    public function verifyPin($token) {
        $bool = false;
        $check = DB::table('password_resets')->where('token', $token);
        
        if ($check->exists()) {
            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
            if ($difference <= 3600) {
                $bool = true;
            }
        }
        
        return $bool;
    }
}
