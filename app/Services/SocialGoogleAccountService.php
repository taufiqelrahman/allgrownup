<?php
namespace App\Services;
use App\LinkedSocialAccount;
use App\User;
use Laravel\Socialite\Contracts\User as ProviderUser;
class SocialGoogleAccountService
{
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = LinkedSocialAccount::whereProviderName('google')
            ->whereProviderId($providerUser->getId())
            ->first();
        if ($account) {
            return $account->user;
        } else {
            $account = new LinkedSocialAccount([
                'provider_id' => $providerUser->getId(),
                'provider_name' => 'google'
            ]);
            $user = User::whereEmail($providerUser->getEmail())->first();
            if (!$user) {
                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                    'password' => md5(rand(1,10000)),
                ]);
            }
            $account->user()->associate($user);
            $account->save();
            return $user;
        }
    }
}