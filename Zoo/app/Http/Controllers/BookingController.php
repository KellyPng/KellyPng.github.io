<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bookings;
use App\Models\DemoCategory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportBookingsReport;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    public function index(Request $request)
    {

        $bookings = Bookings::all();
        $totalQuantity = $bookings->sum(fn($booking) => $booking->bookingDetails->sum('quantity'));
        $totalPrice = $bookings->sum('totalPrice');
        $demographicQuantities = $this->calculateDemographicQuantities($bookings);

        // Fetch data for bookings count chart
        $bookingsCount = $bookings->groupBy(fn($booking) => $booking->created_at->format('Y-m-d'))
                                         ->map(fn($group) => $group->count());


        session()->put('unfilteredData', [
            'dateFilter' => null,
            'fromdate' => null,
            'todate' => null,
            'bookings' => $bookings,
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
            'demographicQuantities' => $demographicQuantities,
        ]);

        return view('pages.bookings', compact('bookings', 'totalQuantity', 'totalPrice', 'demographicQuantities','bookingsCount'));
    }

    

    private function calculateDemographicQuantities($bookings)
    {
        $demographicQuantities = [];

        foreach ($bookings as $booking) {
            $bookingDemographicQuantities = [];

            foreach ($booking->bookingDetails as $bookingDetail) {
                if ($bookingDetail->quantity > 0) {
                    $demoCategory = DemoCategory::find($bookingDetail->demoCategoryID);
                    $categoryName = $demoCategory->demoCategoryName;

                    if ($bookingDetail->is_local == 1) {
                        $categoryName .= ' (local)';
                    } else {
                        $categoryName .= ' (foreign)';
                    }
                    $bookingDemographicQuantities[$categoryName] = $bookingDetail->quantity;
                }
            }

            if (!empty($bookingDemographicQuantities)) {
                $demographicQuantities[$booking->id] = $bookingDemographicQuantities;
            }
        }

        return $demographicQuantities;
    }

    public function filter(Request $request)
    {
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');
        $dateFilter = $request->input('dateFilter');
        $filter = $request->input('filter');
        $totalQuantity = 0;
        $totalPrice = 0;
        $demographicQuantities = [];

        $query = Bookings::query();
        $query = $this->applyDateFilter($query, $dateFilter, $filter, $fromdate, $todate);
        $bookings = $query->get();

        foreach ($bookings as $booking) {
            $bookingDemographicQuantities = [];
            foreach ($booking->bookingDetails as $bookingDetail) {
                if ($bookingDetail->quantity > 0) {
                    $demoCategory = DemoCategory::find($bookingDetail->demoCategoryID);
                    $categoryName = $demoCategory->demoCategoryName;

                    if ($bookingDetail->is_local == 1) {
                        $categoryName .= ' (local)';
                    } else {
                        $categoryName .= ' (foreign)';
                    }
                    $bookingDemographicQuantities[$categoryName] = $bookingDetail->quantity;
                }
            }

            if (!empty($bookingDemographicQuantities)) {
                $demographicQuantities[$booking->id] = $bookingDemographicQuantities;
            }

            $bookingsCount = [];
            foreach ($bookings as $booking) {
                $date = $booking->created_at->format('Y-m-d');
                if(!isset($bookingsCount[$date])){
                    $bookingsCount[$date]=0;
                }
                $bookingsCount[$date]++;
            }

            $totalQuantity += $booking->bookingDetails->sum('quantity');
            $totalPrice += $booking->totalPrice;
        }

        session()->put('filtered_data', [
            'filter' => $filter,
            'dateFilter' => $dateFilter,
            'fromdate' => $fromdate,
            'todate' => $todate,
            'bookings' => $bookings,
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
            'demographicQuantities' => $demographicQuantities,
        ]);

        return view('pages.bookings', compact('bookings', 'totalQuantity', 'totalPrice', 'demographicQuantities','bookingsCount'));
    }

    public function applyDateFilter($query, $dateFilter, $filter, $fromdate, $todate)
    {

        if ($dateFilter === 'visitdate') {
            $dateType = 'bookingDate';
        } else {
            $dateType = 'created_at';
        }

        switch ($filter) {
            case 'last12months':
                return $query->where($dateType, '>=', Carbon::now()->subMonths(12));
            case 'last6months':
                return $query->where($dateType, '>=', Carbon::now()->subMonths(6));
            case 'last3months':
                return $query->where($dateType, '>=', Carbon::now()->subMonths(3));
            case 'lastmonth':
                return $query->whereMonth($dateType, Carbon::now()->subMonth()->month);
            case 'thismonth':
                return $query->whereMonth($dateType, Carbon::now()->month);
            case 'dateRange':
                return $query->whereBetween($dateType, [$fromdate, $todate]);
            default:
                return $query;
        }
    }

    public function export_pdf(Request $request)
    {
        ini_set('max_execution_time', 300);
        $filteredData = session()->get('filtered_data');
        $unfilteredData = session()->get('unfilteredData');
        $chartVisibility = $request->input('chartVisibility');
        $chartImage = $request->input('chartImage');
        if ($filteredData) {
            $bookings = $filteredData['bookings'];
            $totalQuantity = $filteredData['totalQuantity'];
            $totalPrice = $filteredData['totalPrice'];
            $dateFilter = $filteredData['dateFilter'];
            $filter = $filteredData['filter'];
            $fromdate = $filteredData['fromdate'];
            $todate = $filteredData['todate'];
            $demographicQuantities = $filteredData['demographicQuantities'];
            $pdf = PDF::loadView('pdf.bookings', [
                'dateFilter' => $dateFilter,
                'filter' => $filter,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'bookings' => $bookings,
                'totalQuantity' => $totalQuantity,
                'totalPrice' => $totalPrice,
                'demographicQuantities' => $demographicQuantities,
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
                'currentDate' => now(),
            ]);
            Session::forget('filtered_data');
            return $pdf->download('Bookings_Report.pdf');
        } else {
            $bookings = $unfilteredData['bookings'];
            $totalQuantity = $unfilteredData['totalQuantity'];
            $totalPrice = $unfilteredData['totalPrice'];
            $dateFilter = 'all';
            $fromdate = null;
            $todate = null;
            $demographicQuantities = $unfilteredData['demographicQuantities'];
            $pdf = PDF::loadView('pdf.bookings', [
                'dateFilter' => $dateFilter,
                'filter' => null,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'bookings' => $bookings,
                'totalQuantity' => $totalQuantity,
                'totalPrice' => $totalPrice,
                'demographicQuantities' => $demographicQuantities,
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
                'currentDate' => now(),
            ]);
            Session::forget('unfilteredData');
            return $pdf->download('Bookings_Report.pdf');
        }
    }

    public function export_csv()
    {
        $filteredData = session()->get('filtered_data');
        $unfilteredData = session()->get('unfilteredData');
        if ($filteredData) {
            $bookings = $filteredData['bookings'];
            $totalQuantity = $filteredData['totalQuantity'];
            $totalPrice = $filteredData['totalPrice'];
            $dateFilter = $filteredData['dateFilter'];
            $filter = $filteredData['filter'];
            $fromdate = $filteredData['fromdate'];
            $todate = $filteredData['todate'];
            $demographicQuantities = $filteredData['demographicQuantities'];
            $currentDate = now();
            Session::forget('filtered_data');
            return Excel::download(new ExportBookingsReport($bookings, $totalQuantity, $totalPrice, $demographicQuantities, $currentDate, $dateFilter, $filter, $fromdate, $todate, $currentDate), 'Bookings_Report.xlsx');
        } else {
            $bookings = $unfilteredData['bookings'];
            $totalQuantity = $unfilteredData['totalQuantity'];
            $totalPrice = $unfilteredData['totalPrice'];
            $dateFilter = 'all';
            $filter = null;
            $fromdate = $unfilteredData['fromdate'];
            $todate = $unfilteredData['todate'];
            $demographicQuantities = $unfilteredData['demographicQuantities'];
            $currentDate = now();
            Session::forget('unfilteredData');
            return Excel::download(new ExportBookingsReport($bookings, $totalQuantity, $totalPrice, $demographicQuantities, $currentDate, $dateFilter, $filter, $fromdate, $todate, $currentDate), 'Bookings_Report.xlsx');
        }
    }
}
