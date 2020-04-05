<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Cart;
use App\Address;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Hash;
use App\Rules\MatchOldPassword;

class AuthController extends Controller
{
    public function register (Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
    
        $request['password']=Hash::make($request['password']);
        $user = User::create($request->toArray());
        $cart = Cart::create([ 'user_id' => $user->id, 'checkout_id' => $request->checkoutId]);

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
    
        return response($response, 200);
    
    }

    public function login (Request $request)
    {

        $user = User::where('email', $request->email)->first();
    
        if ($user) {
    
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = "Password missmatch";
                return response($response, 422);
            }
    
        } else {
            $response = 'User does not exist';
            return response($response, 500);
        }
    
    }

    public function forgotPassword (Request $request)
    {

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $response = Password::broker()->sendResetLink($request->only('email'));
            if ($response == Password::RESET_LINK_SENT) {
                return response($response, 200);
            } else {
                return response($response, 500);
            }
    
        } else {
            $response = 'User does not exist';
            return response($response, 500);
        }
    
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function reset($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
    }

    public function resetPassword (Request $request)
    {

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $data = $request->only('email', 'password', 'password_confirmation', 'token');
            $response = Password::broker()->reset(
                $data, function ($user, $password) {
                    $this->reset($user, $password);
                }
            );
            if ($response == Password::PASSWORD_RESET) {
                return response($response, 200);
            } else {
                return response($response, 500);
            }
    
        } else {
            $response = 'User does not exist';
            return response($response, 500);
        }
    
    }

    public function logout (Request $request)
    {

        $token = $request->user()->token();
        $token->revoke();
    
        $response = 'You have been succesfully logged out!';
        return response($response, 200);
    
    }

    public function me (Request $request)
    {
        $userId = $request->user()->id;
        $user = User::with('cart')->with('address')->findOrFail($userId);
        return response($user, 200);
    
    }

    public function updateMe (Request $request)
    {
        $userId = $request->user()->id;
        $user = User::with('cart')->with('address')->findOrFail($userId);
        if (isset($request->name)) {
            $user->name = $request->name;
        }
        if (isset($request->email)) {
            // TODO
            // generate token and save to EmailChange
            // then email token to user with url to '/confirm-email-change'
        }
        if (isset($request->password)) {
            $request->validate([
                'password' => ['required', new MatchOldPassword],
                'newPassword' => ['required'],
                'confirmNewPassword' => ['same:newPassword'],
            ]);
            $user->update(['password'=> Hash::make($request->newPassword)]);
        }
        if (isset($request->address1)
            || isset($request->address2)
            || isset($request->city)
            || isset($request->province)
            || isset($request->zip)) {
            if (isset($user->address_id)) {
                $address = Address::findOrFail($user->address_id);
                $address->fill((array) json_decode($request->getContent()));
                $address->save();
            } else {
                $address = new Address;
                $address->fill((array) json_decode($request->getContent()));
                $address->phone = isset($user->phone) ? $user->phone : "";
                $address->save();
                $user->address_id = $address->id;
            }
        }
        $user->save();
        $user = User::with('cart')->with('address')->findOrFail($userId);
        return response($user, 200);
    
    }

    public function checkEmailExists (Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $exists = isset($user);
        if ($exists && null !== $request->user()) {
            if ($request->user()->email == $user->email) {
                $exists = false;
            }
        }
        return response(['exists' => $exists], 200);
    
    }

    public function confirmEmailChange (Request $request)
    {
        // TODO
        // $emailChange = EmailChange::where('token', $request->token)->first();
        // $user = User::findOrFail($emailChange->user_id);
        // $user->email = $emailChange->email;
        // $user->save();
        // return Redirect::to(env('CLIENT_URL').'/account?toast=email-changed');
    
    }
}
