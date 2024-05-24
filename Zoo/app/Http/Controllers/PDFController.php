<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PDFController extends Controller
{
    public function generatePDF(Request $request)
    {
        $bookingid = $request->input('bookingid');
        $booking = DB::table('bookings')->select('*')->where('bookingID', $bookingid)->get()->first();
        $tickettype = DB::table('ticket_types')->select('*')->where('id', $booking->ticketTypeID)->get()->first();
        $bookingdetail = DB::table('booking_details')->select('*')->where('bookID', '=', $booking->id)->where('quantity', '>=', 1)->get();
        $total = DB::table('payments')->select('amount')->where('visitorID', $booking->visitorID)->get()->first();
        $userid = DB::table('visitors')->select('*')->where('id', $booking->visitorID)->get()->first();
        $demoname = DB::select('select * from demo_categories');
        $qrstring = $booking->bookingID;
        if($tickettype->ticketTypeName == "Single Park"){
            $bookpark = DB::table('book_parks')->select('*')->where('bookingID', $booking->id)->get()->first();
            $single = DB::table('parks')->select('*')->where('id', $bookpark->parkID)->get()->first();
        }
        
        // $qrcode = base64_encode(QrCode::format('svg')->size(200)->errorCorrection('H')->generate($qrstring));
        if($tickettype->ticketTypeName == "Single Park"){
            $bookpark = DB::table('book_parks')->select('*')->where('bookingID', $booking->id)->get()->first();
            $single = DB::table('parks')->select('*')->where('id', $bookpark->parkID)->get()->first();
            // $data = ['title'=>'ZooWildlife Booking Confirmation','email'=>$userid->email, 'total'=>$total->amount, 'type'=>$tickettype->ticketTypeName, 'single'=>$single->parkName, 'bookingid'=>$bookingid, 'qrcode'=>$qrcode, 'name'=>$demoname, 'quan'=>$bookingdetail];
            $data = ['title'=>'ZooWildlife Booking Confirmation','email'=>$userid->email, 'total'=>$total->amount, 'type'=>$tickettype->ticketTypeName, 'single'=>$single->parkName, 'bookingid'=>$bookingid, 'name'=>$demoname, 'quan'=>$bookingdetail];
        }else{
            // $data = ['title'=>'ZooWildlife Booking Confirmation','email'=>$userid->email, 'total'=>$total->amount, 'type'=>$tickettype->ticketTypeName, 'bookingid'=>$bookingid, 'qrcode'=>$qrcode, 'name'=>$demoname, 'quan'=>$bookingdetail];
            $data = ['title'=>'ZooWildlife Booking Confirmation','email'=>$userid->email, 'total'=>$total->amount, 'type'=>$tickettype->ticketTypeName, 'bookingid'=>$bookingid, 'name'=>$demoname, 'quan'=>$bookingdetail];
        }
  
        $pdf = PDF::loadView('pdf.ticket', $data);
  
        return $pdf->download($bookingid.'.pdf');
    }
}