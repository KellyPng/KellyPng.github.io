<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Parks;
use App\Models\Bookings;
use App\Models\Visitors;
use App\Models\TicketType;
use Illuminate\Http\Request;
use App\Models\BookingDetail;
use App\Models\EmployeeReports;
use App\Models\FeedbackReview;
use App\Models\Refund;
use Illuminate\Support\Carbon;
use App\Models\SingleParkTicket;
use Illuminate\Support\Facades\DB;
use App\Models\TicketAvailability as ModelsTicketAvailability;
use App\Notifications\TicketAvailability;
use App\Models\SingleParkTicketAvailability;
use Svg\Tag\Rect;

class PagesController extends Controller
{
    public function login()
    {
        return view('pages.login');
    }

    public function register()
    {
        return view('pages.register');
    }

    public function index()
    {
        if (auth()->check()) {
            $startDate = null;
        $endDate = null;
        $previousStartDate = null;
        $previousEndDate = null;
        $currentPeriodData = [];
        $previousPeriodData = [];
            //by default is filter by this month
            $filter = 'thismonth';
            list($startDate, $endDate,$previousStartDate,$previousEndDate) = $this->getDefaultDates($filter);
            $currentPeriodData = $this->getDataForPeriod($startDate,$endDate);
            $previousPeriodData = $this->getDataForPeriod($previousStartDate,$previousEndDate);
            $metrics = $this->calculateMetrics($currentPeriodData, $previousPeriodData);
//dd($metrics);
            return view('pages.dashboard', compact('currentPeriodData','metrics'));
        } else {
            return redirect()->route('login');
        }
    }

    public function filter(Request $request){
        $fromDate = $request->input('startDate');
        $toDate = $request->input('endDate');
        $filter = $request->input('filter');
        $startDate = null;
        $endDate = null;
        $previousStartDate = null;
        $previousEndDate = null;

        if (!$filter) {
            list($startDate, $endDate,$previousStartDate,$previousEndDate) = $this->getDefaultDates('thismonth');
        }

        if ($fromDate && $toDate) {
            $startDate = $fromDate;
            $endDate = $toDate;
            $previousDates = $this->getPreviousDates($fromDate,$toDate);
            $previousStartDate = $previousDates['previousStartDate'];
            $previousEndDate = $previousDates['previousEndDate'];
        }elseif ($filter&&!$fromDate && !$toDate) {

            list($startDate,$endDate,$previousStartDate,$previousEndDate) = $this->getDefaultDates($filter);
            
        }

        $currentPeriodData = $this->getDataForPeriod($startDate,$endDate);
            $previousPeriodData = $this->getDataForPeriod($previousStartDate,$previousEndDate);
            $metrics = $this->calculateMetrics($currentPeriodData, $previousPeriodData);
            session()->put('filteredCurrentPeriodData',['currentPeriodData' => $currentPeriodData]);

        // $bookings = Bookings::whereBetween('created_at',[$startDate,$endDate])->get();
        //     $bookingsCount = [];
        //     foreach ($bookings as $booking) {
        //         $date = $booking->created_at->format('Y-m-d');
        //         if(!isset($bookingsCount[$date])){
        //             $bookingsCount[$date]=0;
        //         }
        //         $bookingsCount[$date]++;
        //     }
        //     //dd($startDate,$endDate);
        //     $visitors = Visitors::whereHas('bookings', function ($query) use ($startDate, $endDate) {
        //         $query->whereBetween('bookingDate', [$startDate, $endDate]);
        //     })->get();
        //     $visitorsCount = [];
        //     foreach ($visitors as $visitor) {
        //         $date = $visitor->visitDate();
        //         if(!isset($visitorsCount[$date])){
        //             $visitorsCount[$date]=0;
        //         }
        //         $visitorsCount[$date]++;
        //     }
        //     $refunds = Refund::whereBetween('requestDate',[$startDate,$endDate])->get();
        //     $productRankingData = $this->getProductRankingDataForPeriod($startDate, $endDate);
        //     $revenue = $this->getRevenueData($startDate,$endDate);
        //     //do for employee reports once available
        //     $reviews = FeedbackReview::whereBetween('created_at',[$startDate,$endDate])->get();
        return view('pages.dashboard',compact('currentPeriodData','metrics'));
    }

    public function getPreviousDates($startDate,$endDate){
        $duration = Carbon::parse($endDate)->diffInDays($startDate);
        $previousStartDate = Carbon::parse($startDate)->subDays($duration + 1)->toDateString();
        $previousEndDate = Carbon::parse($startDate)->subDay()->toDateString();
        return [
            'previousStartDate' => $previousStartDate,
            'previousEndDate' => $previousEndDate,
        ];
    }

    public function getDefaultDates($filter){
        switch ($filter) {
            case 'last12months':
                $startDate = now()->subMonths(12)->startOfMonth();
                $endDate = now()->endOfMonth();
                $previousStartDate = $startDate->copy()->subMonths(12)->startOfMonth();
                $previousEndDate = $endDate->copy()->subMonths(12)->endOfMonth();
                break;
            case 'last6months':
                $startDate = now()->subMonths(6)->startOfMonth();
                $endDate = now()->endOfMonth();
                $previousStartDate = $startDate->copy()->subMonths(6)->startOfMonth();
                $previousEndDate = $endDate->copy()->subMonths(6)->endOfMonth();
                break;
            case 'last3months':
                $startDate = now()->subMonths(3)->startOfMonth();
                $endDate = now()->endOfMonth();
                $previousStartDate = $startDate->copy()->subMonths(3)->startOfMonth();
                $previousEndDate = $endDate->copy()->subMonths(3)->endOfMonth();
                break;
            case 'lastmonth':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                $previousStartDate = $startDate->copy()->subMonths(2)->startOfMonth();
                $previousEndDate = $endDate->copy()->subMonths(2)->endOfMonth();
                break;
            case 'thismonth':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $previousStartDate = $startDate->copy()->subMonth()->startOfMonth();
                $previousEndDate = $endDate->copy()->subMonth()->endOfMonth();
                break;
            case 'thisweek':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                $previousStartDate = $startDate->copy()->subWeek()->startOfWeek();
                $previousEndDate = $endDate->copy()->subWeek()->endOfWeek();
            default:
                break;
        }
        return [
            $startDate->toDateString(),
            $endDate->toDateString(),
            $previousStartDate->toDateString(),
            $previousEndDate->toDateString(),
        ];
    }

    public function getDataForPeriod($startDate,$endDate){
        $revenue = $this->getRevenueData($startDate,$endDate);
            $bookings = Bookings::whereBetween('created_at',[$startDate,$endDate])->get();
            $bookingsCount = [];
            foreach ($bookings as $booking) {
                $date = $booking->created_at->format('Y-m-d');
                if(!isset($bookingsCount[$date])){
                    $bookingsCount[$date]=0;
                }
                $bookingsCount[$date]++;
            }
            $bookingsCountforMetrics = count($bookings);
            $visitors = Visitors::whereHas('bookings', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('bookingDate', [$startDate, $endDate]);
            })->get();
            $visitorsCount = [];
            foreach ($visitors as $visitor) {
                $date = $visitor->visitDate();
                if(!isset($visitorsCount[$date])){
                    $visitorsCount[$date]=0;
                }
                $visitorsCount[$date]++;
            }
            $visitorsCountforMetrics = count($visitors);
            $refunds = Refund::whereBetween('requestDate',[$startDate,$endDate])->get();
            $refundsCount = count($refunds);
            $productRankingData = $this->getProductRankingDataForPeriod($startDate, $endDate);
            
            $employeeReports = EmployeeReports::whereBetween('created_at',[$startDate,$endDate])->get();
            $reviews = FeedbackReview::whereBetween('created_at',[$startDate,$endDate])->get();

            //return [$bookings,$bookingsCount,$visitors,$visitorsCount,$revenue,$refunds,$refundsCount,$productRankingData,$reviews];
            return [
                'bookingsCount' => $bookingsCount,
                'bookings' => $bookings,
                'bookingsCountforMetrics' => $bookingsCountforMetrics,
                'revenue' => $revenue,
                'visitors' => $visitors,
                'visitorsCount' => $visitorsCount,
                'visitorsCountforMetrics' => $visitorsCountforMetrics,
                'refunds' => $refunds,
                'refundsCount' => $refundsCount,
                'productRankingData' => $productRankingData,
                'reviews' => $reviews,
                'employeeReports' => $employeeReports
            ];
    }

    public function calculateMetrics($currentPeriodData, $previousPeriodData)
{
    $metrics = [];

    // Calculate bookings count metrics
    $bookingsCountCurrent = $currentPeriodData['bookingsCountforMetrics'];
    $bookingsCountPrevious = $previousPeriodData['bookingsCountforMetrics'];
    if ($bookingsCountPrevious) {
        $bookingsPercentageChange = (($bookingsCountCurrent-$bookingsCountPrevious)/$bookingsCountPrevious*100);
        $bookingsPercentageChange = min($bookingsPercentageChange,100);
        $bookingsPercentageChange = number_format($bookingsPercentageChange,0);
        $bookingsChangeDirection = $bookingsPercentageChange >=0 ? ($bookingsPercentageChange >0 ?'increase':'stable'):'decrease';
        $metrics['bookings'] = [
            'count' => $bookingsCountCurrent,
            'changePercentage' => $bookingsPercentageChange,
            'changeDirection' => $bookingsChangeDirection
        ];
    }else{
        $metrics['bookings'] = [
            'count' => $bookingsCountCurrent,
            'changePercentage' => null,
            'changeDirection' => null
        ];
    }

    // Calculate visitors count metrics
    $visitorsCountCurrent = $currentPeriodData['visitorsCountforMetrics'];
    $visitorsCountPrevious = $previousPeriodData['visitorsCountforMetrics'];
    if ($visitorsCountPrevious) {
        $visitorsPercentageChange = (($visitorsCountCurrent-$visitorsCountPrevious)/$visitorsCountPrevious*100);
        $visitorsPercentageChange = min($visitorsPercentageChange,100);
        $visitorsPercentageChange = number_format($visitorsPercentageChange,0);
        $visitorsChangeDirection = $visitorsPercentageChange >=0 ? ($visitorsPercentageChange >0 ?'increase':'stable'):'decrease';
        $metrics['visitors'] = [
            'count' => $visitorsCountCurrent,
            'changePercentage' => $visitorsPercentageChange,
            'changeDirection' => $visitorsChangeDirection
        ];
    }else{
        $metrics['visitors'] = [
            'count' => $visitorsCountCurrent,
            'changePercentage' => null,
            'changeDirection' => null
        ];
    }

    // Calculate refunds count metrics
    $refundsCountCurrent = $currentPeriodData['refundsCount']??0;
    $refundsCountPrevious = $previousPeriodData['refundsCount'];
    if ($refundsCountPrevious) {
        $refundsPercentageChange = (($refundsCountCurrent-$refundsCountPrevious)/$refundsCountPrevious*100);
        $refundsPercentageChange = min($refundsPercentageChange,100);
        $refundsPercentageChange = number_format($refundsPercentageChange,0);
        $refundsChangeDirection = $refundsPercentageChange >=0 ? ($refundsPercentageChange >0 ?'increase':'stable'):'decrease';
        $metrics['refunds'] = [
            'count' => $refundsCountCurrent,
            'changePercentage' => $refundsPercentageChange,
            'changeDirection' => $refundsChangeDirection
        ];
    }else{
        $metrics['refunds'] = [
            'count' => $refundsCountCurrent,
            'changePercentage' => null,
            'changeDirection' => null
        ];
    }    

    return $metrics;
}

    public function getProductRankingDataForPeriod($startDate, $endDate)
    {
        $currentPeriodData = [];
        $products = DB::table('bookings')
            ->join('booking_details', 'bookings.id', '=', 'booking_details.bookID')
            ->join('ticket_types', 'bookings.ticketTypeID', '=', 'ticket_types.id')
            ->select(
                'ticket_types.ticketTypeName AS ticketTypeName',
                'ticket_types.id AS ticketTypeId',
                DB::raw('SUM(booking_details.quantity) AS totalQuantitySold')
            )
            ->groupBy('ticket_types.id', 'ticket_types.ticketTypeName')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->orderByDesc('totalQuantitySold')
            ->get();

        foreach ($products as $product) {
            if ($product->ticketTypeId != 1) {
                $currentPeriodData[] = [
                    'type' => 'product',
                    'ticketTypeName' => $product->ticketTypeName,
                    'totalQuantitySold' => $product->totalQuantitySold,
                ];
            }
        }
        $parkTickets = DB::table('bookings')
            ->join('booking_details', 'bookings.id', '=', 'booking_details.bookID')
            ->join('ticket_types', 'bookings.ticketTypeID', '=', 'ticket_types.id')
            ->join('book_parks', 'bookings.id', '=', 'book_parks.bookingID')
            ->join('parks', 'book_parks.parkID', '=', 'parks.id')
            ->join('single_park_tickets', 'parks.id', '=', 'single_park_tickets.parkId')
            ->where('bookings.ticketTypeID', 1)
            ->select(
                'ticket_types.id AS ticketTypeId',
                'ticket_types.ticketTypeName AS ticketTypeName',
                'parks.id AS parkId',
                'parks.parkName AS parkName',
                DB::raw('SUM(booking_details.quantity) AS totalQuantitySold')
            )->groupBy('ticket_types.id', 'ticket_types.ticketTypeName', 'parks.id', 'parks.parkName')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->orderByDesc('totalQuantitySold')
            ->get();

        foreach ($parkTickets as $parkTicket) {
            $currentPeriodData[] = [
                'type' => 'parkTicket',
                'ticketTypeName' => 'Single Park : ' . $parkTicket->parkName,
                'totalQuantitySold' => $parkTicket->totalQuantitySold,
            ];
        }
        return $currentPeriodData;
    }

    public function getRevenueData($startDate, $endDate)
    {
        $query = DB::table('bookings')
            ->leftJoin('booking_details', 'bookings.id', '=', 'booking_details.bookID')
            ->leftJoin('refund_request_tables', 'bookings.bookingID', '=', 'refund_request_tables.bookingID')
            ->select(
                DB::raw("DATE_FORMAT(bookings.created_at,'%M %Y') AS month"),
                DB::raw('COUNT(DISTINCT bookings.id) AS total_bookings'),
                DB::raw('SUM(bookings.totalPrice) AS total_sales'),
                DB::raw('COALESCE(SUM(refund_request_tables.priceRefund), 0) AS total_refunds')
            )
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('bookings.created_at', 'DESC');

        return $query->get();
    }

    public function unauthorized()
{
    return view('auth.unauthorized');
}

    // public function exportBookingsReport(Request $request){
    //     $filteredData = session()->get('filteredCurrentPeriodData');
    //     $currentPeriodData = $filteredData['currentPeriodData'];
    //     $bookings = $currentPeriodData['bookings'];
    //     $bookingsCount = $currentPeriodData['bookingsCount'];
    //     $currentDate = now();
    //     $chartVisibility = 'visible';

    //     $bookingController = new BookingController();
    //     $exportResponse = $bookingController->export_pdf($request,$chartVisibility);
    // }

}
