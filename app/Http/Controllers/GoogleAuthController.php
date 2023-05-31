<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::firstWhere('email', $googleUser->email);
        if ($user === null) {
            $user = User::Create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
            ]);

            $user->email_verified_at = now();
            $user->save();
        } else {
            $user->name = $googleUser->name;
            $user->save();
        }

        auth()->login($user);

        session()->regenerate();

        return response('User Logged in', 200);
    }
}
