<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of unread notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->unreadNotifications();

        return response()->json([
            'data' => $notifications,
            'message' => 'Vos notifications non lues.',
        ]);
    }

    
    /**
     * Mark a notification as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marquée comme lue.',
        ]);
    }
}
