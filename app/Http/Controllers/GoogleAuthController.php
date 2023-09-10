<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect(): string
    {
        return Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
    }

    public function callback(): Response
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::firstWhere('email', $googleUser->email);

        $imageUrl = $googleUser->avatar;

        $response = Http::get($imageUrl);

        $profile = basename($imageUrl) . '.png';

        if ($response->successful()) {
            Storage::disk('public')->put('images/' . $profile, $response->body());
        }

        if ($user === null) {
            $user = User::Create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->getId(),
                'thumbnail' => 'images/' . $profile
            ]);

            $user->email_verified_at = now();
            $user->remember_token = Str::random(60);
            $user->save();
        } elseif($user->remember_token === null) {
            $user->name = $googleUser->name;
            $user->google_id = $googleUser->getId();
            $user->thumbnail = 'images/' . $profile;
            $user->email_verified_at = now();
            $user->remember_token = Str::random(60);
            $user->save();
        } else {
            return response(__('response.user_exists'), 403);
        }

        auth()->login($user);

        session()->regenerate();

        return response(__('response.user_logged'), 200);
    }
}
