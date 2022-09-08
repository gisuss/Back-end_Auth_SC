<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    // protected function sendResetLinkResponse($response)
    // {
    //     if (request()->header('Content-Type') == 'application/json') {
    //         return response()->json(['success' => 'Recovery email sent.']);
    //     }
    //     return back()->with('status', trans($response));
    // }
    
    // protected function sendResetLinkFailedResponse(Request $request, $response)
    // {
    //     if (request()->header('Content-Type') == 'application/json') {
    //         return response()->json(['error' => 'Oops something went wrong.']);
    //     }
    //     return back()->withErrors(
    //         ['email' => trans($response)]
    //     );
    // }
}
