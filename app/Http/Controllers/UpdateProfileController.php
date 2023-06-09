<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class UpdateProfileController extends Controller
{
    public function update(Request $request)
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
            $user->password = $request->password;
            $user->save();
        }

        return response(__('response.profile_updated'), 200);
    }
}
