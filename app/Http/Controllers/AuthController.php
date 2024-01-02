<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequests\AuthRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(AuthRequest $request): Response
    {
        App::setLocale($request->locale);
        $attributes = $request->validated();
        $user = null;
        $credentials = [
            'password' => $attributes['password']
        ];

        if (filter_var($attributes['user'], FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $attributes['user'];
            $user = User::firstWhere('email', $attributes['user']);
        } else {
            $credentials['name'] = $attributes['user'];
            $user = User::firstWhere('name', $attributes['user']);
        }

        if ($user === null) {
            return response(['user' => __('response.user_login_error')], 401);
        } else {
            if ($user->email_verified_at === null) {
                return response(['user' => __('response.user_not_verified')], 401);
            }
        }

        if (Auth::attempt($credentials, $attributes['remember_me'])) {
            $user = User::firstWhere('id', auth()->user()->id);
            $user->google_id = null;
            $user->save();

            return response('User Logged in', 200);
        }

        return response(['user' => __('response.user_login_error')], 401);
    }

    public function index()
    {
        $users = User::where('id', '!=', auth()->user()->id)->whereNotNull('email_verified_at')->get();

        foreach ($users as $user) {
            $friends = $user->friends;
            foreach ($friends as $friend) {
                $status = $friend->pivot->status;
            }
        }

        $transformedUsers = new UsersResource($users);

        return response()->json($transformedUsers);
    }

    public function user(): JsonResponse
    {
        $user = auth()->user();

        $transformedUser = new UserResource($user);

        return response()->json($transformedUser);
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
}
