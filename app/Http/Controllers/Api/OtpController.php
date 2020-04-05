<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class OtpController extends Controller
{
    /**
     * Trigger to send OTP to user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $user = User::find($request->user()->id);
        // TODO
        // send otp to $user->phone
        return response(200);
    }
}
