<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    //
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Old password is incorrect'], 400);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully']);
    }

    public function uploadProfile(Request $request)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = $request->file('image')->store('profiles', 'public');

        $user = auth()->user();
        $user->image = $path;
        $user->save();

        return response()->json(['success' => true, 'path' => asset('storage/' . $path)]);
    }


}
