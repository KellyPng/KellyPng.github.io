<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
     public function index()
     {
        $getNotifications = auth()->user()->notifications;

        // Separate unread and read notifications
        $unreadNotifications = $getNotifications->filter(function ($notification) {
            return $notification->unread();
        });

        $readNotifications = $getNotifications->filter(function ($notification) {
            return !$notification->unread();
        });

        // Combine unread and read notifications and sort by creation date
        $notifications = $unreadNotifications->merge($readNotifications)
            ->sortByDesc('created_at');
        return view('notifications.index', compact('notifications'));
     }

    public function getNotifications()
    {
        $notifications = auth()->user()->notifications;
        return response()->json($notifications);
    }

    public function show(string $type,string $id)
    {
        $notification = auth()->user()->notifications->find($id);
        if ($notification->unread()) {
            $notification->markAsRead();
        }
        //dd($notification);
        $view = 'notifications.'.$type;
        return view($view, compact('notification'));
    }

    public function unreadNotificationsCount()
    {
        $unreadCount = auth()->user()->unreadNotifications->count();
        return $unreadCount;
    }


    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = auth()->user()->notifications->find($id);
        $notification->markAsRead();
        $unreadCount = $request->user()->unreadNotifications->count();
        session(['unreadNotificationCount' => $unreadCount]);
        return response()->json(['unreadCount' => $unreadCount]);
    }

    public function deleteSelected(Request $request){
        $selectedNotifications = $request->input('selectedNotifications');
        Notification::whereIn('id', $selectedNotifications)->delete();
        return redirect()->back()->with('success', 'Selected notifications deleted successfully.');
    }

    public function deleteAll(){
        Notification::where('notifiable_id', Auth::id())
        ->where('notifiable_type', 'App\Models\User')
        ->delete();
        return redirect()->back()->with('success', 'All notifications deleted successfully.');
    }

    public function showFeedback($id){
        $notification = auth()->user()->notifications->find($id);
        //dd($notification);
        return view('notifications.feedback',compact('notification'));
    }

    public function showRefund($id){
        $notification = auth()->user()->notifications->find($id);
        //dd($notification);
        return view('notifications.refund',compact('notification'));
    }

    public function showSoldOutTicket($id){
        $notification = auth()->user()->notifications->find($id);
        return view('notifications.soldOutTicket',compact('notification'));
    }

    public function showSoldOutParkTicket($id){
        $notification = auth()->user()->notifications->find($id);
        return view('notifications.soldOutParkTicket',compact('notification'));
    }
}
