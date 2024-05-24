<?php

namespace App\Http\Controllers;

use App\Exports\ExportRefundsReport;
use App\Models\Refund;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class RefundController extends Controller
{
    public function refund(Request $request)
    {
        $refunds = Refund::all();
        // $processedRequests = DB::table('refund_request_tables')->where('status', 'approve')->orWhere('status', 'disapprove')->get();
        return view('refund.refundRequest', compact('refunds'));
    }

    public function refundprocess(Request $request)
    {
        $bookingid = $request->get('bookingID');

        if ($request->get('choice') == "Approve") {
            $refund = DB::table('refund_request_tables')->select('*')->where('bookingID', $bookingid)->get()->first();

            $booking = DB::table('bookings')->select('*')->where('bookingID', $bookingid)->get()->first();

            $stripe = new \Stripe\StripeClient(config('stripe.sk'));

            $refunded = $stripe->refunds->create([
                'payment_intent' => $refund->paymentIntent,
                'amount' => ($booking->totalPrice * 100),
            ]);

            //DB::update('update refund_request_tables set status = ? where bookingID = ?', ['Approve', $bookingid]);
            DB::table('refund_request_tables')
                ->where('bookingID', $bookingid)
                ->update(['status' => 'Approved', 'approvedate' => now()]);
        } elseif ($request->get('choice') == "Disapprove") {
            //DB::update('update refund_request_tables set status = ? where bookingID = ?', ['Disapproved', $bookingid]);
            DB::table('refund_request_tables')
                ->where('bookingID', $bookingid)
                ->update(['status' => 'Disapproved', 'approvedate' => now()]);
        }
        return redirect('/refund');
    }

    // public function getProcessedRequests(){
    //     $processedRequests = DB::table('refund_request_tables')->where('status', 'approved')->orWhere('status','disapproved')->get();
    //     return view('refund.refundRequest',compact('processedRequests'));
    // }

    public function filter(Request $request)
    {
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');
       

        $refunds = Refund::whereDate('requestDate', '>=', $fromdate)
            ->whereDate('requestDate', '<=', $todate)
            ->get();

        // $processedRequests = $refunds->filter(function ($refund) {
        //     return $refund->status === 'approve' || $refund->status === 'disapprove';
        // });

        session()->put('filtered_requests', [
            'fromdate' => $fromdate,
            'todate' => $todate,
        ]);

        return view('refund.refundRequest', compact('refunds'));
    }

    public function exportPDF(Request $request){
        $chartVisibility = $request->input('chartVisibility');
        $chartImage = $request->input('chartImage');
        $requestType = $request->input('requestType');
        $filtered_requests = session()->get('filtered_requests');
        $fromdate = isset($filtered_requests['fromdate']) ? $filtered_requests['fromdate'] : null;
        $todate = isset($filtered_requests['todate']) ? $filtered_requests['todate'] : null;
        $totalAmount = 0;
        $currentDate = now();

        if ($requestType === 'all') {
            $refunds = Refund::all();
        } elseif ($requestType === 'pending') {
            $refunds = Refund::where('status', 'pending')->get();
        } elseif ($requestType === 'processed') {
            $refunds = Refund::where('status', 'Approved')
                ->orWhere('status', 'Disapproved')
                ->get();
        } elseif ($requestType === 'approved') {
            $refunds = Refund::where('status', 'Approved')->get();
            foreach ($refunds as $refund) {
                $totalAmount += $refund->priceRefund;
            }
        } elseif ($requestType === 'disapproved') {
            $refunds = Refund::where('status', 'Disapproved')->get();
            foreach ($refunds as $refund) {
                $totalAmount += $refund->priceRefund;
            }
        }

        $fileName = "Refund_Report_" . $currentDate->format('Y-m-d') . '.pdf';
            $pdf = PDF::loadView('pdf.refunds', ['chartVisibility' => $chartVisibility,'chartImage'=>$chartImage,'fromdate' => $fromdate, 'todate' => $todate, 'refunds' => $refunds, 'currentDate' => $currentDate, 'requestType' => $requestType, 'totalAmount' => $totalAmount]);
            Session::forget('filtered_requests');
            return $pdf->download($fileName);
    }

    public function exportCSV(Request $request){
        $chartVisibility = $request->input('chartVisibility');
        $chartImage = $request->input('chartImage');
        $requestType = $request->input('requestType');
        $filtered_requests = session()->get('filtered_requests');
        $fromdate = isset($filtered_requests['fromdate']) ? $filtered_requests['fromdate'] : null;
        $todate = isset($filtered_requests['todate']) ? $filtered_requests['todate'] : null;
        $totalAmount = 0;
        if ($requestType === 'all') {
            $refunds = Refund::all();
        } elseif ($requestType === 'pending') {
            $refunds = Refund::where('status', 'pending')->get();
        } elseif ($requestType === 'processed') {
            $refunds = Refund::where('status', 'Approved')
                ->orWhere('status', 'Disapproved')
                ->get();
        } elseif ($requestType === 'approved') {
            $refunds = Refund::where('status', 'Approved')->get();
            foreach ($refunds as $refund) {
                $totalAmount += $refund->priceRefund;
            }
        } elseif ($requestType === 'disapproved') {
            $refunds = Refund::where('status', 'Disapproved')->get();
            foreach ($refunds as $refund) {
                $totalAmount += $refund->priceRefund;
            }
        }
        $currentDate = now();
        $fileName = 'RefundsReport_' . $currentDate->format('Y-m-d') . '.xlsx';
            Session::forget('filtered_requests');
            return Excel::download(new ExportRefundsReport($refunds, $fromdate, $todate, $currentDate, $requestType, $totalAmount), $fileName);
    }

    // public function export(Request $request, $requestType, $exportType)
    // {
    //     $filtered_requests = session()->get('filtered_requests');
    //     $fromdate = isset($filtered_requests['fromdate']) ? $filtered_requests['fromdate'] : null;
    //     $todate = isset($filtered_requests['todate']) ? $filtered_requests['todate'] : null;
    //     $totalAmount = 0;
    //     if ($requestType === 'all') {
    //         $refunds = Refund::all();
    //     } elseif ($requestType === 'pending') {
    //         $refunds = Refund::where('status', 'pending')->get();
    //     } elseif ($requestType === 'processed') {
    //         $refunds = Refund::where('status', 'approve')
    //             ->orWhere('status', 'disapprove')
    //             ->get();
    //     } elseif ($requestType === 'approved') {
    //         $refunds = Refund::where('status', 'approve')->get();
    //         foreach ($refunds as $refund) {
    //             $totalAmount += $refund->priceRefund;
    //         }
    //     } elseif ($requestType === 'disapproved') {
    //         $refunds = Refund::where('status', 'disapprove')->get();
    //         foreach ($refunds as $refund) {
    //             $totalAmount += $refund->priceRefund;
    //         }
    //     }
    //     $currentDate = now();
    //     //return $totalAmount;
    //     if ($exportType == 'pdf') {
    //         $fileName = "RefundsReport_" . $currentDate->format('Y-m-d') . '.pdf';
    //         $pdf = PDF::loadView('pdf.refunds', ['fromdate' => $fromdate, 'todate' => $todate, 'refunds' => $refunds, 'currentDate' => $currentDate, 'requestType' => $requestType, 'totalAmount' => $totalAmount]);
    //         Session::forget('filtered_requests');
    //         return $pdf->download($fileName);
    //     } elseif ($exportType == 'excel') {
    //         //need to see if is pending or processed or all
    //         $fileName = 'RefundsReport_' . $currentDate->format('Y-m-d') . '.xlsx';
    //         Session::forget('filtered_requests');
    //         return Excel::download(new ExportRefundsReport($refunds, $fromdate, $todate, $currentDate, $requestType, $totalAmount), $fileName);
    //     }
    // }
}
