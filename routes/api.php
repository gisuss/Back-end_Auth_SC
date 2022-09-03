<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FilesController;
use App\Http\Controllers\Api\CodeCheckController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\ForgotPasswordController;

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

// Route::post('password/email',  ForgotPasswordController::class);
// Route::post('password/code/check', CodeCheckController::class);
// Route::post('password/reset', ResetPasswordController::class);

Route::group( ['middleware' => ['auth:sanctum']], function() {
    Route::get('user-profile', [UserController::class, 'userProfile']);
    Route::post('edit-user-profile/{id}', [UserController::class, 'edituserProfile']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('logout', [UserController::class, 'logout']);
});

Route::prefix('/files')->group(function () {
    Route::post('/import-users', [FilesController::class,'excel_UsersImports'])->name('excel-import-users');
    Route::get('/export-users-excel', [FilesController::class,'excel_UsersExports'])->name('excel-export-users');
    Route::get('/export-users-pdf', [FilesController::class,'pdf_UsersExports']);
});

//Esta ruta viene por defecto
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
