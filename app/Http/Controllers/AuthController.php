<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function log_in(Request $request)
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

    public function get_user_data()
    {
        return auth()->user();
    }

    public function logout()
    {
        Auth::guard('web')->logout();
    }
}
