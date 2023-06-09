<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequests\PasswordEmailRecoverRequest;
use App\Http\Requests\AuthRequests\PasswordRecoverRequest;
use App\Mail\PasswordRecoverMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendEmail(PasswordEmailRecoverRequest $request): JsonResponse
    {
        App::setLocale($request->locale);
        $attributes = $request->validated();

        $token = sha1($attributes['email']);
        $user = User::firstWhere('email', $attributes['email']);
        $expires = now();

        if ($user === null) {
            return response()->json(__('response.email_not_found'), 401);
        } else {
            Mail::to($user->email)->send(new PasswordRecoverMail($user, $expires, $token));

            return response()->json(['message' => __('response.email_recover_sent')]);
        }
    }
    public function reset(PasswordRecoverRequest $request): JsonResponse
    {
        App::setLocale($request->locale);
        $attributes = $request->validated();

        $user = User::firstWhere('email', $attributes['email']);
        $isSamePassword = Hash::check($attributes['password'], $user->password);

        if ($isSamePassword) {
            return response()->json(['message' => __('response.same_password')], 422);
        }

        if (sha1($user->email) === $attributes['recover_token']) {
            $user->password = $attributes['password'];
            $user->save();
        } else {
            return response()->json(['message' => __('response.error_password')]);
        }

        return response()->json(['message' => __('response.success_password')]);
    }
}
