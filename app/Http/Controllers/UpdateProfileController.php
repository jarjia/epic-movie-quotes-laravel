<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequests\UpdateEmailRequest;
use App\Http\Requests\AuthRequests\UpdateprofileRequest;
use App\Mail\UpdateEmailMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UpdateProfileController extends Controller
{
    public function update(UpdateprofileRequest $request): Response | JsonResponse
    {
        $authUser = auth()->user();
        $user = User::firstWhere('email', $authUser->email);
        $attributes = $request->validated();
        App::setLocale($attributes['locale']);

        if (isset($attributes['password']) && isset($attributes['email']) && auth()->user()->google_id === null) {
            $isSamePassword = Hash::check($attributes['password'], $user->password);
            if ($isSamePassword) {
                return response()->json(['password' => __('response.same_password')], 422);
            }
        }

        if (isset($attributes['password'])) {
            $isSamePassword = Hash::check($attributes['password'], $user->password);
            if ($isSamePassword) {
                return response()->json(['password' => __('response.same_password')], 422);
            }

            $user->password = $attributes['password'];
            $user->save();
        }

        if (isset($attributes['email']) && auth()->user()->google_id === null) {
            $token = sha1($attributes['email']);

            $expires = now();
            $userData = (object)[
                'id' => $user->id,
                'name' => $user->name,
                'email' => $attributes['email']
            ];

            Mail::to($attributes['email'])->send(new UpdateEmailMail($userData, $expires, $token));
        }

        if (isset($attributes['name'])) {
            $user->name = $attributes['name'];
            $user->save();
        }
        if (isset($attributes['thumbnail'])) {
            $file = request()->file('thumbnail')->store('images', 'public');
            $user->thumbnail = $file;
            $user->save();
        }

        return response(__('response.profile_updated'), 200);
    }

    public function UpdateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $user = User::where('id', $attributes['user_id']);

        if (sha1($attributes['email']) === $attributes['update_token']) {
            $user->update([
                'email' => $attributes['email'],
                'email_verified_at' => now(),
            ]);
        } else {
            return response()->json('Something went wrong!');
        }

        return response()->json(['message' => __('response.user_verified')], 201);
    }
}
