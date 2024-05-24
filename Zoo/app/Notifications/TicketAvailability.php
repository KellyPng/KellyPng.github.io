<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Events\MessageNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketAvailability extends Notification
{
    use Queueable;
    protected $soldOutTickets;
    protected $soldOutParkTickets;
    protected $admin;

    /**
     * Create a new notification instance.
     */
    public function __construct($soldOutTickets,$soldOutParkTickets,$admin)
    {
        $this->soldOutTickets = $soldOutTickets;
        $this->soldOutParkTickets = $soldOutParkTickets;
        $this->admin = $admin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Low Stock Notification')
                    //->line('Dear [Admin Name],')
                    //->line('Below are the tickets that are low in stock or have sold out.')
                    //->line('This is a notification to inform you that the tickets for the [park] on [Event Date] are low in stock/have been sold out.')
                    //->line('You may consider adding more capacity to meet the demand or taking no action if deemed appropriate. Please review the ticket availability and make the necessary decisions.')
                    ->markdown('email.ticket_availability_table',['soldOutTickets'=>$this->soldOutTickets,'soldOutParkTickets'=>$this->soldOutParkTickets,'admin'=>$this->admin]);
                    //->line('[Park] [Ticket Type] [Quantity]')
                    //->line('Thank you for your attention to this matter.');
                    //->action('Notification Action', url('/'))
                    //->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'Ticket has sold out',
            'soldOutTickets' => $this->soldOutTickets,
            'soldOutParkTickets' => $this->soldOutParkTickets,
            'admin' => $this->admin,
        ];
    }
    
}
