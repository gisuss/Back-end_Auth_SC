<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Jobs\SendEmails;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
                "ok" => false,
                "message" => "Error en la Validacion de Datos Recibidos.",
                "errors" => $validator->errors(),
            ], 422);
        }else{
            $email = trim(Str::lower($request->email));
            if (DB::table('users')->where('email', $email)->exists()) {
                return response()->json([
                    "ok" => false,
                    "message" => "El email suministrado ya existe.",
                ], 422);
            }else{
                $first_name = self::eliminar_tildes($request->first_name);
                $last_name = self::eliminar_tildes($request->last_name);
                $first_name = ucwords(Str::lower(trim(preg_replace('/[^a-zA-Z" "]/i', '', $first_name))));
                $last_name = ucwords(Str::lower(trim(preg_replace('/[^a-zA-Z" "]/i', '', $last_name))));
                if (($first_name == "") || ($last_name == "")) {
                    return response()->json([
                        "ok" => false,
                        "message" => "Verifique los campos de Nombre y Apellido.",
                    ], 422);
                }else{
                    //GENERANDO CEDULA VALIDA
                    $letra = Str::upper(substr(trim($request->identification), 0, 1));
                    if ($letra == 'V' || $letra == 'E' || $letra == 'J') {
                        $ci_sin_formato = preg_replace('/[^0-9]/i', '', $request->identification);
                        if ($ci_sin_formato == "") {
                            return response()->json([
                                "ok" => false,
                                "message" => "El formato de la Cédula de Identidad suministrada es erróneo.",
                            ], 422);
                        }else{
                            $cedula = $letra."-".$ci_sin_formato;
                            if (DB::table('users')->where('identification', $cedula)->exists()) {
                                return response()->json([
                                    "ok" => false,
                                    "message" => "La Cédula de Identidad suministrada ya existe.",
                                ], 422);
                            }else{
                                //GENERANDO USERNAME
                                $i = 1;
                                $nombre = explode(" ", $first_name);
                                $first_name = Str::lower($first_name);
                                $last_name = Str::lower($last_name);
                                $apellido = explode(" ", $last_name);
                                $username = substr($first_name, 0, $i).$apellido[0];
                                while (DB::table('users')->where('username', $username)->exists()) {
                                    $i += 1;
                                    if ($i <= 2) {
                                        $username = substr($first_name, 0, $i).$apellido[0];
                                    }else{
                                        $j = $i-1;
                                        $username = substr($first_name, 0, 2).$apellido[0].$j;
                                    }
                                }

                                $user = new User();
                                $user->email = $email;
                                $user->identification = $cedula;
                                $user->active = isset($request->active) ? $request->active : 1;
                                $user->username = $username;
                                $user->password = Hash::make($ci_sin_formato);
                                $r = isset($request->role) ? trim(Str::lower($request->role)) : "student";

                                if ($r == "coordinador" || $r == "coordinator"):
                                    $r = "coordinator";
                                elseif ($r == "tutor"):
                                    $r = "tutor";
                                elseif ($r == "estudiante" || $r == "student"):
                                    $r = "student";
                                else:
                                    $r = "student";
                                endif;

                                $user->assignRole($r);
                                $user->save();

                                // $token = Str::random(64);
                                dispatch(new SendEmails($nombre[0], $username, $ci_sin_formato, $user['email']))->delay(now()->addSeconds(10));
                                // Mail::to($user->email)->send(new RegisterMail($nombre[0], $username, $ci_sin_formato));

                                return response()->json([
                                    "ok" => true,
                                    "message" => "Registro de Usuario Exitoso.",
                                    "username" => $user->username,
                                    "password" => $ci_sin_formato,
                                ], 200);
                            }
                        }
                    }else{
                        return response()->json([
                            "ok" => false,
                            "message" => "Verifique el campo de cédula.",
                        ], 422);
                    }
                }
            }
        }
    }

    public function registerMasivo(RegisterMassiveRequests $request) {
        $validated = $request->validated();

        $usersInput = $validated['users'];
        $usersOutput = [];
        $userRoles = [];
        $usernames = [];
        $Identifications = [];

        $users = DB::table('users')->select('*')->get();
        foreach ($users as $user) {
            $usernames[] = $user->username;
            $Identifications[] = $user->identification;
        }

        foreach ($usersInput as $datum) {
            $first_name = self::eliminar_tildes($datum['first_name']);
            $last_name = self::eliminar_tildes($datum['last_name']);
            $first_name = ucwords(Str::lower(trim(preg_replace('/[^a-zA-Z" "]/i', '', $first_name))));
            $last_name = ucwords(Str::lower(trim(preg_replace('/[^a-zA-Z" "]/i', '', $last_name))));
            $email = trim(Str::lower($datum['email']));
            if (($first_name != "") and ($last_name != "") and (DB::table('users')->where('email', $email)->doesntExist())) {
                //GENERANDO CEDULA VALIDA
                $letra = Str::upper(substr(trim($datum['identification']), 0, 1));
                if ($letra == 'V' || $letra == 'E' || $letra == 'J') {
                    $ci_sin_formato = preg_replace('/[^0-9]/i', '', $datum['identification']);
                    $cedula = $letra."-".$ci_sin_formato;
                    if (($ci_sin_formato != "") and (in_array($cedula, $Identifications) == false)) {
                        $Identifications[] = $cedula;
                        //GENERANDO USERNAME
                        $i = 1;
                        $nombre = explode(" ", $first_name);
                        $first_name = Str::lower($first_name);
                        $last_name = Str::lower($last_name);
                        $apellido = explode(" ", $last_name);
                        $username = substr($first_name, 0, $i).$apellido[0];
                        while (in_array($username, $usernames)) {
                            $i += 1;
                            if ($i <= 2) {
                                $username = substr($first_name, 0, $i).$apellido[0];
                            }else{
                                $j = $i-1;
                                $username = substr($first_name, 0, 2).$apellido[0].$j;
                            }
                        }
                        $usernames[] = $username;
    
                        $user = [];
                        $user['email'] = $email;
                        $user['identification'] = $cedula;
                        $user['username'] = $username;
                        $user['password'] = hash::make($ci_sin_formato);
                        
                        $usersOutput[] = $user;
    
                        $r = (isset($datum['role'])) ? trim(Str::lower($datum['role'])) : "student";
    
                        if ($r == "coordinador" || $r == "coordinator"):
                            $r = "coordinator";
                            array_push($userRoles, $r);
                        elseif ($r == "tutor"):
                            array_push($userRoles, $r);
                        elseif ($r == "estudiante" || $r == "student"):
                            $r = "student";
                            array_push($userRoles, $r);
                        else:
                            $r = "student";
                            array_push($userRoles, $r);
                        endif;
    
                        // $token = Str::random(64);
                        // Mail::to($email)->send(new RegisterMail($nombre[0], $username, $ci_sin_formato));
                        dispatch(new SendEmails($nombre[0], $username, $ci_sin_formato, $email))->delay(now()->addSeconds(10));
                    }
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
                    "ok" => 1,
                    "message" => "Registro de Usuarios Completamente Exitoso.",
                    "users" => $usersOutput,
                ], 200);
            }else{
                if ($b == 0) {
                    return response()->json([
                        "ok" => 2,
                        "message" => "¡Ops! Algo ha salido mal, no se pudo registrar ningún usuario.",
                    ]);
                }else{
                    if ($b >= 1) {
                        return response()->json([
                            "ok" => 3,
                            "message" => "Se registraron ".$b." / ".$a." nuevos usuarios.",
                            "users" => $usersOutput,
                        ], 200);
                    }
                }
            }
        }else{
            return response()->json([
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
            'email' => 'required|email|string|unique:users',
            'identification' => 'required|string|min:6',
            'role' => 'nullable',
            'active' => 'nullable'
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
                $email = trim(Str::lower($request->email));
                if ($email != $user->email) {
                    if (DB::table('users')->where('email', $email)->exists()) {
                        return response()->json([
                            "ok" => false,
                            "message" => "El email suministrado ya existe.",
                        ], 422);
                    }
                }
                $user->email = $email;

                // VALIDANDO STATUS DE ACTIVIDAD
                if (isset($request->active) || ($request->active != $user->active)) {
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
                    if ($ci_sin_formato == "") {
                        return response()->json([
                            "ok" => false,
                            "message" => "La Cédula de Identidad suministrada es errónea.",
                        ], 422);
                    }
                    $user->identification = $cedula;
                }

                if (isset($request->role)) {
                    $r = trim(Str::lower($request->role));
                    if ($r == "coordinador" || $r == "coordinator"):
                        $r = "coordinator";
                    elseif ($r == "tutor"):
                        $r = "tutor";
                    elseif ($r == "estudiante" || $r == "student"):
                        $r = "student";
                    else:
                        $r = "student";
                    endif;
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
                        $user->assignRole($r);
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

    public function eliminar_tildes($cadena) {

        //Codificamos la cadena en formato utf8 en caso de que nos de errores
        // $cadena = utf8_encode($cadena);
    
        //Ahora reemplazamos las letras
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );
    
        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );
    
        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );
    
        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );
    
        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );
    
        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );
    
        return $cadena;
    }
}
