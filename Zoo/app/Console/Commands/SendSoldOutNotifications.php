<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\TicketType;
use App\Models\Notification;
use Illuminate\Console\Command;
use App\Models\SingleParkTicket;
use App\Events\MessageNotification;
use Illuminate\Support\Facades\Cache;
use App\Notifications\TicketAvailability;
use App\Models\SingleParkTicketAvailability;
use App\Models\TicketAvailability as ModelsTicketAvailability;

class SendSoldOutNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-sold-out-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $soldOutTickets = ModelsTicketAvailability::where('available_quantity', 0)->get();
        $soldOutParkTickets = SingleParkTicketAvailability::where('available_quantity',0)->get();
        $admins = User::where('employeeType', 'admin')->get();
        if($soldOutTickets->isNotEmpty()&&$soldOutParkTickets->isNotEmpty()){
                foreach ($admins as $admin) {
                    $notification = new TicketAvailability($soldOutTickets,$soldOutParkTickets,$admin);
                    
                    // if (!Cache::has('notification_sent_' . $notificationId)) {
                        // Notification has not been sent, so send it and dispatch the event
                        $admin->notify($notification);
                        $sentNotification = $admin->notifications->first();
                        $unreadCount = $admin->unreadNotifications()->count();
                        //event(new MessageNotification($sentNotification,$unreadCount));
                        // Mark the notification as sent to prevent duplicates
                        // Cache::put('notification_sent_' . $notificationId, true, now()->addHours(24)); // Cache for 24 hours
                    // }
                    
                }
        }elseif($soldOutTickets->isNotEmpty()){
                foreach ($admins as $admin) {
                    $notification = new TicketAvailability($soldOutTickets,null,$admin);
                    // $notificationId = $notification->id;
                    // dd($notificationId);
                    // if (!Cache::has('notification_sent_' . $notificationId)) {
                        // Notification has not been sent, so send it and dispatch the event
                        $admin->notify($notification);
                        // event(new MessageNotification($notification->toArray($admin)));
                        $sentNotification = $admin->notifications->first();
                        $unreadCount = $admin->unreadNotifications()->count();
                        event(new MessageNotification($sentNotification, $unreadCount));
                        // Mark the notification as sent to prevent duplicates
                        // Cache::put('notification_sent_' . $notificationId, true, now()->addHours(24)); // Cache for 24 hours
                    // }
                }
        }elseif($soldOutParkTickets->isNotEmpty()){
                foreach ($admins as $admin) {
                    $notification = new TicketAvailability(null,$soldOutParkTickets,$admin);
                    // $notificationId = $notification->id;
                    // if (!Cache::has('notification_sent_' . $notificationId)) {
                        // Notification has not been sent, so send it and dispatch the event
                        $admin->notify($notification);
                        // event(new MessageNotification($notification->toArray($admin)));
                        $sentNotification = Notification::sent($admin)->first();
                        $unreadCount = $admin->unreadNotifications()->count();
                        //event(new MessageNotification($sentNotification,$unreadCount));
                        // Mark the notification as sent to prevent duplicates
                        // Cache::put('notification_sent_' . $notificationId, true, now()->addHours(24)); // Cache for 24 hours
                    // }
                }
            }
        }

}
