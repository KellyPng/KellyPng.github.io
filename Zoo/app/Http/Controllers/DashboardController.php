<?php

namespace App\Http\Controllers;

use App\Models\Parks;
use App\Models\Bookings;
use App\Models\SingleParkTicket;
use App\Models\Visitors;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $bookings = Bookings::join('visitors', 'bookings.visitorID', '=', 'visitors.id')
            ->leftJoin('booking_details', 'bookings.id', '=', 'booking_details.bookID')
            ->select(
                'bookings.id',
                DB::raw("CONCAT(visitors.firstName, ' ', visitors.lastName) as visitorName"),
                'bookings.totalPrice',
                'bookings.bookingDate',
                DB::raw('SUM(booking_details.quantity) as totalQuantity')
            )
            ->groupBy('bookings.id', 'visitorName', 'totalPrice', 'bookingDate')
            ->get();

        $todayDate = Carbon::now()->format('d-m-Y');
        $thisMonth = Carbon::now()->format('m');
        $thisYear = Carbon::now()->format('Y');
        

        $totalVisitors = Visitors::query()->count();
        $totalBookings = Bookings::query()->count();
        $totalParks = Parks::query()->count();
        $todayBooking = Bookings::query()->whereDate('created_at', $todayDate)->count();
        $thisMonthBooking = Bookings::query()->whereMonth('created_at', $thisMonth)->count();
        $thisYearBooking = Bookings::query()->whereYear('created_at', $thisYear)->count();

        $totalSoldOutTickets = TicketType::query()->where('remaining_quantity', 0)->where('id', '!=', 1)->count();
        $totalSoldOutParkTickets = SingleParkTicket::query()->where('remaining_quantity', 0)->count();
        $totalSoldOut = $totalSoldOutTickets + $totalSoldOutParkTickets;

        $monthlySales = Bookings::select(
            DB::raw('MONTH(bookingDate) as month'),
            DB::raw('YEAR(bookingDate) as year'),
            DB::raw('SUM(totalPrice) as totalSales')
        )
        ->groupBy('month', 'year')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $countries = Visitors::select('country', DB::raw('count(*) as total'))
    ->groupBy('country')
    ->get();


    $ticketTypes = DB::table('bookings')
    ->join('ticket_types', 'bookings.ticketTypeID', '=', 'ticket_types.id')
    ->join('booking_details','bookings.id','=','booking_details.bookID')
    ->select('ticket_types.ticketTypeName', DB::raw('SUM(booking_details.quantity) as total'))
    ->groupBy('ticket_types.ticketTypeName')
    ->orderBy('total', 'desc')
    ->get();

        // $visitors = Visitors::all();
        // $visitors->each(function ($visitor) {
        //     $visitor->fullName = $visitor->firstName . ' ' . $visitor->lastName;
        // });
        return view('pages.dashboard', compact('bookings', 'totalVisitors', 'ticketTypes', 'monthlySales', 'countries','totalParks', 'totalSoldOut', 'totalBookings', 'todayBooking', 'thisMonthBooking', 'thisYearBooking'));
    }
}
