<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\RegisterMail;
use App\Mail\massiveRegisterMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Requests\RegisterMassiveRequests;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:4',
            'last_name' => 'required|string|min:4',
            'email' => 'required|string|email|unique:users',
            'identification' => 'required|string|min:6',
            'role' => 'nullable',
            'active' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Registro de Usuario No Exitoso, Error en la Validacion de Datos Recibidos.',
                'errors' => $validator->errors(),
            ], 422);
        }else{
            if (DB::table('users')->where('email', trim(Str::lower($request->email)))->exists()) {
                return response()->json([
                    "ok" => false,
                    "message" => "Registro de Usuario No Exitoso, el email suministrado ya existe.",
                ], 422);
            }else{
                $first_name = ucwords(Str::lower(trim(preg_replace('/[^a-z" "]/i', '', $request->first_name), ' ')));
                $last_name = ucwords(Str::lower(trim(preg_replace('/[^a-z" "]/i', '', $request->last_name), ' ')));
                if (($first_name == "") || ($last_name == "")) {
                    return response()->json([
                        "ok" => false,
                        "message" => "Registro de Usuario No Exitoso, Verifique los campos de Nombre y Apellido.",
                    ], 422);
                }else{
                    //GENERANDO CEDULA VALIDA
                    $letra = Str::upper(substr(trim($request->identification), 0, 1));
                    if ($letra == 'V' || $letra == 'E' || $letra == 'J') {
                        $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification);
                        $cedula = $letra."-".$ci_sin_formato;
                    }else{
                        $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification);
                        $cedula = "V-".$ci_sin_formato;
                    }
                    
                    if (DB::table('users')->where('identification', $cedula)->exists()) {
                        return response()->json([
                            "ok" => false,
                            "message" => "Registro de Usuario No Exitoso, la Cédula de Identidad suministrada ya existe.",
                        ], 422);
                    }else{
                        if ($ci_sin_formato == "") {
                            return response()->json([
                                "ok" => false,
                                "message" => "Registro de Usuario No Exitoso, la Cédula de Identidad suministrada es errónea.",
                            ], 422);
                        }else{
                            //GENERANDO USERNAME
                            $i = 1;
                            $apellido = explode(" ", $last_name);
                            $username = substr(Str::lower($first_name), 0, $i).Str::lower($apellido[0]);
                            while (DB::table('users')->where('username', $username)->exists()) {
                                $i += 1;
                                if ($i <= 2) {
                                    $username = substr(Str::lower($first_name), 0, $i).Str::lower($apellido[0]);
                                }else{
                                    $j = $i-1;
                                    $username = substr(Str::lower($first_name), 0, 2).Str::lower($apellido[0]).$j;
                                }
                            }

                            $user = new User();
                            $user->email = trim(Str::lower($request->email));
                            $user->identification = $cedula;
                            $user->active = isset($request->active) ? $request->active : 1;
                            $user->username = $username;
                            $user->password = Hash::make($ci_sin_formato);
                            $r = isset($request->role) ? $request->role : "student";
                            $user->assignRole($r);
                            $user->save();

                            $token = Str::random(64);
                            Mail::to($user->email)->send(new RegisterMail($username, $ci_sin_formato, $token));

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
        }
    }

    public function registerMasivo(RegisterMassiveRequests $request) {
        $validated = $request->validated();

        $usersInput = $validated['users'];
        $usersOutput = [];
        $userErrors = [];
        $userRoles = [];
        $pos = 1;

        foreach ($usersInput as $datum) {
            $pos += 1;
            $first_name = ucwords(Str::lower(trim(preg_replace('/[^a-z" "]/i', '', $datum['first_name']), ' ')));
            $last_name = ucwords(Str::lower(trim(preg_replace('/[^a-z" "]/i', '', $datum['last_name']), ' ')));
            if (($first_name == "") || ($last_name == "") || (DB::table('users')->where('email', trim(Str::lower($datum['email'])))->exists())) {
                //ALMACENAR INDICE DE USUARIO NO AGREGADO
                $userErrors[] = $pos;
            }else{
                //GENERANDO CEDULA VALIDA
                $letra = Str::upper(substr(trim($datum['identification']), 0, 1));
                if ($letra == 'V' || $letra == 'E' || $letra == 'J') {
                    $ci_sin_formato = preg_replace('/[^0-9]/i', '', $datum['identification']);
                    $cedula = $letra."-".$ci_sin_formato;
                }else{
                    $ci_sin_formato = preg_replace('/[^0-9]/i', '', $datum['identification']);
                    $cedula = "V-".$ci_sin_formato;
                }

                if (($ci_sin_formato == "") || (DB::table('users')->where('identification', $cedula)->exists())) {
                    //ALMACENAR INDICE DE USUARIO NO AGREGADO
                    $userErrors[] = $pos;
                }else{
                    //GENERANDO USERNAME
                    $i = 1;
                    $apellido = explode(" ", $last_name);
                    $username = substr(Str::lower($first_name), 0, $i).Str::lower($apellido[0]);
                    while (DB::table('users')->where('username', $username)->exists()) {
                        $i += 1;
                        if ($i <= 2) {
                            $username = substr(Str::lower($first_name), 0, $i).Str::lower($apellido[0]);
                        }else{
                            $j = $i-1;
                            $username = substr(Str::lower($first_name), 0, 2).Str::lower($apellido[0]).$j;
                        }
                    }

                    $user = [];
                    $user['email'] = trim(Str::lower($datum['email']));
                    $user['identification'] = $cedula;
                    $user['username'] = $username;
                    $user['password'] = hash::make($ci_sin_formato);
                    
                    $usersOutput[] = $user;

                    $r = (isset($datum['role'])) ? $datum['role'] : "student";
                    trim(Str::lower($r));

                    if ($r == "coordinador" || $r == "coordinator"):
                        $r = "coordinator";
                        array_push($userRoles, $r);
                    elseif ($r == "tutor"):
                        array_push($userRoles, $r);
                    elseif ($r == "estudiante" || $r == "student"):
                        $r = "student";
                        array_push($userRoles, $r);
                    else:
                        $r = NULL;
                        array_push($userRoles, $r);
                    endif;

                    $token = Str::random(64);
                    Mail::to($user['email'])->send(new RegisterMail($username, $ci_sin_formato, $token));
                }
            }
        }

        $value = User::insert($usersOutput);

        if ($value) {
            //ASIGNACION DE ROLES
            $i = 0;
            foreach ($usersOutput as $element) {
                $currentuser = User::where('email', $element['email'])->first();
                $currentuser->assignRole($userRoles[$i]);
                $i += 1;
            }

            $a = count($usersInput);
            $b = count($usersOutput);

            if ($b == $a) {
                return response()->json([
                    "ok" => true,
                    "message" => "Registro de Usuarios Completamente Exitoso.",
                    "users" => $usersOutput,
                ], 200);
            }else{
                if ($b == 0) {
                    return response()->json([
                        "ok" => false,
                        "message" => "Ha ocurrido un error en la entrada de datos, por favor valide la información.",
                    ]);
                }else{
                    if ($b >= 1) {
                        return response()->json([
                            "ok" => true,
                            "message" => "Se registraron ".$b." / ".$a." nuevos usuarios.",
                            "users" => $usersOutput,
                            "errors" => $userErrors,
                        ], 200);
                    }
                }
            }
        }else{
            return response()->json([
                "ok" => false,
                "message" => "Fatal. Registro de Usuario Fallido.",
            ], 500);
        }
    }

    public function verifyuseremail(Request $request) {
        $token = $request->header('Authorization');
        $check = DB::table('verifyuseremails')->where('token', $token);

        if ($check->exists()) {
            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
            if ($difference > 604800) {
                $mensaje = "Token Expirado. Recuerde que dispone de 7 días para culminar su proceso de verificación de email.";
                $bool = false;
            }else{
                $mensaje = "Token Válido.";
                $bool = true;

                $result = $check->first();
                $email = $result->email;
                $user = User::where('email', $email);
                $user->update([
                    'email_verified_at' => Carbon::now()
                ]);
            }
        }else{
            $mensaje = "Token Inválido.";
            $bool = false;
        }
        
        $check->delete();
        return response()->json([
            'ok' => $bool,
            'message' => $mensaje,
        ]);
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
            'identification' => 'required|string|min:6',
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
                if ($user->identification != $request->identification) {
                    $letra = Str::upper(substr(trim($request->identification), 0, 1));
                    if ($letra == 'V' || $letra == 'E' || $letra == 'J') {
                        $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification);
                        $cedula = $letra."-".$ci_sin_formato;
                    }else{
                        $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification);
                        $cedula = "V-".$ci_sin_formato;
                    }
                }else{
                    $cedula = $request->identification;
                }

                if (DB::table('users')->where('identification', $cedula)->exists()) {
                    return response()->json([
                        "ok" => false,
                        "message" => "La Cédula de Identidad suministrada ya existe.",
                    ], 422);
                }else{
                    $user->identification = $cedula;
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
