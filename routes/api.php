<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::group( ['middleware' => ['auth:sanctum']], function() {
    Route::get('user-profile', [UserController::class, 'userProfile']);
    Route::put('edit-user-profile/{id}', [UserController::class, 'edituserProfile']);
    Route::get('logout', [UserController::class, 'logout']);
    Route::post('import-users', [UserController::class,'excel_UsersImports'])->name('excel-import-users');
    Route::get('export-users-excel', [UserController::class,'excel_UsersExports'])->name('excel-export-users');
    Route::get('export-users-pdf', [UserController::class,'pdf_UsersExports']);
});

//Esta ruta viene por defecto
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
