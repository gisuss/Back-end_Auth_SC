<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\ResetPassword;
use Illuminate\Support\Str;
use App\Jobs\SendResetEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class newForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "message" => $validator->errors(),
            ], 422);
        }

        $email = trim(Str::lower($request->all()['email']));
        $verify = User::where('email', $email)->exists();

        if ($verify) {
            $verify2 =  DB::table('password_resets')->where([
                ['email', $email]
            ]);

            if ($verify2->exists()) {
                $verify2->delete();
            }

            $token = Str::random(64);
            $password_reset = DB::table('password_resets')->insert([
                'email' => $email,
                'token' =>  $token,
                'created_at' => Carbon::now()
            ]);

            if ($password_reset) {
                dispatch(new SendResetEmail($email, $token))->delay(now()->addSeconds(10));
                // Mail::to($email)->send(new ResetPassword($token));

                // AL RECIBIR ESTE JSON DE RESPUESTA, EL CLIENTE DEBE REDIRIGIRSE A LA VENTANA DE LOGIN
                return response()->json([
                    "ok" => true,
                    "message" => "Enlace de reseteo de contraseña enviado correctamente."
                ], 200);
            }
        }else{
            // AL RECIBIR ESTE JSON DE RESPUESTA, EL CLIENTE DEBE PERMANECER EN LA VISTA ACTUAL
            return response()->json([
                "ok" => false,
                "message" => "El email suministrado no existe."
            ], 400);
        }
    }
}
