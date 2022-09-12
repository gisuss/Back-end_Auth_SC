<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SCAuthController;
use App\Http\Controllers\Api\newResetPasswordController;
use App\Http\Controllers\Api\newForgotPasswordController;

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
Route::post('login', [SCAuthController::class, 'login']);
Route::put('verify-email', [UserController::class, 'verifyuseremail']);
Route::post('forgot-password', [newForgotPasswordController::class, 'forgotPassword']);
Route::put('reset-password', [newResetPasswordController::class, 'resetPassword']);

Route::group( ['middleware' => ['auth:sanctum']], function() {
    Route::get('user-profile', [UserController::class, 'userProfile']);
    Route::put('edit-user-profile/{id}', [UserController::class, 'edituserProfile']);
    Route::delete('delete-user/{id}', [UserController::class, 'deleteUser']);
    Route::put('change-password', [UserController::class, 'changePassword']);
    Route::post('logout', [SCAuthController::class, 'logout']);
    Route::get('refresh-token', [SCAuthController::class, 'refresh']);
    Route::get('userExists', [UserController::class, 'userExists']); //EN PROCESO
});
