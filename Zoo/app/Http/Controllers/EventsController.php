<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Parks;
use App\Models\Events;
use Illuminate\Http\Request;
use App\DataTables\EventsDataTable;
use HTMLPurifier_AttrTransform_Input;
use Intervention\Image\Facades\Image;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(EventsDataTable $dataTable)
    // {
    //     $events = Events::all();
    //     return $dataTable->render('events.index',compact('events'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $park = Parks::query()->find((int)$id);
        return view('events.create', compact('park'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(),[
            'eventname' => 'required',
            // 'capacity' => 'required',
            'schedules' => 'required|array',
            'startDate' => 'required',
            // 'endDate' => 'required',
            'startTime' => 'required',
            'endTime' => 'required',
            'eventdesc' => 'required',
            // 'price' => 'required',
            //  'parkImage' => 'required|array|min:1',
            'eventImage' => 'required|mimes:jpg,jpeg', //,png,webp
        ],[
            'eventname'=> 'Event Name is required',
            // 'capacity'=> 'Capacity is required',
            'schedules' => 'Schedule is required',
            'startDate' => 'Start Date is required',
            // 'endDate' => 'End Date is required',
            'startTime' => 'Start time is required',
            'endTime' => 'End time is required',
            'eventdesc' => 'Description is required',
            // 'price' => 'Price is required',
            'eventImage' => 'Image is required and should have an extension of .jpeg, .jpg', //, .png, .webp
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //Add in database
        //Convert image file to image directory
        // $fileName = "";
        $imageFile = $request->file('eventImage');
        if($imageFile){
            $compressedImage = Image::make($request->file('eventImage')->getRealPath())->fit(1920, 1080);
        $base64 = base64_encode($compressedImage->encode('jpg', 80)->__toString());
            $fileName = $imageFile->getClientOriginalName();
            // $compressedImage->save(public_path('images/' . $fileName));
            $event = new Events;
            $eventName = ucwords(strtolower($request->input('eventname')));
            $event->eventName = $eventName;
            // $event->capacity = $request->input('capacity');
            $event->schedule = implode(",",$request->input('schedules'));
            $event->startDate = $request->input('startDate');
            $event->endDate = $request->input('endDate');
            $event->startTime = $request->input('startTime');
            $event->endTime = $request->input('endTime');
            $event->description = $request->input('eventdesc');
            $event->parkId = $request->input('selectedPark');
            $event->img_dir = $base64;
            $event->save();
            $parkid = $request->input('selectedPark');
            return redirect()->route('parks.show',['park'=>$parkid])->with('success','Event Added!');
        }
    }

     /**
      * Display the specified resource.
      */
     public function show(string $id)
     {
         $event = Events::query()->find((int)$id);
         return view('events.show', compact('event'));
     }

     /**
      * Show the form for editing the specified resource.
      */
     public function edit(string $id)
     {
        //  $parks = Parks::query()->where('parkId',$parkid);
         $event = Events::query()->find((int)$id);
         $event->startDate = $event->startDate->format('Y-m-d');
    $event->endDate = $event->endDate->format('Y-m-d');
         $schedule = explode(',', $event->schedule);
         return view('events.edit',compact('event','schedule'));
     }

     /**
      * Update the specified resource in storage.
      */
     public function update(Request $request, string $id)
     {
         $validator = FacadesValidator::make($request->all(),[
             'eventname' => 'required',
             // 'capacity' => 'required',
             'schedules' => 'required|array',
             'startDate' => 'required',
             // 'endDate' => 'required',
             'startTime' => 'required',
             'endTime' => 'required',
             'eventdesc' => 'required',
             //  'parkImage' => 'required|array|min:1',
             'eventImage' => 'mimes:jpeg,jpg', //,png,webp
         ],[
             'eventname'=> 'Event Name is required',
             // 'capacity'=> 'Capacity is required',
             'schedules' => 'Schedule is required',
             'startDate' => 'Start Date is required',
             // 'endDate' => 'End Date is required',
             'startTime' => 'Start time is required',
             'endTime' => 'End time is required',
             'eventdesc' => 'Description is required',
             'eventImage' => 'Image should have an extension of .jpg', //, .png, .webp
         ]);

         if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
         }

         //Add in database
         //Convert image file to image directory
         $fileName = "";
         $imageFile = $request->file('eventImage');
         if($imageFile){
            $compressedImage = Image::make($request->file('eventImage')->getRealPath())->fit(1920, 1080);
            $base64 = base64_encode($compressedImage->encode('jpg', 80)->__toString());
             $event = Events::query()->find((int)$id);
             $eventName = ucwords(strtolower($request->input('eventname')));
             $event->eventName = $eventName;
             // $event->capacity = $request->input('capacity');
             $event->schedule = implode(",",$request->input('schedules'));
             $event->startDate = $request->input('startDate');
             $event->endDate = $request->input('endDate');
             $event->startTime = $request->input('startTime');
             $event->endTime = $request->input('endTime');
             $event->description = $request->input('eventdesc');
             $event->img_dir = $base64;
             $event->save();

             return redirect('/events.show/'.$id)->with('success', 'Event Updated!');
        }else{
             //if image is null, means no change to the current image
             $event = Events::query()->find((int)$id);
             $eventName = ucwords(strtolower($request->input('eventname')));
             $event->eventName = $eventName;
             // $event->capacity = $request->input('capacity');
             $event->schedule = implode(",",$request->input('schedules'));
             $event->startDate = $request->input('startDate');
             $event->endDate = $request->input('endDate');
             $event->startTime = $request->input('startTime');
             $event->endTime = $request->input('endTime');
             $event->description = $request->input('eventdesc');
             $event->save();
             return redirect('/events.show/'.$id)->with('success', 'Event Updated!');
         }
     }

     /**
      * Remove the specified resource from storage.
      */
     public function destroy(string $id)
     {
         $event = Events::query()->find((int)$id);
         $parkid = $event->parkId;
         $event->delete();
         return redirect()->route('parks.show',['park'=>$parkid])->with('success','Event Deleted!');
     }
}
