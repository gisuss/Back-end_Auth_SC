<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class newForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $verify = User::where('email', $request->all()['email'])->exists();

        if ($verify) {
            $verify2 =  DB::table('password_resets')->where([
                ['email', $request->all()['email']]
            ]);

            if ($verify2->exists()) {
                $verify2->delete();
            }

            $token = Str::random(64);
            $password_reset = DB::table('password_resets')->insert([
                'email' => $request->all()['email'],
                'token' =>  $token,
                'created_at' => Carbon::now()
            ]);

            if ($password_reset) {
                Mail::to($request->all()['email'])->send(new ResetPassword($token));

                // AL RECIBIR ESTE JSON DE RESPUESTA, EL CLIENTE DEBE REDIRIGIRSE A LA VENTANA DE LOGIN
                return response()->json([
                    'ok' => true,
                    'message' => "Enlace de reseteo de contraseña enviado correctamente."
                ], 200);
            }
        }else{
            // AL RECIBIR ESTE JSON DE RESPUESTA, EL CLIENTE DEBE PERMANECER EN LA VISTA ACTUAL
            return response()->json([
                    'ok' => false,
                    'message' => "Este email no existe."
            ], 400);
        }
    }
}
