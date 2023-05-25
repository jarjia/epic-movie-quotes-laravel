<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequests\VerificationRequest;
use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $request->password,
        ]);

        $token = sha1($user->email);

        Mail::to($user->email)->send(new VerificationEmail($user, $token));

        return response()->json(['message' => 'User registered successfully!'], 201);
    }

    public function verify(VerificationRequest $request): JsonResponse
    {
        $attributes = $request->validate([
            'email' => 'required',
            'token' => 'required'
        ]);

        $user = User::where('email', $attributes['email']);

        if (sha1($attributes['email']) === $attributes['token']) {
            $user->update([
                'email_verified_at' => now(),
            ]);
        } else {
            return response()->json('something went wrong!');
        }

        return response()->json(['message' => 'Email verified successfully!'], 201);
    }
}
