<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use OTPHP\TOTP;

class OtpController extends Controller
{
    protected $totp;
    public function __construct()
    {
        $this->totp = TOTP::create(
            env('OTP_KEY'), // New TOTP with custom secret
            60,                 // The period (int)
            'sha512',           // The digest algorithm (string)
            6                   // The number of digits (int)
        );
    }
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
        return response($this->create(), 200);
    }
    /**
     * Trigger to verify OTP
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function verify($password)
    {
        return $this->totp->verify($password);
    }
    /**
     * Trigger to create otp
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function create()
    {
        return $this->totp->now();
    }
}
