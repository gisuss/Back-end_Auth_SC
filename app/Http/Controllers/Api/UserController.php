<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|min:4',
            'lastname' => 'required|string|min:4',
            'email' => 'required|email|unique:users',
            'identification_document' => 'required|string|min:6',
            'role' => 'required',
            'active' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Registro de Usuario No Exitoso, Error en la Validacion de Datos Recibidos.',
                'errors' => $validator->errors(),
            ], 422);
        }else{
            $firstname = ucwords(Str::lower(trim(preg_replace('/[^a-z" "]/i', '', $request->firstname), ' ')));
            $lastname = ucwords(Str::lower(trim(preg_replace('/[^a-z" "]/i', '', $request->lastname), ' ')));
            if (($firstname == "") || ($lastname == "")) {
                return response()->json([
                    "ok" => false,
                    "message" => "Registro de Usuario No Exitoso, Verifique los campos de Nombre y Apellido.",
                ], 422);
            }else{
                //GENERANDO CEDULA VALIDA
                $letra = Str::upper(substr(trim($request->identification_document), 0, 1));
                if ($letra == 'V' || $letra == 'E' || $letra == 'J') {
                    $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification_document);
                    $cedula = $letra."-".$ci_sin_formato;
                }else{
                    $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification_document);
                    $cedula = "V-".$ci_sin_formato;
                }

                if (DB::table('users')->where('identification_document', $cedula)->exists()) {
                    return response()->json([
                        "ok" => false,
                        "message" => "Registro de Usuario No Exitoso, la Cédula de Identidad suministrada ya existe.",
                    ], 422);
                }else{
                    //GENERANDO USERNAME
                    $i = 1;
                    $apellido = explode(" ", $lastname);
                    $username = substr(Str::lower($firstname), 0, $i).Str::lower($apellido[0]);
                    while (DB::table('users')->where('username', $username)->exists()) {
                        $i += 1;
                        if ($i <= 2) {
                            $username = substr(Str::lower($firstname), 0, $i).Str::lower($apellido[0]);
                        }else{
                            $j = $i-1;
                            $username = substr(Str::lower($firstname), 0, 2).Str::lower($apellido[0]).$j;
                        }
                    }

                    $user = new User();
                    $user->email = trim(Str::lower($request->email));
                    $user->identification_document = $cedula;
                    $user->active = isset($request->active) ? $request->active : 1;
                    $user->username = $username;
                    $user->password = Hash::make($ci_sin_formato);
                    $user->assignRole($request->role);
                    $user->save();

                    return response()->json([
                        "ok" => true,
                        "message" => "Registro de Usuario Exitoso.",
                        "username" => $user->username,
                        "password" => $ci_sin_formato,
                    ], 200);
                }
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'identification_document' => 'required|string|min:6',
            'role' => 'required',
            'active' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Edición de Usuario No Exitosa, Error en la Validacion de Datos Recibidos.',
                'errors' => $validator->errors(),
            ], 422);
        }else{
            if (User::where('id',$id)->exists()) {
                $user = User::find($id);

                // VALIDANDO EMAIL
                trim(Str::lower($request->email));
                if ($request->email != $user->email) {
                    if (DB::table('users')->where('email', $request->email)->exists()) {
                        return response()->json([
                            "ok" => false,
                            "message" => "Registro de Usuario No Exitoso, el email suministrado ya existe.",
                        ], 422);
                    }
                }
                $user->email = $request->email;

                // VALIDANDO STATUS DE ACTIVIDAD
                if ($request->active != $user->active) {
                    $user->active = $request->active;
                }

                //GENERANDO CEDULA VALIDA
                if ($user->identification_document != $request->identification_document) {
                    $letra = Str::upper(substr(trim($request->identification_document), 0, 1));
                    if ($letra == 'V' || $letra == 'E' || $letra == 'J') {
                        $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification_document);
                        $cedula = $letra."-".$ci_sin_formato;
                    }else{
                        $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification_document);
                        $cedula = "V-".$ci_sin_formato;
                    }
                }else{
                    $cedula = $request->identification_document;
                }

                if (DB::table('users')->where('identification_document', $cedula)->exists()) {
                    return response()->json([
                        "ok" => false,
                        "message" => "La Cédula de Identidad suministrada ya existe.",
                    ], 422);
                }else{
                    $user->identification_document = $cedula;
                }
        
                $user->update();

                $role = $user->getRoleNames();

                if (sizeof($role) == 0) {
                    $user->assignRole($request->role);
                    unset($role);
                    $role = $user->getRoleNames();

                    return response()->json([
                        "ok" => true,
                        'message' => "Usuario Actualizado Correctamente.",
                        "role" => $role[0],
                        "data" => $user,
                    ], 200);
                }else{
                    if ($request->role != $role[0]) {
                        $user->removeRole($role[0]);
                        $user->assignRole($request->role);
                        unset($role);
                        $role = $user->getRoleNames();
                    }

                    return response()->json([
                        "ok" => true,
                        'message' => "Usuario Actualizado Correctamente.",
                        "role" => $role[0],
                        "data" => $user,
                    ], 200);
                }
            }else{
                return response()->json([
                    "ok" => false,
                    "message" => "El usuario no se encuentra Registrado",
                ], 401);
            }
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

        $id = $request->user()->id;
        $user = User::find($id);
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->update();

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

    public function userExists(Request $request) {
        $validator = Validator::make($request->all(), [
            'identification_document' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Verifique la Cédula Suministrada.',
                'errors' => $validator->errors(),
            ], 422);
        }else{
            //GENERANDO CEDULA VALIDA
            $letra = Str::upper(substr(trim($request->identification_document), 0, 1));
            if ($letra == 'V' || $letra == 'E' || $letra == 'J') {
                $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification_document);
                $cedula = $letra."-".$ci_sin_formato;
                $request->identification_document = $cedula;
            }else{
                $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification_document);
                $cedula = "V-".$ci_sin_formato;
                $request->identification_document = $cedula;
            }

            if (DB::table('users')->where('identification_document', $cedula)->exists()) {
                // EL USUARIO SI ESTA REGISTRADO ENTONCES SE PROCEDE A GENERAR UNA EXCEPCION
                return response()->json([
                    "ok" => false,
                    "message" => "La Cédula de Identidad: ".$cedula." ya existe.",
                ], 422);
            }else{
                // EL USUARIO NO ESTA REGISTRADO ENTONCES PROCEDO A REGISTRAR
                self::register($request);

                return response()->json([
                    "ok" => true,
                    "message" => "La Cédula de Identidad: ".$cedula." NO está Registrada.",
                ], 200);
            }
        }
    }

    public function deleteUser($id) {
        $user = User::find($id);
        
        if (isset($user->id)) {
            $role = $user->getRoleNames();
            $user->removeRole($role[0]);
            $user->delete();

            return response()->json([
                "ok" => true,
                "message" => "Usuario Eliminado Exitosamente.",
            ], 200);
        }else{
            return response()->json([
                "ok" => false,
                "message" => "Usuario No existe.",
            ]);
        }
    }
}
