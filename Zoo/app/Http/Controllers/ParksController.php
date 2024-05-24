<?php

namespace App\Http\Controllers;

use App\Models\Parks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use App\DataTables\ParksDataTable;
use App\Models\Animals;
use App\Models\Events;
use App\Models\ParkTicketPricing;
use App\Models\SingleParkTicket;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\PDF;

class ParksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ParksDataTable $dataTable)
    {
        $parks = Parks::all();
        return view('parks.index', compact('parks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('parks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(),[
            'parkname' => 'required',
            'parkdesc' => 'required',
            //  'parkImage' => 'required|array|min:1',
            'parkImage' => 'required|mimes:jpg', //,png,webp
            'schedules' => 'required|array',
        ],[
            'parkname'=> 'Park Name is required',
            'parkdesc' => 'Description is required',
            'parkImage' => 'Image is required and should have an extension of .jpeg, .jpg', //, .png, .webp
            'schedules' => 'Schedule is required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
         
        //Add in database
        //Convert image file to image directory
        // $fileName = "";
        $imageFile = $request->file('parkImage');
        
        if($imageFile){
        $filename = uniqid() . '.' . $imageFile->getClientOriginalExtension();

        $compressedImage = Image::make($request->file('parkImage')->getRealPath())->fit(1920, 1080);
        $base64 = base64_encode($compressedImage->encode('jpg', 80)->__toString());
            $park = new Parks;
            $parkName = ucwords(strtolower($request->input('parkname')));
            $park->parkName = $parkName;
            $park->description = $request->input('parkdesc');
            $park->img_dir = $base64;
            $park->schedule = implode(",",$request->input('schedules'));
            $park->save();
            //dd($park->img_dir);
            $parkId = $park->id;
            $action = $request->input('action');
            if ($action == 'yes') {
                return redirect()->route('create2', ['id' => $parkId]);
            }else{
                return redirect('/parks')->with('success', 'Park Created!');
            }
            //return redirect()->route('create2',['id'=>$park->id])->with('success','Park Created successfully!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $park = Parks::query()->find((int)$id);
        $animals = $park->animals;
        $ticket = SingleParkTicket::where('parkId', $id)->first();
        $pricings = [];
        if ($ticket) {
            $pricings = ParkTicketPricing::where('parkTicketId', $ticket->id)->get();
        }
        $events = Events::where('parkId', $id)->get();
        return view('parks.show', compact('park','animals','ticket','pricings','events'));
        // $park = Parks::query()->find((int)$id);
        // if ($park) {
        //     $animals = $park->animals();
        //     return view('parks.show', compact('park', 'animals'));
        // } else {
        //     // Handle the case where the park is not found (e.g., redirect or show an error page)
        //     return 'No parks found';
        // }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $park = Parks::query()->find((int)$id);
        $schedule = explode(',', $park->schedule);
        return view('parks.edit',compact('park', 'schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = FacadesValidator::make($request->all(),[
            'parkname' => 'required',
            'parkdesc' => 'required',
            'parkImage' => 'mimes:jpeg,jpg', //,png
            'schedules' => 'required|array',
        ],[
            'parkname'=> 'Park Name is required',
            'parkdesc' => 'Description is required',
            'parkImage' => 'Image should have an extension of .jpg', //, .png, .webp
            'schedules' => 'Schedule is required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
         
        //Add in database
        //Convert image file to image directory
        $fileName = "";
        $imageFile = $request->file('parkImage');
        if($imageFile){
            $compressedImage = Image::make($request->file('parkImage')->getRealPath())->fit(1920, 1080);
        $base64 = base64_encode($compressedImage->encode('jpg', 80)->__toString());
            $park = Parks::query()->find((int)$id);
            $parkName = ucwords(strtolower($request->input('parkname')));
            $park->parkName = $parkName;
            $park->description = $request->input('parkdesc');
            $park->img_dir = $base64;
            $park->schedule = implode(",",$request->input('schedules'));
            $park->save();

            return redirect('/parks')->with('success', 'Park Updated!');
        }else{
            //if image is null, means no change to the current image
            $park = Parks::query()->find((int)$id);
            $parkName = ucwords(strtolower($request->input('parkname')));
            $park->parkName = $parkName;
            $park->description = $request->input('parkdesc');
            $park->schedule = implode(",",$request->input('schedules'));
            $park->save();

            return redirect('/parks')->with('success', 'Park Updated!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $park = Parks::query()->find((int)$id);
        $park->delete();
        $ticket = SingleParkTicket::where('parkId', $id)->first();

        //$pricing = ParkTicketPricing::where('parkTicketId', $ticket->id)->first();
        //

        if($ticket){
            $ticket->delete();
        }

        $animal = SingleParkTicket::where('parkId', $id)->first();
        if($animal){
            $animal->delete();
        }

        

        return redirect('/parks')->with('success', 'Park Removed!');
    }
    
}
