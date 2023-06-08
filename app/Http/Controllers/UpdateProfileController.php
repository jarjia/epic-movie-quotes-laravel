<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UpdateProfileController extends Controller
{
    public function update(Request $request)
    {
        $authUser = auth()->user();
        $user = User::firstWhere('email', $authUser->email);

        if ($request->name !== null) {
            $user->name = $request->name;
            $user->save();
        }
        if ($request->thumbnail !== null) {
            $file = request()->file('thumbnail')->store('images', 'public');
            $thumbnailPath = config('app.url') . ':8000/storage/' . $file;
            $user->thumbnail = $thumbnailPath;
            $user->save();
        }
        if ($request->password !== null) {
            $user->password = $request->password;
            $user->save();
        }

        return response('Profile updated!', 200);
    }
}
