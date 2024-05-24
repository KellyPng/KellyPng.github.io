<?php

namespace App\Console\Commands;

use App\Models\Parks;
use App\Models\Bookings;
use App\Models\BookPark;
use App\Models\TicketType;
use App\Models\BookingDetail;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\SingleParkTicket;

class UpdateAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-availability';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update availability for each ticket type and park tickets';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $todayBookings = Bookings::whereDate('bookingDate', Carbon::now())
                             ->where('quantity_deducted', false)
                             ->get();

    foreach ($todayBookings as $booking) {
        // Get the booking details for this booking
        $bookingDetails = BookingDetail::where('bookID', $booking->id)->get();

        foreach ($bookingDetails as $detail) {
            if ($booking->ticketTypeID == 1) {
                // This is a Single Park ticket
                // Get the park ID from the book_parks table
                $bookPark = BookPark::where('bookingID', $booking->id)->first();
                $parkId = $bookPark->parkID;

                // Find the corresponding single park ticket
                $singleParkTicket = SingleParkTicket::where('parkId', $parkId)->first();

                // Deduct the quantity from the remaining quantity
                $singleParkTicket->remaining_quantity = max(0, $singleParkTicket->remaining_quantity - $detail->quantity);
                $singleParkTicket->save();
            } else {
                // This is a regular ticket
                // Find the corresponding ticket type
                $ticketType = TicketType::find($booking->ticketTypeID);

                // Deduct the quantity from the remaining quantity
                $ticketType->remaining_quantity = max(0, $ticketType->remaining_quantity - $detail->quantity);
                $ticketType->save();
            }
        }
        $booking->quantity_deducted = true;
        $booking->save();
    }

    $this->info('Ticket quantities have been deducted successfully.');
}

    // public function updateAvailability($bookingid, $tickettypeid){
    //     $ticketType = TicketType::where('id',$tickettypeid);
    //     $bookingDetails = BookingDetail::where('bookID',$bookingid)->get();
    //     foreach($bookingDetails as $detail){
    //         $ticketType->remaining_quantity -= $detail->quantity;
    //     }
    //     $ticketType->save();
        
    //     $bookedPark = BookPark::where('bookingID',$bookingid)->first();
    //     if ($bookedPark) {
    //         $park = Parks::find($bookedPark->parkID);
    //         foreach($bookingDetails as $detail){
    //             $park->singleParkTicket()->remaining_quantity -= $detail->quantity;
    //         }
    //         $park->singleParkTicket()->save();
    //     }
    // }

    
}
