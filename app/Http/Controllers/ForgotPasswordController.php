<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequests\PasswordEmailRecoverRequest;
use App\Http\Requests\AuthRequests\PasswordRecoverRequest;
use App\Mail\PasswordRecoverMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendEmail(PasswordEmailRecoverRequest $request): JsonResponse
    {
        $attributes = $request->validated();

        $token = sha1($attributes->email);
        $user = User::firstWhere('email', $attributes->email);
        $expires = now();

        if ($user === null) {
            return response()->json('User with this email could not be found.', 401);
        } else {
            Mail::to($user->email)->send(new PasswordRecoverMail($user, $expires, $token));

            return response()->json(['message' => 'Password recover email sent successfuly!']);
        }
    }
    public function reset(PasswordRecoverRequest $request): JsonResponse
    {
        $attributes = $request->validated();

        $user = User::firstWhere('email', $attributes->email);

        if (sha1($user->email) === $attributes->token) {
            $user->password = $attributes->password;
            $user->save();
        } else {
            return response()->json(['message' => 'Something went wrong!']);
        }

        return response()->json(['message' => 'Password reseted successfuly!']);
    }
}
