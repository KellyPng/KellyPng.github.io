<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Parks;
use App\Models\Events;
use App\Models\Discounts;
use App\Models\TicketType;
use Illuminate\Support\Str;
use App\Models\DemoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class DiscountsController extends Controller
{
    public function index()
{
    $discounts = Discounts::query()->cursorPaginate(3);
    foreach ($discounts as $discount) {
        list($type, $itemId) = explode('_', $discount->discountFor);
        if ($type == 'ticket') {
            $discount->item = TicketType::findOrFail($itemId)->ticketTypeName;
        } elseif ($type == 'park') {
            $discount->item = Parks::findOrFail($itemId)->parkName;
        } else {
            $discount->item = 'All Parks';
        }
    }
    return view('discounts.view', compact('discounts'));
}

    public function manage(){
        $parks = Parks::all(); // Fetch all parks
        $ticketTypes = TicketType::all(); // Fetch all tickets
        $categories = DemoCategory::all();
        return view('discounts.manage', compact('parks', 'ticketTypes','categories'));
    }

// Store a newly created discount in storage.
public function store(Request $request)
{
    $validator = FacadesValidator::make($request->all(),[
        'discountFor' => 'required',
        'title' => 'required',
        'discount_percentage' => 'required|numeric|min:0|max:100',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'eligibility' => 'required|string',
        'description' => 'required|string',
    ]);
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
    // Generate the promo code
    // Extract the first letter of each word from the park name and make sure it's uppercase
    // $initials = implode('', array_map(function ($word) {
    //     return $word[0];
    // }, explode(' ', $validatedData['park_name'])));
    // $promoCode = strtoupper(substr($initials, 0, 1)) . strtoupper(Str::random(5));

    // Continue with the creation of the discount
    $discount = new Discounts;
    $discount->discountFor = $request->input('discountFor');
    $discount->title = $request->input('title');
    $discount->description = $request->input('description');
    $discount->discount_percentage = $request->input('discount_percentage');
    $discount->start_date = $request->input('start_date');
    $discount->end_date = $request->input('end_date');
    $discount->eligibility = $request->input('eligibility');
    $discount->save();

    return redirect()->route('discounts.index')->with('success', 'Discount created successfully.');
}


// Display the specified discount.
public function show($id)
{
    $discount = Discounts::findOrFail($id);
    return view('discounts.show', compact('discount'));
}

// Show the form for editing the specified discount.
public function edit($id)
{
    $discount = Discounts::findOrFail($id);
    //$park = Parks::findOrFail($discount->discountFor);
    list($type, $itemId) = explode('_', $discount->discountFor);

    if ($type == 'ticket') {
        $item = TicketType::findOrFail($itemId);
    } elseif ($type == 'park') {
        $item = Parks::findOrFail($itemId);
    }elseif ($type == 'all'){
        $item = 'All Parks';
    }
    $categories = DemoCategory::all();
    $parsedStartDate = strtotime($discount->start_date);
    $parsedEndDate = strtotime($discount->end_date);
    $formattedStartDate = date('Y-m-d H:i:s', $parsedStartDate);
    $formattedEndDate = date('Y-m-d H:i:s', $parsedEndDate);
    // dd($formattedStartDate,$formattedEndDate);
    $startdate = Carbon::parse($discount->start_date)->format('Y-m-d');
    $enddate = Carbon::parse($discount->end_date)->format('Y-m-d');
    // dd($startdate,$enddate);
    return view('discounts.edit', compact('discount','categories','item','formattedStartDate','formattedEndDate'));
}

// Update the specified discount in storage.
public function update(Request $request, $id)
{
    $validator = FacadesValidator::make($request->all(),[
        'title' => 'required',
        'discount_percentage' => 'required|numeric|min:0|max:100',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'eligibility' => 'required|string',
        'description' => 'required|string',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
    $discount = Discounts::findOrFail($id);
    $discount->title = $request->input('title');
    $discount->description = $request->input('description');
    $discount->discount_percentage = $request->input('discount_percentage');
    $discount->start_date = $request->input('start_date');
    $discount->end_date = $request->input('end_date');
    $discount->eligibility = $request->input('eligibility');
    $discount->save();

        // Redirect with a success message
        return redirect()->route('discounts.index')->with('success', 'Discount updated successfully.');
}

// Remove the specified discount from storage.
public function destroy($id)
{
    $discount = Discounts::findOrFail($id);
    $discount->delete();
    return redirect()->route('discounts.index')->with('success', 'Discount deleted successfully.');
}

   public function selectType()
    {
        $ticketTypes = TicketType::where('name', '!=', 'single park')->get();
        $parks = Parks::all();
        
        return view('discounts.select-type', compact('ticketTypes', 'parks'));
    }
}
