<?php

namespace App\Http\Controllers;

use App\Models\Pricing;
use App\Models\TicketType;
use App\Models\DemoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TicketAvailability;
use App\DataTables\PricingDataTable;
use App\DataTables\TicketTypeDataTable;
use App\DataTables\SingleParkTicketsDataTable;
use App\Models\Bookings;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class TicketTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $demoCategories = DemoCategory::all();
        $tickets = TicketType::orderBy('created_at', 'desc')->get();
        $pricings = Pricing::all();
        return view('tickets.index', compact('demoCategories', 'tickets', 'pricings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $demoCategories = DemoCategory::all();
        return view('tickets.create', compact('demoCategories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = TicketType::query()->find((int)$id);
        $pricings = Pricing::where('ticketTypeId', $ticket->id)->get();
        return view('tickets.show',compact('ticket','pricings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $demoCategories = DemoCategory::all();

        $ticket = new TicketType();
        $ticket->ticketTypeName = $request->input('ticketname');
        $ticket->minpax = $request->input('minpax');
        $ticket->description = $request->input('eventdesc');
        $ticket->capacity = $request->input('capacity');
        $ticket->validfrom = $request->input('validfrom');
        $ticket->validtill = $request->input('validtill') ?? now()->addYear();
        $ticket->remaining_quantity = $request->input('capacity');
        $ticket->ticket_type_img_dir = 0;
        $ticket->save();
        $ticketid = $ticket->id;

        foreach ($demoCategories as $category) {
            $localpricing = new Pricing();
            $localpricing->ticketTypeId = $ticketid;
            $localpricing->demoCategoryId = $category->id;
            $localpricing->is_local = true;
            $localpricing->price = $request->input($category->demoCategoryName . '_local');
            $localpricing->save();

            $foreignpricing = new Pricing();
            $foreignpricing->ticketTypeId = $ticketid;
            $foreignpricing->demoCategoryId = $category->id;
            $foreignpricing->is_local = false;
            $foreignpricing->price = $request->input($category->demoCategoryName . '_foreigner');
            $foreignpricing->save();
        }

        // Calculate the dates between validfrom and validtill
    $dates = collect();
    $currentDate = Carbon::parse($ticket->validfrom);

    while ($currentDate <= Carbon::parse($ticket->validtill)) {
        $dates->push($currentDate->toDateString());
        $currentDate->addDay();
    }

    // Create ticket availabilities for each date
    foreach ($dates as $date) {
        $availability = new TicketAvailability();
        $availability->ticketTypeId = $ticketid;
        $availability->date = $date;
        $availability->available_quantity = $request->input('capacity');
        $availability->save();
    }

        return redirect('tickets')->with('success', 'Ticket Created!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $demoCategories = DemoCategory::all();
        $ticket = TicketType::findOrFail($id);

        $pricing = Pricing::where('ticketTypeId', $ticket->id)->get();

        $pricingData = [];
        foreach ($pricing as $price) {
            $pricingData[$price->demoCategoryId][$price->is_local ? 'local' : 'foreigner'] = $price->price;
        }

        return view('tickets.edit', compact('ticket', 'demoCategories', 'pricingData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $demoCategories = DemoCategory::all();
        $ticket = TicketType::query()->find((int)$id);

        $originalCapacity = $ticket->capacity;
        // $originalValidFrom = $ticket->validfrom;
        // $originalValidTill = $ticket->validtill;
        $existingAvailabilities = TicketAvailability::where('ticketTypeId', $ticket->id)->get();

        $soldTicketsExist = Bookings::where('ticketTypeId', $ticket->id)
        ->whereBetween('bookingDate', [$request->input('validfrom'), $request->input('validtill')])
        ->exists();

    if ($soldTicketsExist) {
        return redirect()->back()->with('error', 'Cannot change dates as tickets have already been sold for the selected date range.');
    }

        $ticket->ticketTypeName = $request->input('ticketname');
        $ticket->minpax = $request->input('minpax');
        $ticket->capacity = $request->input('capacity');
        $ticket->description = $request->input('eventdesc');
        $ticket->validfrom = $request->input('validfrom');
        $ticket->validtill = $request->input('validtill');
        $ticket->save();

        foreach ($demoCategories as $category) {
            $localpricing = Pricing::where('ticketTypeId', $id)
                ->where('demoCategoryId', $category->id)
                ->where('is_local', 1)
                ->first();

            if ($localpricing) {
                $localpricing->price = $request->input($category->demoCategoryName . '_local');
                $localpricing->save();
            }

            $foreignpricing = Pricing::where('ticketTypeId', $id)
                ->where('demoCategoryId', $category->id)
                ->where('is_local', 0)
                ->first();

            if ($foreignpricing) {
                $foreignpricing->price = $request->input($category->demoCategoryName . '_foreigner');
                $foreignpricing->save();
            }
        }

        if ($originalCapacity != $ticket->capacity){
            foreach($existingAvailabilities as $oldavailability){
            $availableQuantity = max(0, $ticket->capacity - $oldavailability->sold);

            // Update the availability record
            $oldavailability->available_quantity = $availableQuantity;
            $oldavailability->save();
            }
        }

        return redirect('tickets')->with('success', 'Ticket Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = TicketType::query()->find((int)$id);
        $pricing = Pricing::where('ticketTypeId', $id);
        $ticket->availabilities()->delete();
        $ticket->delete();
        $pricing->delete();
        return redirect('tickets')->with('success', 'Ticket Removed!');
    }
}
