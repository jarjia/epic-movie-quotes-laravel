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
            $fileName = request()->file('thumbnail')->getClientOriginalName();
            $filePath = request()->file('thumbnail')->storeAs('images', $fileName, 'public');
            $user->thumbnail = config('app.url') . ':8000/storage/' . $filePath;
            $user->save();
        }
        if ($request->password !== null) {
            $user->password = $request->password;
            $user->save();
        }

        return response('Profile updated!', 200);
    }
}
