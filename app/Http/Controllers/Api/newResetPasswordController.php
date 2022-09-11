<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class newResetPasswordController extends Controller
{
    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'same:password'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        //VERIFICACION DE TOKEN RECIBIDO POR LOS HEADERS
        $token = $request->header('Authorization');
        // $arr = [];
        // $arr = array_merge($arr, $this->verifyPin($token));
        $res = $this->verifyPin($token);

        if ($res) {
            //ELIMINO EL TOKEN DE LA TABLA password_resets
            $result = DB::table('password_resets')->where(['token' => $token])->first();
            $email = $result->email;
            DB::table('password_resets')->where('email', $email)->delete();

            $user = User::where('email', $email);
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'ok' => true,
                'message' => "La contraseña ha sido cambiada con éxito.",
            ]);
        }else{
            return response()->json([
                'ok' => false,
                'message' => "Token Inválido o Caducado.",
            ]);
        }
    }

    public function verifyPin($token) {
        $check = DB::table('password_resets')->where('token', $token);

        if ($check->exists()) {
            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
            if ($difference > 3600) {
                $mensaje = "Token Expirado. Recuerde que dispone de 60 minutos para culminar su proceso de reseteo de contraseña.";
                $bool = false;
            }else{
                $mensaje = "Token Válido.";
                $bool = true;
            }
        }else{
            $mensaje = "Token Inválido.";
            $bool = false;
        }

        $array = [];
        array_push($array, $bool, $mensaje);
        
        return $bool;
    }
}
