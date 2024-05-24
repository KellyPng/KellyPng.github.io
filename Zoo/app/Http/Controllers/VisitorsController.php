<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Visitors;
use Illuminate\Http\Request;
use App\Exports\ExportVisitorsReport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class VisitorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        session()->forget(['filtered-data', 'search-query', 'unfiltered-data']);
        $countries = DB::table('visitors')->distinct()->pluck('country')->all();
        $visitors = Visitors::all();
        $newVisitorCount = $visitors->filter(function ($visitor) {
            return $visitor->visitorstatus() === 'New';
        })->count();

        $existingVisitorCount = $visitors->filter(function ($visitor) {
            return $visitor->visitorstatus() === 'Existing';
        })->count();
        // session()->put('unfiltered-data', [
        //     'selectedCountry' => $countries,
        //     'visitDate' => null,
        //     'visitors' => null,
        // ]);

        return view('visitors.index', compact('visitors', 'countries', 'newVisitorCount', 'existingVisitorCount'));
    }

    public function filter(Request $request)
    {
        $countries = DB::table('visitors')->distinct()->pluck('country')->all();
        $selectedCountry = $request->input('countryFilter');
        // $visitDate = $request->input('visitDateFilter');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        $query = Visitors::query();

        if ($selectedCountry && $selectedCountry !== 'all') {
            $query->where('country', $selectedCountry);
        }

        // if ($visitDate) {
        //     $visitDateTime = Carbon::parse($visitDate);
        //     $query->whereHas('bookings', function ($bookingQuery) use ($visitDateTime) {
        //         $bookingQuery->whereDate('bookingDate', $visitDateTime->toDateString());
        //     });
        // }

        if ($fromdate && $todate) {
            $query->whereHas('bookings', function ($bookingQuery) use ($fromdate, $todate) {
                $bookingQuery->whereBetween('bookingDate', [$fromdate, $todate]);
            });
        }

        $visitors = $query->get();
        $newVisitorCount = $visitors->filter(function ($visitor) {
            return $visitor->visitorstatus() === 'New';
        })->count();

        $existingVisitorCount = $visitors->filter(function ($visitor) {
            return $visitor->visitorstatus() === 'Existing';
        })->count();
        session()->put('filtered-data', [
            'selectedCountry' => $selectedCountry,
            'fromdate' => $fromdate,
            'todate' => $todate,
            'visitors' => $visitors,
        ]);
        return view('visitors.index', compact('visitors', 'countries', 'newVisitorCount', 'existingVisitorCount'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $filteredResults = Visitors::where('firstName', 'like', "%$search%")
            ->orWhere('lastName', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->get();

        $filteredResults->each(function ($visitor) {
            $visitor->latestBookingDate = $visitor->latestBookingDate();
            $visitor->visitDate = $visitor->visitDate();
            $visitor->visitorstatus = $visitor->visitorstatus();
        });
        session()->put('search-query', $search);
        return response()->json($filteredResults);
    }

    public function export_pdf(Request $request)
    {
        $filteredData = session()->get('filtered-data');
        $searchQuery = session()->get('search-query');
        $chartVisibility = $request->input('chartVisibility');
        $chartImage = $request->input('chartImage');
        //dd($chartVisibility,$chartImage);
        if ($filteredData) {
            $visitors = $filteredData['visitors'];
            $selectedCountry = $filteredData['selectedCountry'];
            $fromdate = $filteredData['fromdate'];
            $todate = $filteredData['todate'];
            $currentDate = now();
            $fileName = 'VisitorsReport_' . $currentDate->format('Y-m-d') . '.pdf';

            $pdf = PDF::loadView('pdf.visitors', [
                'visitors' => $visitors,
                'selectedCountry' => $selectedCountry,
                'currentDate' => $currentDate,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
            ]);

            return $pdf->download($fileName);
        } elseif ($searchQuery) {
            $visitors = Visitors::where('firstName', 'like', "%$searchQuery%")
                ->orWhere('lastName', 'like', "%$searchQuery%")
                ->orWhere('email', 'like', "%$searchQuery%")
                ->get();
            $selectedCountry = null;
            $fromdate = null;
            $todate = null;
            $currentDate = now();
            $fileName = 'VisitorsReport_' . $currentDate->format('Y-m-d') . '.pdf';

            $pdf = PDF::loadView('pdf.visitors', [
                'visitors' => $visitors,
                'selectedCountry' => $selectedCountry,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'currentDate' => $currentDate,
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
            ]);

            return $pdf->download($fileName);
        } else {
            $visitors = Visitors::all();
            $selectedCountry = DB::table('visitors')->distinct()->pluck('country')->all();
            $fromdate = null;
            $todate = null;
            $currentDate = now();
            $fileName = 'VisitorsReport_' . $currentDate->format('Y-m-d') . '.pdf';

            $pdf = PDF::loadView('pdf.visitors', [
                'visitors' => $visitors,
                'selectedCountry' => $selectedCountry,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'currentDate' => $currentDate,
            ]);
            return $pdf->download($fileName);
        }
    }

    public function export_csv()
    {
        $filteredData = session()->get('filtered-data');
        $searchQuery = session()->get('search-query');
        $unfilteredData = session()->get('unfiltered-data');

        if ($filteredData) {
            $visitors = $filteredData['visitors'];
            $selectedCountry = $filteredData['selectedCountry'];
            $fromdate = $filteredData['fromdate'];
            $todate = $filteredData['todate'];
            $currentDate = now();
            $type = null;
            $fileName = 'VisitorsReport_' . $currentDate->format('Y-m-d') . '.xlsx';
            return Excel::download(new ExportVisitorsReport($visitors, $fromdate,$todate, $selectedCountry, $currentDate,$type), $fileName);
            session()->forget('filtered-data');
            session()->forget('search-query');
            session()->forget('unfiltered-data');
        } elseif ($searchQuery) {
            $visitors = Visitors::where('firstName', 'like', "%$searchQuery%")
                ->orWhere('lastName', 'like', "%$searchQuery%")
                ->orWhere('email', 'like', "%$searchQuery%")
                ->get();
            $selectedCountry = null;
            $fromdate = null;
            $todate = null;
            $type = 'search';
            $currentDate = now();
            $fileName = 'VisitorsReport_' . $currentDate->format('Y-m-d') . '.xlsx';
            return Excel::download(new ExportVisitorsReport($visitors, $fromdate,$todate, $selectedCountry, $currentDate,$type), $fileName);
            session()->forget('filtered-data');
            session()->forget('search-query');
            session()->forget('unfiltered-data');
        } else {
            $visitors = Visitors::all();
            $selectedCountry = DB::table('visitors')->distinct()->pluck('country')->all();
            $fromdate = null;
            $todate = null;
            $type = null;
            $currentDate = now();
            $fileName = 'VisitorsReport_' . $currentDate->format('Y-m-d') . '.xlsx';
            return Excel::download(new ExportVisitorsReport($visitors, $fromdate,$todate, $selectedCountry, $currentDate,$type), $fileName);
            session()->forget('filtered-data');
            session()->forget('search-query');
            session()->forget('unfiltered-data');
        }
    }
}
