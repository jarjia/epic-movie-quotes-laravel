<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequests\RegisterRequest;
use App\Http\Requests\AuthRequests\VerificationRequest;
use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request): JsonResponse
    {
        $attributes = $request->validated();

        App::setLocale($attributes['locale']);

        $user = User::create([
            'name'      => $attributes['name'],
            'email'     => $attributes['email'],
            'password'  => $attributes['password'],
            'thumbnail' => 'assets/user.png'
        ]);

        $token = sha1($user->email);

        $expires = now();

        Mail::to($user->email)->send(new VerificationEmail($user, $expires, $token));

        return response()->json(['message' => __('response.user_registered')], 201);
    }

    public function verify(VerificationRequest $request): JsonResponse
    {
        App::setLocale($request->locale);
        $attributes = $request->validated();

        $user = User::where('email', $attributes['email']);

        if (sha1($attributes['email']) === $attributes['token']) {
            $user->update([
                'email_verified_at' => now(),
            ]);
        } else {
            return response()->json(['verify' => 'Something went wrong!']);
        }

        return response()->json(['message' => __('response.user_verified')], 201);
    }
}
