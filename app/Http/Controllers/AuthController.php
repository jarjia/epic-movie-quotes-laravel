<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): Response
    {
        App::setLocale($request->locale);
        $remember = $request->boolean('remember_me');
        $attributes = $request->only('password');
        $usernameOrEmail = $request->input('user');

        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            $attributes['email'] = $usernameOrEmail;
        } else {
            $attributes['name'] = $usernameOrEmail;
        }

        if (Auth::attempt($attributes, $remember)) {
            $user = User::firstWhere('id', auth()->user()->id);
            $user->google_id = null;
            $user->save();

            return response('User Logged in', 200);
        }

        return response(__('response.user_login_error'), 401);
    }

    public function user(): JsonResponse
    {
        $user = auth()->user();
        $userImage = '';
        if (strpos($user->thumbnail, 'assets') === 0) {
            $userImage = asset($user->thumbnail);
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

        return response()->json($user);;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
}
