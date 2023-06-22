<?php

namespace App\Http\Controllers;

use App\Mail\UpdateEmailMail;
use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class UpdateProfileController extends Controller
{
    public function update(Request $request): Response | JsonResponse
    {
        App::setLocale($request->locale);
        $authUser = auth()->user();
        $user = User::firstWhere('email', $authUser->email);

        if ($request->name !== null) {
            $user->name = $request->name;
            $user->save();
        }
        if ($request->thumbnail !== null) {
            $file = request()->file('thumbnail')->store('images', 'public');
            $user->thumbnail = $file;
            $user->save();
        }
        if ($request->password !== null) {
            $attributes = $request->validate([
                'password' => 'required'
            ]);
            $isSamePassword = Hash::check($attributes['password'], $user->password);
            if ($isSamePassword) {
                return response()->json(['message' => __('response.same_password')], 422);
            }
            $user->password = $request->password;
            $user->save();
        }

        if ($request->email !== null) {
            $attributes = $request->validate([
                'email' => 'required|unique:users,email'
            ]);
            $token = sha1($attributes['email']);

            $expires = now();
            $userData = $user;
            $userData['email'] = $attributes['email'];

            Mail::to($attributes['email'])->send(new UpdateEmailMail($userData, $expires, $token));
        }

        return response(__('response.profile_updated'), 200);
    }

    public function UpdateEmail(Request $request): JsonResponse
    {
        $user = User::where('id', $request->user_id);

        if (sha1($request->email) === $request->update_token) {
            $user->update([
                'email' => $request->email,
                'email_verified_at' => now(),
            ]);
        } else {
            return response()->json('something went wrong!');
        }

        return response()->json(['message' => __('response.user_verified')], 201);
    }
}
