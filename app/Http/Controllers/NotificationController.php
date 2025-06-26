<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //Show index page of navigation
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->notifications()->latest()->get()
        );
    }

    //Enable notification
   public function enableNotifications(Request $request)
{
    $request->validate([
        'expo_token' => 'required|string',
    ]);

    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $user->expo_token = $request->expo_token;
    $user->notifications_enabled = $request->enabled_notification;
    $user->save();

    return response()->json(['message' => 'Notification Enabled']);
}

    //Disable notification
    public function disableNotifications(Request $request) {
    $user = auth()->user();
    $user->notification_enabled = "0";
    $user->expo_token = null;
    $user->save();

    return response()->json(['message' => 'Notifications disabled']);
    }

    //Check notification status
    public function status(Request $request, $id) {
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json([
        'enabled_notification' => $user->notification_enabled
    ]);
}



}
