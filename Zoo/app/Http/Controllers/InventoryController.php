<?php

namespace App\Http\Controllers;

use App\Models\Parks;
use App\Models\Events;
use App\Models\TicketType;
use Illuminate\Http\Request;
use App\Models\SingleParkTicket;
use App\Models\SingleParkTicketAvailability;
use App\Models\TicketAvailability;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $activeTab = $request->input('active_tab','tickets');

        // Fetch only the tickets that are valid for the selected date
        $tickets = TicketType::where('validfrom', '<=', $date)
            ->where('validtill', '>=', $date)
            ->get();

            $singleParkTickets = SingleParkTicket::where('validfrom', '<=', $date)
            ->where('validtill', '>=', $date)
            ->get();

        // Initialize an array to store availability information for each ticket
        $availabilityData = [];
        $parkAvailabilityData = [];

        // Fetch availability for each ticket based on the selected date
        foreach ($tickets as $ticket) {
            $availability = TicketAvailability::where('ticketTypeId', $ticket->id)
                ->where('date', $date)
                ->first();

            // If availability record exists, store the data
            if ($availability) {
                $availabilityData[$ticket->id] = [
                    'available_quantity' => $availability->available_quantity,
                    'capacity' => $ticket->capacity,
                ];
            } else {
                // If no availability record exists for the date, assume full capacity
                $availabilityData[$ticket->id] = [
                    'available_quantity' => $ticket->capacity,
                    'capacity' => $ticket->capacity,
                ];
            }
        }

        foreach($singleParkTickets as $parkTicket){
            $parkAvailability = SingleParkTicketAvailability::where('parkTicketId',$parkTicket->id)
            ->where('date',$date)
            ->first();
            if ($parkAvailability) {
                $parkAvailabilityData[$parkTicket->id] = [
                    'available_quantity' => $parkAvailability->available_quantity,
                    'capacity' => $parkTicket->capacity,
                ];
            }else{
                $parkAvailabilityData[$parkTicket->id] = [
                    'available_quantity' => $parkTicket->capacity,
                    'capacity' => $parkTicket->capacity,
                ];
            }
        }

        // Pass the data to the view
        return view('inventory.index', compact('tickets', 'availabilityData', 'date', 'parkAvailabilityData','singleParkTickets','activeTab'));
    }

    public function addTicketCapacity(Request $request, string $id)
    {
        $additionalCapacity = $request->input('addCapacity');
        $ticket = TicketType::query()->find((int)$id);
        $ticket->capacity += $additionalCapacity;
        $ticket->remaining_quantity += $additionalCapacity;
        $ticket->save();

        $today = now()->format('Y-m-d');
        TicketAvailability::where('ticketTypeId', $ticket->id)
        ->where('date', '>', $today)
        ->increment('available_quantity', $additionalCapacity);

        return redirect()->back()->with('success', 'Capacity added successfully.');
    }

    public function addParkTicketCapacity(Request $request, string $id)
    {
        $additionalCapacity = $request->input('addCapacity');
        $ticket = SingleParkTicket::find((int)$id);
        $ticket->capacity += $additionalCapacity;
        $ticket->remaining_quantity += $additionalCapacity;
        $ticket->save();

        $today = now()->format('Y-m-d');
        SingleParkTicketAvailability::where('parkTicketId', $ticket->id)
        ->where('date', '>', $today)
        ->increment('available_quantity', $additionalCapacity);

        return redirect()->back()->with('success', 'Capacity added successfully.');
    }
}
