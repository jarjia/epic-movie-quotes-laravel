<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $remember = $request->boolean('remember_me');
        $attributes = $request->only('password');
        $usernameOrEmail = $request->input('user');

        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            $attributes['email'] = $usernameOrEmail;
        } else {
            $attributes['name'] = $usernameOrEmail;
        }

        if (Auth::attempt($attributes, $remember)) {
            return response()->json(Auth::user(), 201);
        }

        return response('User authentication failed, incorrect credentials', 401);
    }

    public function user()
    {
        $user = auth()->user();
        $userImage = '';
        if (strpos($user->thumbnail, "http") === 0) {
            $userImage = $user->thumbnail;
        } else {
            $userImage = asset('storage/' . $user->thumbnail);
        }

        $user = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'thumbnail' => $userImage,
            'google_id' => $user->google_id
        ];

        return $user;
    }

    public function logout()
    {
        Auth::guard('web')->logout();
    }
}
