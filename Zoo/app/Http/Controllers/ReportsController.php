<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ExportRevenueReport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Exports\ExportProductRankingReport;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reports.index');
    }

    public function productRanking()
    {
        $filter = 'last12months';
        $startDate = null;
        $endDate = null;
        $previousStartDate = null;
        $previousEndDate = null;
        $currentPeriodData = [];

        if (!$startDate || !$endDate) {
            list($startDate, $endDate, $previousStartDate, $previousEndDate) = $this->getDefaultDates($filter);
            //dd($startDate,$endDate,$previousStartDate,$previousEndDate);
        }

        $currentPeriodData = $this->getDataForPeriod($startDate, $endDate);
        $previousPeriodData = $this->getDataForPeriod($previousStartDate, $previousEndDate);
        $currentPeriodData = $this->calculateMetrics($currentPeriodData, $previousPeriodData);

        // $previousStartDate = null;
        // $previousEndDate = null;
        // if ($filter === 'dateRange' && $startDate && $endDate) {
        //     $duration = Carbon::parse($endDate)->diffInDays($startDate);
        //     $previousStartDate = Carbon::parse($startDate)->subDays($duration + 1)->toDateString();
        //     $previousEndDate = Carbon::parse($startDate)->subDay()->toDateString();
        // } elseif ($startDate && $endDate) {
        //     $previousStartDate = date('Y-m-d', strtotime($endDate . '-' . ($filter === '7days' ? '14 days' : ($filter === '30days' ? '60 days' : '0 days'))));
        //     $previousEndDate = date('Y-m-d', strtotime($endDate . '-' . ($filter === '7days' ? '8 days' : ($filter === '30days' ? '31 days' : '0 days'))));
        // }
        // //dd($previousStartDate,$previousEndDate,$startDate,$endDate);
        // $previousPeriodData = $this->getDataForPeriod($previousStartDate, $previousEndDate);
        // //dd($previousPeriodData);

        // foreach ($currentPeriodData as $index => $currentTicket) {
        //     $previousTicket = $previousPeriodData[$index] ?? null;

        //     if ($previousTicket) {
        //         $percentageChange = (($currentTicket['totalQuantitySold'] - $previousTicket['totalQuantitySold']) / $previousTicket['totalQuantitySold']) * 100;
        //         $percentageChange = min($percentageChange, 100);
        //         $percentageChange = number_format($percentageChange, 2);
        //         $changeDirection = $percentageChange >= 0 ? ($percentageChange > 0 ? 'increase' : 'stable') : 'decrease';
        //         $currentPeriodData[$index]['percentageChange'] = abs($percentageChange);
        //         $currentPeriodData[$index]['changeDirection'] = $changeDirection;
        //     } else {
        //         $currentPeriodData[$index]['percentageChange'] = null;
        //         $currentPeriodData[$index]['changeDirection'] = null;
        //     }
        // }
        // usort($currentPeriodData, function ($a, $b) {
        //     return $b['totalQuantitySold'] <=> $a['totalQuantitySold'];
        // });

        // foreach ($currentPeriodData as $index => $product) {
        //     $currentPeriodData[$index]['rank'] = $index + 1;
        // }

        session()->put('unfiltered', ['currentPeriodData' => $currentPeriodData, 'filter' => $filter, 'startdate' => $startDate, 'enddate' => $endDate]);
        return view('reports.productRanking', compact('currentPeriodData'));
    }

    public function getDefaultDates($filter)
    {
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

    public function calculateMetrics($currentPeriodData, $previousPeriodData)
    {
        foreach ($currentPeriodData as $index => $currentTicket) {
            $previousTicket = $previousPeriodData[$index] ?? null;

            if ($previousTicket) {
                $percentageChange = (($currentTicket['totalQuantitySold'] - $previousTicket['totalQuantitySold']) / $previousTicket['totalQuantitySold']) * 100;
                $percentageChange = min($percentageChange, 100);
                $percentageChange = number_format($percentageChange, 2);
                $changeDirection = $percentageChange >= 0 ? ($percentageChange > 0 ? 'increase' : 'stable') : 'decrease';
                $currentPeriodData[$index]['percentageChange'] = abs($percentageChange);
                $currentPeriodData[$index]['changeDirection'] = $changeDirection;
            } else {
                $currentPeriodData[$index]['percentageChange'] = null;
                $currentPeriodData[$index]['changeDirection'] = null;
            }
        }

        usort($currentPeriodData, function ($a, $b) {
            return $b['totalQuantitySold'] <=> $a['totalQuantitySold'];
        });

        foreach ($currentPeriodData as $index => $product) {
            $currentPeriodData[$index]['rank'] = $index + 1;
        }

        return $currentPeriodData;
    }


    public function productRankingFilter(Request $request)
    {
        $filter = $request->input('filterby');
        $startDate = $request->input('fromdate');
        $endDate = $request->input('todate');
        $previousStartDate = null;
        $previousEndDate = null;
        // $startDate = null;
        // $endDate = null;
        if ($filter === 'dateRange') {
            $dates = $this->getDatesFromFilter($startDate, $endDate);
            $previousStartDate = $dates['previousStartDate'];
            $previousEndDate = $dates['previousEndDate'];
            //dd($previousStartDate,$previousEndDate);
        } else {
            list($startDate, $endDate, $previousStartDate, $previousEndDate) = $this->getDefaultDates($filter);
        }

        // Fetch data for the given period
        $currentPeriodData = $this->getDataForPeriod($startDate, $endDate);
        $previousPeriodData = $this->getDataForPeriod($previousStartDate, $previousEndDate);

        // Calculate percentage change and other metrics
        $currentPeriodData = $this->calculateMetrics($currentPeriodData, $previousPeriodData);

        // if ($filter === '7days') {
        //     $startDate = now()->subDays(7)->toDateString();
        //     $endDate = now()->toDateString();
        //     //dd($startDate,$endDate);
        // } elseif ($filter === '30days') {
        //     $startDate = now()->subDays(30)->toDateString();
        //     $endDate = now()->toDateString();
        // } elseif ($filter === 'dateRange' && $fromDate && $toDate) {
        //     $startDate = $fromDate;
        //     $endDate = $toDate;
        // }

        // $currentPeriodData = $this->getDataForPeriod($startDate, $endDate);

        // $previousStartDate = null;
        // $previousEndDate = null;
        // if ($filter === 'dateRange' && $startDate && $endDate) {
        //     $duration = Carbon::parse($endDate)->diffInDays($startDate);
        //     $previousStartDate = Carbon::parse($startDate)->subDays($duration + 1)->toDateString();
        //     $previousEndDate = Carbon::parse($startDate)->subDay()->toDateString();
        // } elseif ($startDate && $endDate) {
        //     $previousStartDate = date('Y-m-d', strtotime($endDate . '-' . ($filter === '7days' ? '14 days' : ($filter === '30days' ? '60 days' : '0 days'))));
        //     $previousEndDate = date('Y-m-d', strtotime($endDate . '-' . ($filter === '7days' ? '8 days' : ($filter === '30days' ? '31 days' : '0 days'))));
        // }
        // //dd($previousStartDate,$previousEndDate,$startDate,$endDate);
        // $previousPeriodData = $this->getDataForPeriod($previousStartDate, $previousEndDate);
        // //dd($previousPeriodData);

        // foreach ($currentPeriodData as $index => $currentTicket) {
        //     $previousTicket = $previousPeriodData[$index] ?? null;

        //     if ($previousTicket) {
        //         $percentageChange = (($currentTicket['totalQuantitySold'] - $previousTicket['totalQuantitySold']) / $previousTicket['totalQuantitySold']) * 100;
        //         $percentageChange = min($percentageChange, 100);
        //         $percentageChange = number_format($percentageChange, 2);
        //         $changeDirection = $percentageChange >= 0 ? ($percentageChange > 0 ? 'increase' : 'stable') : 'decrease';
        //         $currentPeriodData[$index]['percentageChange'] = abs($percentageChange);
        //         $currentPeriodData[$index]['changeDirection'] = $changeDirection;
        //     } else {
        //         $currentPeriodData[$index]['percentageChange'] = null;
        //         $currentPeriodData[$index]['changeDirection'] = null;
        //     }
        // }

        // usort($currentPeriodData, function ($a, $b) {
        //     return $b['totalQuantitySold'] <=> $a['totalQuantitySold'];
        // });

        // foreach ($currentPeriodData as $index => $product) {
        //     $currentPeriodData[$index]['rank'] = $index + 1;
        // }
        session()->put('filtered_data', ['currentPeriodData' => $currentPeriodData, 'filter' => $filter, 'startdate' => $startDate, 'enddate' => $endDate]);
        return view('reports.productRanking', compact('currentPeriodData'));
    }

    public function getDatesFromFilter($startDate, $endDate)
    {
        $duration = Carbon::parse($endDate)->diffInDays($startDate);
        $previousStartDate = Carbon::parse($startDate)->subDays($duration + 1)->toDateString();
        $previousEndDate = Carbon::parse($startDate)->subDay()->toDateString();
        return [
            'previousStartDate' => $previousStartDate,
            'previousEndDate' => $previousEndDate,
        ];
    }

    public function getDataForPeriod($startDate, $endDate)
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

    public function exportPDF(Request $request)
    {
        $filtered_data = session()->get('filtered_data');
        $unfiltered = session()->get('unfiltered');
        $chartVisibility = $request->input('chartVisibility');
        $chartImage = $request->input('chartImage');
        $fileName = 'ProductRankings_' . now()->format('Y-m-d') . '.pdf';
        //dd($chartVisibility,$chartImage);
        if ($filtered_data) {
            $currentPeriodData = $filtered_data['currentPeriodData'];
            $filter = $filtered_data['filter'];
            $startDate = $filtered_data['startdate'];
            $endDate = $filtered_data['enddate'];

            $pdf = PDF::loadView('pdf.productRankings', [
                'currentPeriodData' => $currentPeriodData,
                'filter' => $filter,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'currentDate' => now(),
                'chartImage' => $chartImage,
                'chartVisibility' => $chartVisibility
            ]);

            return $pdf->download($fileName);
        } else {
            $currentPeriodData = $unfiltered['currentPeriodData'];
            $filter = $unfiltered['filter'];
            $startDate = $unfiltered['startdate'];
            $endDate = $unfiltered['enddate'];

            $pdf = PDF::loadView('pdf.productRankings', [
                'currentPeriodData' => $currentPeriodData,
                'filter' => $filter,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'currentDate' => now(),
                'chartImage' => $chartImage,
                'chartVisibility' => $chartVisibility
            ]);
            return $pdf->download($fileName);
        }
    }

    public function exportCSV()
    {
        $filtered_data = session()->get('filtered_data');
        $unfiltered = session()->get('unfiltered');
        $fileName = 'ProductRankings_' . now()->format('Y-m-d') . '.xlsx';

        if ($filtered_data) {
            $currentPeriodData = $filtered_data['currentPeriodData'];
            $filter = $filtered_data['filter'];
            $startDate = $filtered_data['startdate'];
            $endDate = $filtered_data['enddate'];
            $currentDate = now();

            return Excel::download(new ExportProductRankingReport($currentPeriodData, $filter, $startDate, $endDate, $currentDate), $fileName);
        } else {
            $currentPeriodData = $unfiltered['currentPeriodData'];
            $filter = $unfiltered['filter'];
            $startDate = $unfiltered['startdate'];
            $endDate = $unfiltered['enddate'];
            $currentDate = now();

            return Excel::download(new ExportProductRankingReport($currentPeriodData, $filter, $startDate, $endDate, $currentDate), $fileName);
        }
    }

    public function revenue()
    {
        $filter = 'last12months';
        $startDate = null;
        $endDate = null;
        $getRevenueData = $this->getRevenueData($filter, $startDate, $endDate);
        $totalRevenue = $getRevenueData->sum('total_sales') - $getRevenueData->sum('total_refunds');
        session()->put('unfilteredRevenueData', [
            'filter' => $filter,
            'dateRange' => null,
            'startDate' => null,
            'endDate' => null,
            'getRevenueData' => $getRevenueData,
            'totalRevenue' => $totalRevenue,
        ]);
        return view('reports.revenue', compact('getRevenueData', 'totalRevenue'));
    }

    public function revenueFilter(Request $request)
    {
        $filter = $request->input('filter');
        $dateRange = $request->input('dateRange');
        $startDate = $request->input('fromdate');
        $endDate = $request->input('todate');
        $chartImage = $request->input('chartImage');
        $getRevenueData = $this->getRevenueData($filter, $startDate, $endDate);
        $totalRevenue = $getRevenueData->sum('total_sales') - $getRevenueData->sum('total_refunds');
        session()->put('filteredRevenueData', [
            'filter' => $filter,
            'dateRange' => $dateRange,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'getRevenueData' => $getRevenueData,
            'totalRevenue' => $totalRevenue,
            'chartImage' => $chartImage,
        ]);
        return view('reports.revenue', compact('getRevenueData', 'totalRevenue'));
    }

    public function getRevenueData($filter, $startDate, $endDate)
    {
        $query = DB::table('bookings')
            ->leftJoin('booking_details', 'bookings.id', '=', 'booking_details.bookID')
            ->leftJoin('refund_request_tables', 'bookings.bookingID', '=', 'refund_request_tables.bookingID')
            ->select(
                DB::raw("DATE_FORMAT(bookings.created_at,'%M %Y') AS month"),
                DB::raw('COUNT(DISTINCT bookings.id) AS total_bookings'),
                DB::raw('SUM(bookings.totalPrice) AS total_sales'),
                DB::raw('COALESCE(SUM(refund_request_tables.priceRefund), 0) AS total_refunds')
            );

        switch ($filter) {
            case 'lastmonth':
                $query->whereYear('bookings.created_at', '=', Carbon::now()->subMonth()->year)
                    ->whereMonth('bookings.created_at', '=', Carbon::now()->subMonth()->month);
                break;
            case 'thismonth':
                $query->whereYear('bookings.created_at', '=', Carbon::now()->year)
                    ->whereMonth('bookings.created_at', '=', Carbon::now()->month);
                break;
            case 'last3months':
                $query->where('bookings.created_at', '>=', Carbon::now()->subMonths(3));
                break;
            case 'last6months':
                $query->where('bookings.created_at', '>=', Carbon::now()->subMonths(6));
                break;
            case 'last12months':
                $query->where('bookings.created_at', '>=', Carbon::now()->subMonths(12));
                break;
            case 'dateRange':
                $query->whereBetween('bookings.created_at', [$startDate, $endDate]);
                break;
            default:
                break;
        }
        $query->groupBy('month');
        $query->orderBy('bookings.created_at', 'DESC');
        //dd($query->toSql());
        return $query->get();
    }

    public function export_revenue_pdf(Request $request)
    {
        $filteredRevenueData = session()->get('filteredRevenueData');
        $unfilteredRevenueData = session()->get('unfilteredRevenueData');
        $chartVisibility = $request->input('chartVisibility');
        $chartImage = $request->input('chartImage');
        $fileName = 'RevenueReport_' . now()->format('Y-m-d') . '.pdf';

        if ($filteredRevenueData) {
            $filter = $filteredRevenueData['filter'];
            $dateRange = $filteredRevenueData['dateRange'];
            $startDate = $filteredRevenueData['startDate'];
            $endDate = $filteredRevenueData['endDate'];
            $getRevenueData = $filteredRevenueData['getRevenueData'];
            $totalRevenue = $filteredRevenueData['totalRevenue'];
            //$chartImage = $filteredRevenueData['chartImage'];
            $pdf = PDF::loadView('pdf.revenueReport', [
                'dateRange' => $dateRange,
                'getRevenueData' => $getRevenueData,
                'totalRevenue' => $totalRevenue,
                'filter' => $filter,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'currentDate' => now(),
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
            ]);
            return $pdf->download($fileName);
        } elseif ($unfilteredRevenueData) {
            $filter = $unfilteredRevenueData['filter'];
            $dateRange = $unfilteredRevenueData['dateRange'];
            $startDate = $unfilteredRevenueData['startDate'];
            $endDate = $unfilteredRevenueData['endDate'];
            $getRevenueData = $unfilteredRevenueData['getRevenueData'];
            $totalRevenue = $unfilteredRevenueData['totalRevenue'];
            $pdf = PDF::loadView('pdf.revenueReport', [
                'dateRange' => $dateRange,
                'getRevenueData' => $getRevenueData,
                'totalRevenue' => $totalRevenue,
                'filter' => $filter,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'currentDate' => now(),
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
            ]);
            return $pdf->download($fileName);
        }
    }

    public function export_revenue_csv(Request $request)
    {
        $filteredRevenueData = session()->get('filteredRevenueData');
        $unfilteredRevenueData = session()->get('unfilteredRevenueData');
        $chartImage = $request->input('chartImage');
        $chartImage = str_replace('data:image/png;base64,', '', $chartImage);
        $chartImage = str_replace(' ', '+', $chartImage);
        $chartImage = Str::random(10) . '.' . 'png';
        $chartVisibility = $request->input('chartVisibility');
        $fileName = 'RevenueReport_' . now()->format('Y-m-d') . '.xlsx';
        $currentDate = now();


        if ($filteredRevenueData) {
            $filter = $filteredRevenueData['filter'];
            $dateRange = $filteredRevenueData['dateRange'];
            $startDate = $filteredRevenueData['startDate'];
            $endDate = $filteredRevenueData['endDate'];
            $getRevenueData = $filteredRevenueData['getRevenueData'];
            $totalRevenue = $filteredRevenueData['totalRevenue'];
            // dd($getRevenueData);
            return Excel::download(new ExportRevenueReport($filter, $dateRange, $getRevenueData, $totalRevenue, $startDate, $endDate, $currentDate, $chartImage), $fileName);
        } else {
            $filter = $unfilteredRevenueData['filter'];
            $dateRange = $unfilteredRevenueData['dateRange'];
            $startDate = $unfilteredRevenueData['startDate'];
            $endDate = $unfilteredRevenueData['endDate'];
            $getRevenueData = $unfilteredRevenueData['getRevenueData'];
            $totalRevenue = $unfilteredRevenueData['totalRevenue'];
            //dd($getRevenueData);
            return Excel::download(new ExportRevenueReport($filter, $dateRange, $getRevenueData, $totalRevenue, $startDate, $endDate, $currentDate, $chartImage), $fileName);
        }
    }
}
