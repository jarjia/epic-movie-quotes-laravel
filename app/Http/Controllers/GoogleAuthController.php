<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

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

        $response = response('User Logged in', 200);

        $response->cookie('laravel_session', session()->getId());
        $response->cookie('XSRF-TOKEN', csrf_token());

        return $response;
    }
}
