<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\Services\SocialFacebookAccountService;
use App\Services\SocialGoogleAccountService;

class SocialAuthController extends Controller
{
    private function createToken($user) {
        return $user->createToken('Laravel Password Grant Client')->accessToken;
    }
    /**
     * Create a redirect method to facebook api.
     *
     * @return void
     */
    public function redirectFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }
    /**
     * Create a redirect method to google api.
     *
     * @return void
     */
    public function redirectGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */
    public function callbackFacebook(SocialFacebookAccountService $service)
    {
        $user = $service->createOrGetUser(Socialite::driver('facebook')->stateless()->user());
        $response = ['token' => $this->createToken($user)];
        return response($response, 200);
    }
    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */
    public function callbackGoogle(SocialGoogleAccountService $service)
    {
        $user = $service->createOrGetUser(Socialite::driver('google')->stateless()->user());
        $response = ['token' => $this->createToken($user)];
        return response($response, 200);
    }
}
