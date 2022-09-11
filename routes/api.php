<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SCAuthController;
use App\Http\Controllers\Api\VerifyEmailController;
use App\Http\Controllers\Api\newResetPasswordController;
use App\Http\Controllers\Api\newForgotPasswordController;
use Illuminate\Http\Request;

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

// // Verify email
// Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])
//     ->middleware(['auth:sanctum', 'signed'])
//     ->name('verification.verify');

// // Resend link to verify email
// Route::post('/email/verify/resend', function (Request $request) {
//     $request->user()->sendEmailVerificationNotification();
//     return back()->with('message', 'Verification link sent!');
// })->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');


Route::post('forgot-password', [newForgotPasswordController::class, 'forgotPassword']);
// Route::post('verify_token/{token}', [newResetPasswordController::class, 'verifyPin']);
Route::put('reset-password', [newResetPasswordController::class, 'resetPassword']);


Route::group( ['middleware' => ['auth:sanctum']], function() {
    Route::get('user-profile', [UserController::class, 'userProfile']);
    Route::post('edit-user-profile/{id}', [UserController::class, 'edituserProfile']);
    Route::post('delete-user/{id}', [UserController::class, 'deleteUser']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('logout', [SCAuthController::class, 'logout']);
    Route::get('refresh-token', [SCAuthController::class, 'refresh']);
    Route::get('userExists', [UserController::class, 'userExists']); //EN PROCESO
});
