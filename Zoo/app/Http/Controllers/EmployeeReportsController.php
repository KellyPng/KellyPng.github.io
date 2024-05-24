<?php

namespace App\Http\Controllers;

use App\Exports\ExportEmployeeReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\EmployeeReports;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeReportsController extends Controller
{
    public function index()
    {
        $filter = 'last12months';
        $filteredReports = $this->getFilteredData($filter, null, null)->get();
        $reports = $filteredReports->map(function ($report) {
            return [
                'id' => $report->id,
                'subject' => $report->SUBJECT,
                'description' => $report->description,
                'email' => $report->email,
                'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $report->updated_at->format('Y-m-d H:i:s')
            ];
        });
        return view('employeeReports.index', compact('reports'));
    }

    public function show($report)
    {
        $report = EmployeeReports::query()->find($report);
        // dd($report->image);
        return view('employeeReports.show', compact('report'));
    }

    public function filter(Request $request)
    {
        $filter = $request->input('filter');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');
        $filteredReports = $this->getFilteredData($filter, $fromdate, $todate)->get();
        $reports = $filteredReports->map(function ($report) {
            return [
                'id' => $report->id,
                'subject' => $report->SUBJECT,
                'description' => $report->description,
                'email' => $report->email,
                'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $report->updated_at->format('Y-m-d H:i:s')
            ];
        });

        session()->put('filteredData',[
            'filter' => $filter,
            'fromdate' => $fromdate,
            'todate' => $todate,
            'reports' => $reports
        ]);
        return view('employeeReports.index', compact('reports'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $filteredResults = EmployeeReports::where('SUBJECT', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orWhere('email', 'LIKE', "%$search%")
            ->get();
        // $filteredResults->each(function ($report) {
        //     $report->formatted_created_at = Carbon::parse($report->created_at)->format('Y-m-d H:i:s');
        //     return $report;
        // });

        $formattedReports = $filteredResults->map(function ($report) {
            return [
                'id' => $report->id,
                'subject' => $report->SUBJECT,
                'description' => $report->description,
                'email' => $report->email,
                'created_at' => $report->created_at->format('Y-m-d H:i:s'),
            ];
        });
        session()->put('searchQuery',[
            'reports' => $formattedReports
        ]);
        return response()->json($formattedReports);
    }

    public function getFilteredData($filter, $startdate, $enddate)
    {

        switch ($filter) {
            case 'lastmonth':
                return EmployeeReports::whereYear('created_at', '=', Carbon::now()->subMonth()->year)
                    ->whereMonth('created_at', '=', Carbon::now()->subMonth()->month);
                break;
            case 'thismonth':
                return EmployeeReports::whereYear('created_at', '=', Carbon::now()->year)
                    ->whereMonth('created_at', '=', Carbon::now()->month);
                break;
            case 'thisweek':
                return EmployeeReports::whereBetween('created_at', [
                    Carbon::now()->startOfWeek()->format('Y-m-d'),
                    Carbon::now()->endOfWeek()->format('Y-m-d')
                ]);
                break;
            case 'last3months':
                return EmployeeReports::where('created_at', '>=', Carbon::now()->subMonths(3));
                break;
            case 'last6months':
                return EmployeeReports::where('created_at', '>=', Carbon::now()->subMonths(6));
                break;
            case 'last12months':
                return EmployeeReports::where('created_at', '>=', Carbon::now()->subMonths(12));
                break;
            case 'dateRange':
                return EmployeeReports::whereDate('created_at', '>=', $startdate)
                ->whereDate('created_at', '<=', $enddate);
                break;
            default:
                return EmployeeReports::all();
                break;
        }
    }

    public function exportPDF(Request $request)
    {
        $chartVisibility = $request->input('chartVisibility');
        $chartImage = $request->input('chartImage');
        $searchReports = session()->get('searchQuery');
        $filteredData = session()->get('filteredData');
        $currentDate = now();
        $fileName = 'EmployeeReports_'.$currentDate->format('Y-m-d').'.pdf';

        if ($filteredData) {
            $reports = $filteredData['reports'];
            $filter = $filteredData['filter'];
            $fromdate = $filteredData['fromdate'];
            $todate = $filteredData['todate'];

            $pdf = PDF::loadView('pdf.employeeReports',['reports'=>$reports,'filter' => $filter, 'fromdate'=>$fromdate,'todate'=>$todate,'chartVisibility'=>$chartVisibility,'chartImage'=>$chartImage,'currentDate'=>$currentDate]);
            session()->forget('filteredData');
            return $pdf->download($fileName);
        }elseif ($searchReports) {
            $reports = $searchReports['reports'];
            $filter = null;
            $fromdate = null;
            $todate = null;

            $pdf = PDF::loadView('pdf.employeeReports',['reports'=>$reports,'filter' => $filter, 'fromdate'=>$fromdate,'todate'=>$todate,'chartVisibility'=>$chartVisibility,'chartImage'=>$chartImage,'currentDate'=>$currentDate]);
            session()->forget('searchQuery');
            return $pdf->download($fileName);
        }else{
            $reports = EmployeeReports::all();
            $filter = null;
            $fromdate = null;
            $todate = null;

            $pdf = PDF::loadView('pdf.employeeReports',['reports'=>$reports,'filter' => $filter, 'fromdate'=>$fromdate,'todate'=>$todate,'chartVisibility'=>$chartVisibility,'chartImage'=>$chartImage,'currentDate'=>$currentDate]);
            return $pdf->download($fileName);
        }
    }

    public function exportCSV()
    {
        $searchReports = session()->get('searchQuery');
        $filteredData = session()->get('filteredData');
        $currentDate = now();
        $fileName = 'EmployeeReports_'.$currentDate->format('Y-m-d').'.xlsx';

        if ($filteredData) {
            $reports = $filteredData['reports'];
            $filter = $filteredData['filter'];
            $fromdate = $filteredData['fromdate'];
            $todate = $filteredData['todate'];

            return Excel::download(new ExportEmployeeReport($reports, $filter, $fromdate, $todate, $currentDate), $fileName);
        }elseif ($searchReports) {
            $reports = $searchReports['reports'];
            $filter = null;
            $fromdate = null;
            $todate = null;

            return Excel::download(new ExportEmployeeReport($reports, $filter, $fromdate, $todate, $currentDate), $fileName);
        }else{
            $reports = EmployeeReports::all();
            $filter = null;
            $fromdate = null;
            $todate = null;
            return Excel::download(new ExportEmployeeReport($reports, $filter, $fromdate, $todate, $currentDate), $fileName);
        }
    }
}
