<?php

namespace App\Http\Controllers;

use App\Exports\ExportFeedbackReviewsReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\FeedbackReview;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class FeedbackReviewController extends Controller
{
    public function index()
    {
        $reviews = FeedbackReview::latest()->get();
        foreach ($reviews as $review) {
            $review->formatted_created_at = Carbon::parse($review->created_at)->format('Y-m-d');
        }

        return view('feedback_reviews.index', compact('reviews'));
    }

    public function updateVisibility(Request $request, $id)
    {
        $review = FeedbackReview::findOrFail($id);
        $review->is_visible = $request->has('is_visible');
        $review->save();
    
        return back();  // Redirect back to the same page
    }

    public function filter(Request $request){
        $stars = $request->input('starsFilter');
        $fromdate = $request->input('fromdate');
        $todate = $request->input('todate');

        if ($stars&&$stars!='all') {
            $reviews = FeedbackReview::where('stars', $stars)->get();
        }elseif ($stars=='all') {
            $reviews = FeedbackReview::all();
        }

        if ($fromdate&&$todate) {
            $reviews = FeedbackReview::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)->get();
        }

        session()->put('filteredReviewsData',[
            'fromdate'=>$fromdate,
            'todate'=>$todate,
            'stars'=>$stars,
            'reviews'=>$reviews
        ]);
        return view('feedback_reviews.index',compact('reviews'));
    }

    public function search(Request $request){
        $search = $request->input('search');
        $filteredResults = FeedbackReview::where('visitor_name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->get();

            $filteredResults->each(function ($feedback) {
                $feedback->formatted_created_at = Carbon::parse($feedback->created_at)->format('Y-m-d');
                return $feedback;
            });
        session()->put('search-query', $search);
        return response()->json($filteredResults);
    }

    public function exportPDF(Request $request){
        $chartVisibility = $request->input('chartVisibility');
        $chartImage = $request->input('chartImage');
        $filteredData = session()->get('filteredReviewsData');
        $searchQuery = session()->get('search-query');
        $currentDate = now();
        $fileName = 'FeedbackReviewsReport_'.$currentDate->format('Y-m-d').'.pdf';
        if ($filteredData) {
            $reviews = $filteredData['reviews'];
            $starsFilter = $filteredData['stars'];
            $fromdate = $filteredData['fromdate'];
            $todate = $filteredData['todate'];
            // dd('filtered, ',$reviews,$starsFilter,$fromdate,$todate);
            $pdf = PDF::loadView('pdf.feedbackReview', [
                'reviews' => $reviews,
                'starsFilter' => $starsFilter,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'currentDate' => $currentDate,
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
            ]);
            session()->forget('filteredReviewsData');
            return $pdf->download($fileName);
        }elseif ($searchQuery) {
            $reviews = FeedbackReview::where('visitor_name', 'like', "%$searchQuery%")
            ->orWhere('description', 'like', "%$searchQuery%")
            ->get();
            // dd('searched, ',$reviews);
            $starsFilter = null;
            $fromdate = null;
            $todate = null;
            $pdf = PDF::loadView('pdf.feedbackReview', [
                'reviews' => $reviews,
                'starsFilter' => $starsFilter,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'currentDate' => $currentDate,
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
            ]);
            session()->forget('search-query');
            return $pdf->download($fileName);
        }else {
            $reviews = FeedbackReview::all();
            // dd('all reviews, ',$reviews);
            $starsFilter = null;
            $fromdate = null;
            $todate = null;
            $pdf = PDF::loadView('pdf.feedbackReview', [
                'reviews' => $reviews,
                'starsFilter' => $starsFilter,
                'fromdate' => $fromdate,
                'todate' => $todate,
                'currentDate' => $currentDate,
                'chartVisibility' => $chartVisibility,
                'chartImage' => $chartImage,
            ]);

            return $pdf->download($fileName);
        }
    }

    public function exportCSV(){
        $filteredData = session()->get('filteredReviewsData');
        $searchQuery = session()->get('search-query');
        $currentDate = now();
        $fileName = 'FeedbackReviewsReport_'.$currentDate->format('Y-m-d').'.xlsx';
        if ($filteredData) {
            $reviews = $filteredData['reviews'];
            $starsFilter = $filteredData['stars'];
            $fromdate = $filteredData['fromdate'];
            $todate = $filteredData['todate'];
            return Excel::download(new ExportFeedbackReviewsReport($reviews,$starsFilter,$fromdate,$todate,$currentDate), $fileName);
        }elseif ($searchQuery) {
            $reviews = FeedbackReview::where('visitor_name', 'like', "%$searchQuery%")
            ->orWhere('description', 'like', "%$searchQuery%")
            ->get();
            $starsFilter = null;
            $fromdate = null;
            $todate = null;
            return Excel::download(new ExportFeedbackReviewsReport($reviews,$starsFilter,$fromdate,$todate,$currentDate), $fileName);
        }else{
            $reviews = FeedbackReview::all();
            $starsFilter = null;
            $fromdate = null;
            $todate = null;
            return Excel::download(new ExportFeedbackReviewsReport($reviews,$starsFilter,$fromdate,$todate,$currentDate), $fileName);
        }
    }
}
