<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function unread()
    {
        $userId = Auth::id();

        $notifications = Notification::query()
            ->where('user_id', $userId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->limit(20)
            ->get(['id', 'document_id', 'message', 'sent_at', 'status']);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, int $id)
    {
        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $notification->update([
            'status' => 'read',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }
}
