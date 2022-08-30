<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class UserController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'firstname' => 'required|string|min:4',
            'lastname' => 'required|string|min:4',
            'email' => 'required|email|unique:users',
            'identification_document' => 'required|string|min:6'
        ]);

        $user = new User();
        $user->firstname = trim(Str::ucfirst(Str::lower($request->firstname)));
        $user->lastname = trim(Str::ucfirst(Str::lower($request->lastname)));
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
        $username = substr(Str::lower($user->firstname), 0, $i).Str::lower($user->lastname);
        while (DB::table('users')->where('username', $username)->exists()) {
            $i += 1;
            if ($i <= 2) {
                $username = substr(Str::lower($user->firstname), 0, $i).Str::lower($user->lastname);
            }else{
                $j = $i-2;
                $username = substr(Str::lower($user->firstname), 0, 2).Str::lower($user->lastname).$j;
            }
        }
        $user->username = $username;

        //GENERANDO PASSWORD Hash::make($request->identification_document)
        $user->password = Hash::make($request->identification_document);

        //ROL ASIGNADO AL USUARIO
        if ($user->hasRole('coordinator')) {
            $role = 'coordinator';
        }else if ($user->hasRole('student')) {
            $role = 'student';
        }else if ($user->hasRole('tutor')) {
            $role = 'tutor';
        }

        $user->save();

        return response()->json([
            "ok" => 'true',
            "message" => "Registro de Usuario Exitoso.",
            "name" => $user->firstname." ".$user->lastname,
            "uuid" => $user->id,
            "identification_document" => $user->identification_document,
            "role" => $role,
        ]);
    }

    public function login(Request $request) {
        $request->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $user = User::where("username", "=", $request->username)->first();

        if (isset($user->id)) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken($request->username."_auth_token")->plainTextToken;
                $role = $user->getRoleNames();
                
                return response()->json([
                    "ok" => 'true',
                    "message" => "Usuario Logeado Exitosamente.",
                    "name" => $user->firstname." ".$user->lastname,
                    "uuid" => $user->id,
                    "identification_document" => $user->identification_document,
                    "role" => $role[0],
                    'token_type' => 'bearer',
                    "token" => $token,
                ]);
            }else{
                return response()->json([
                    "ok" => 'false',
                    "message" => "La contraseña es incorrecta.",
                ]);
            }
        }else{
            return response()->json([
                "ok" => 'false',
                "message" => "Usuario no Registrado.",
            ]);
        }
    }

    public function userProfile() {
        return response()->json([
            "ok" => 'true',
            "message" => "Datos del Perfil de Usuario.",
            "data" => auth()->user(),
        ]);
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

            if (isset($request->role)) {
                if ($request->role != $user->role) {
                    // DB::table('model_has_roles')->where('model_id',$id)->delete();
                    $user->removeRole($user->role);
                    $user->assignRole($request->role);
                }
            }

            return response()->json([
                "ok" => 'true',
                'message' => "Usuario Actualizado Correctamente.",
                "data" => $user,
            ]);
        }else{
            return response()->json([
                "ok" => "false",
                "message" => "El usuario no se encuentra Registrado"
            ]);
        }
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        // auth()->user()->tokens()->delete();

        return response()->json([
            "ok" => 'true',
            "message" => "Cierre de Sesión Exitoso.",
        ]);
    }
}
