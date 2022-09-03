<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|min:4',
            'lastname' => 'required|string|min:4',
            'email' => 'required|email|unique:users',
            'identification_document' => 'required|string|min:6',
            'faculty' => 'nullable|string',
            'departament' => 'nullable|string',
            'phone' => 'nullable|string',
            'gender' => 'nullable|string',
            'birthday' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Registro de Usuario No Exitoso, Error en la Validacion de Datos.',
                'errors' => $validator->errors(),
            ], 422);
        }else{
            $firstname = ucwords(Str::lower(trim(preg_replace('/[0-9\@\.\;\*\#\$\%\_\-\!\,]+/', '', $request->firstname), ' ')));
            $lastname = ucwords(Str::lower(trim(preg_replace('/[0-9\@\.\;\*\#\$\%\_\-\!\,]+/', '', $request->lastname), ' ')));
            if (($firstname == "") || ($lastname == "")) {
                return response()->json([
                    "ok" => false,
                    "message" => "Registro de Usuario No Exitoso, Verifique los campos de Nombre y Apellido.",
                ], 422);
            }else{
                $user = new User();
                $user->firstname = $firstname;
                $user->lastname = $lastname;
                $user->email = trim(Str::lower($request->email));
                $user->identification_document = $request->identification_document;

                $user->departament = isset($request->departament) ? $request->departament : NULL;
                $user->faculty = isset($request->faculty) ? $request->faculty : NULL;
                $user->phone = isset($request->phone) ? $request->phone : NULL;
                $user->gender = isset($request->gender) ? $request->gender : NULL;
                $user->birthday = isset($request->birthday) ? $request->birthday : NULL;
                $user->active = isset($request->active) ? $request->active : 1;

                if ($request->role != NULL) {
                    $user->assignRole($request->role);
                }

                //GENERANDO USERNAME
                $i = 1;
                $apellido = explode(" ", $user->lastname);
                $username = substr(Str::lower($user->firstname), 0, $i).Str::lower($apellido[0]);
                while (DB::table('users')->where('username', $username)->exists()) {
                    $i += 1;
                    if ($i <= 2) {
                        $username = substr(Str::lower($user->firstname), 0, $i).Str::lower($apellido[0]);
                    }else{
                        $j = $i-1;
                        $username = substr(Str::lower($user->firstname), 0, 2).Str::lower($apellido[0]).$j;
                    }
                }
                $user->username = $username;

                //GENERANDO PASSWORD Hash::make($request->identification_document)
                $user->password = Hash::make($request->identification_document);

                $user->save();

                return response()->json([
                    "ok" => true,
                    "message" => "Registro de Usuario Exitoso.",
                    "username" => $user->username,
                    "password" => $request->identification_document,
                ], 200);
            }
        }
    }

    public function login(Request $request) {
        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json([
                "ok" => false,
                "message" => "Las Credenciales Suministradas no son Válidas.",
            ], 422);
        }else{
            $user = User::where('username', $request['username'])->firstOrFail();
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

    public function userProfile(Request $request) {
        $role = $request->user()->getRoleNames();
        if (sizeof($role) == 0) {
            return response()->json([
                "ok" => true,
                "message" => "Datos del Perfil de Usuario.",
                "role" => NULL,
                "user" => $request->user(),
            ], 200);
        }else{
            return response()->json([
                "ok" => true,
                "message" => "Datos del Perfil de Usuario.",
                "role" => $role[0],
                "user" => $request->user(),
            ], 200);
        }
    }

    public function edituserProfile(Request $request, $id) {
        if (User::where('id',$id)->exists()) {
            $user = User::find($id);
            $user->firstname = isset($request->firstname) ? trim(Str::ucfirst(Str::lower($request->firstname))) : $user->firstname;
            $user->lastname = isset($request->lastname) ? trim(Str::ucfirst(Str::lower($request->lastname))) : $user->lastname;
            $user->identification_document = isset($request->identification_document) ? $request->identification_document : $user->identification_document;
            $user->email = isset($request->email) ? trim(Str::lower($request->email)) : $user->email;
            $user->faculty = isset($request->faculty) ? $request->faculty : $user->faculty;
            $user->departament = isset($request->departament) ? $request->departament : $user->departament;
            $user->phone = isset($request->phone) ? $request->phone : $user->phone;
            $user->gender = isset($request->gender) ? $request->gender : $user->gender;
            $user->birthday = isset($request->birthday) ? $request->birthday : $user->birthday;
            $user->active = isset($request->active) ? $request->active : $user->active;

            $user->update();
            $role = $user->getRoleNames();

            if (isset($request->role)) {
                if ($request->role != $role[0]) {
                    $user->removeRole($role[0]);
                    $user->assignRole($request->role);
                    unset($role);
                    $role = $user->getRoleNames();
                }
            }

            return response()->json([
                "ok" => true,
                'message' => "Usuario Actualizado Correctamente.",
                "role" => $role[0],
                "data" => $user,
            ], 200);
        }else{
            return response()->json([
                "ok" => false,
                "message" => "El usuario no se encuentra Registrado",
            ], 401);
        }
    }
    
    public function changePassword (Request $request) {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:6|max:16',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Error en la Validacion de Contraseñas.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Contraseña Actualizada Correctamente.',
            ], 200);
        }else{
            return response()->json([
                'ok' => false,
                'message' => 'La contraseña Suministrada no coincide con la Registrada.',
            ], 400);
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
}
