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
        $request->validate([
            'email' => 'required'
        ]);

        $token = sha1($request->email);
        $user = User::firstWhere('email', $request->email);

        Mail::to($user->email)->send(new PasswordRecoverMail($user, $token));

        return response()->json(['message' => 'Password recover email sent successfuly!']);
    }
    public function reset(PasswordRecoverRequest $request): JsonResponse
    {
        $request->validate([
            'email' => 'required',
            'token' => 'required',
            'password' => 'required'
        ]);

        $user = User::firstWhere('email', $request->email);

        if (sha1($user->email) === $request->token) {
            $user->password = $request->password;
            $user->save();
        } else {
            return response()->json(['message' => 'Something went wrong!']);
        }

        return response()->json(['message' => 'Password reseted successfuly!']);
    }
}
