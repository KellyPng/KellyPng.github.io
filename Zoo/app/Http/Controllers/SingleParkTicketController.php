<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Parks;
use App\Models\DemoCategory;
use Illuminate\Http\Request;
use App\Models\SingleParkTicket;
use App\Models\ParkTicketPricing;
use App\DataTables\SingleParkTicketDataTable;
use App\DataTables\ParkTicketPricingDataTable;
use App\Models\SingleParkTicketAvailability;

class SingleParkTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SingleParkTicketDataTable $dataTable)
    {
        $pricings = ParkTicketPricing::all();
        return $dataTable->render('singleparktickets.index', compact('pricings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parks = Parks::all();
        $demoCategories = DemoCategory::all();
        return view('singleparktickets.create',compact('parks','demoCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create2(string $id)
    {
        $park = Parks::query()->find((int)$id);
        $demoCategories = DemoCategory::all();
        return view('singleparktickets.create2',compact('park','demoCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $demoCategories = DemoCategory::all();

    //     $ticket = new SingleParkTicket();
    //     $ticket->parkId = $request->input('selectedPark');
    //     $ticket->capacity = $request->input('capacity');
    //     $ticket->validfrom = $request->input('validfrom');
    //     $ticket->validtill = $request->input('validtill');
    //     $ticket->remaining_quantity = $request->input('capacity');
    //     dd($ticket);
    //     $ticket->save();
    //     $ticketid = $ticket->id;
    //     foreach ($demoCategories as $category) {
    //         $localpricing = new ParkTicketPricing();
    //         $localpricing->demoCategoryId = $category->id;
    //         $localpricing->parkTicketId = $ticketid;
    //         $localpricing->is_local = true;
    //         $localpricing->price = $request->input($category->demoCategoryName . '_local');
    //         $localpricing->save();

    //         $foreignpricing = new ParkTicketPricing();
    //         $foreignpricing->demoCategoryId = $category->id;
    //         $foreignpricing->parkTicketId = $ticketid;
    //         $foreignpricing->is_local = false;
    //         $foreignpricing->price = $request->input($category->demoCategoryName . '_foreigner');
    //         $foreignpricing->save();
    //     }

    //     return redirect('singleparktickets')->with('success', 'Ticket Created!');
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store2(Request $request)
    {
        $demoCategories = DemoCategory::all();

        $ticket = new SingleParkTicket();
        $ticket->parkId = $request->input('selectedPark');
        $ticket->capacity = $request->input('capacity');
        $ticket->validfrom = $request->input('validfrom');
        $ticket->validtill = $request->input('validtill');
        $ticket->remaining_quantity = $request->input('capacity');
        $ticket->save();
        $ticketid = $ticket->id;
        foreach ($demoCategories as $category) {
            $localpricing = new ParkTicketPricing();
            $localpricing->demoCategoryId = $category->id;
            $localpricing->parkTicketId = $ticketid;
            $localpricing->is_local = true;
            $localpricing->price = $request->input($category->demoCategoryName . '_local');
            $localpricing->save();

            $foreignpricing = new ParkTicketPricing();
            $foreignpricing->demoCategoryId = $category->id;
            $foreignpricing->parkTicketId = $ticketid;
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
    foreach ($dates as $date) {
        $availability = new SingleParkTicketAvailability();
        $availability->parkTicketId = $ticketid;
        $availability->date = $date;
        $availability->sold = 0;
        $availability->available_quantity = $request->input('capacity');
        $availability->save();
    }

        // return redirect('parks')->with('success', 'Park and Ticket Created!');
        return redirect()->route('parks.show', ['park' => $ticket->parkId])->with('success', 'Ticket Created!');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     $ticket = SingleParkTicket::query()->find((int)$id);
    //     $pricings = ParkTicketPricing::where('parkTicketId', $ticket->id)->get();
    //     return view('singleparktickets.show',compact('ticket','pricings'));
    // }

    /**
     * Display the specified resource.
     */
    public function show2(string $id)
    {
        $ticket = SingleParkTicket::where('parkId', (int)$id)->first();
        $pricings = ParkTicketPricing::where('parkTicketId', $ticket->id)->get();
        return view('singleparktickets.show2',compact('ticket','pricings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $demoCategories = DemoCategory::all();
        $ticket = SingleParkTicket::query()->find((int)$id);
        $parkId = $ticket->parkId;
        $park = Parks::query()->find((int)$parkId);
        $pricings = ParkTicketPricing::where('parkTicketId', $ticket->id)->get();
        $pricingData = [];
        foreach ($pricings as $pricing) {
            $pricingData[$pricing->demoCategoryId][$pricing->is_local ? 'local' : 'foreigner'] = $pricing->price;
        }
        return view('singleparktickets.edit',compact('ticket','park','demoCategories','pricingData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $demoCategories = DemoCategory::all();

        $ticket = SingleParkTicket::query()->find($id);
        $ticket->capacity = $request->input('capacity');
        $ticket->validfrom = $request->input('validfrom');
        $ticket->validtill = $request->input('validtill');
        $ticket->save();
        foreach ($demoCategories as $category) {
            $localpricing = ParkTicketPricing::where('parkTicketId', $id)
                ->where('demoCategoryId', $category->id)
                ->where('is_local', 1)
                ->first();

            if ($localpricing) {
                $localpricing->price = $request->input($category->demoCategoryName . '_local');
                $localpricing->save();
            }

            $foreignpricing = ParkTicketPricing::where('parkTicketId', $id)
                ->where('demoCategoryId', $category->id)
                ->where('is_local', 0)
                ->first();

            if ($foreignpricing) {
                $foreignpricing->price = $request->input($category->demoCategoryName . '_foreigner');
                $foreignpricing->save();
            }
        }
        $parkid = $ticket->parkId;
        return redirect('/parks/'.$parkid)->with('success', 'Ticket Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = SingleParkTicket::query()->find((int)$id);
        $parkid = $ticket->parkId;
        $pricing = ParkTicketPricing::where('parkTicketId', $id);
        $ticket->delete();
        $pricing->delete();
        $ticket->availabilities()->delete();
        return redirect('/parks/'.$parkid)->with('success', 'Ticket Removed!');
    }
}
