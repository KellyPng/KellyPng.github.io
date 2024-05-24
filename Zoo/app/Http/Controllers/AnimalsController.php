<?php

namespace App\Http\Controllers;

use App\Models\Animals;
use App\Models\Parks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Intervention\Image\Facades\Image;

class AnimalsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $animals = Animals::all();
        return view('animals.index',compact('animals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $park = Parks::query()->find((int)$id);
        return view('animals.create',compact('park'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(),[
            'animalname' => 'required',
            'animalimage' => 'required|mimes:jpg',
        ],[
            'animalname'=> 'Animal Name is required',
            'animalimage' => 'Image is required and should have an extension of .jpg', //, .png, .webp
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // $fileName = "";
        $imageFile = $request->file('animalimage');
        if($imageFile){
            $compressedImage = Image::make($request->file('animalimage')->getRealPath())->fit(1920, 1080);
        $base64 = base64_encode($compressedImage->encode('jpg', 80)->__toString());
            $animal = new Animals;
            $animal->parkId = $request->input('selectedPark');
            $animalName = ucwords(strtolower($request->input('animalname')));
            $animal->animalName = $animalName;
            $animal->img_dir = $base64;
            $animal->save();
            $parkid = $request->input('selectedPark');
            return redirect()->route('parks.show',['park'=>$parkid])->with('success','Animal Created!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $animal = Animals::query()->find((int)$id);
        $park = $animal->park;
        return view('animals.edit',compact('animal','park'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = FacadesValidator::make($request->all(),[
            'animalname' => 'required',
            'animalimage' => 'mimes:jpg', //,png,webp
        ],[
            'animalname'=> 'Animal Name is required',
            'animalimage' => 'Image should have an extension of .jpg', //, .png, .webp
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $fileName = "";
        $imageFile = $request->file('animalimage');
        if($imageFile){
            $compressedImage = Image::make($request->file('animalimage')->getRealPath())->fit(1920, 1080);
        $base64 = base64_encode($compressedImage->encode('jpg', 80)->__toString());
            $animal = Animals::query()->find((int)$id);
            $animalName = ucwords(strtolower($request->input('animalname')));
            $animal->animalName = $animalName;
            $animal->img_dir = $base64;
            $animal->save();
            $parkid = $animal->parkId;
            return redirect()->route('parks.show',['park'=>$parkid])->with('success','Animal Updated!');
        }else{
            //if image is null, means no change to the current image
            $animal = Animals::query()->find((int)$id);
            $animalName = ucwords(strtolower($request->input('animalname')));
            $animal->animalName = $animalName;
            $animal->save();
            $parkid = $animal->parkId;
            return redirect()->route('parks.show',['park'=>$parkid])->with('success','Animal Updated!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $animal = Animals::query()->find((int)$id);
        $parkid = $animal->parkId;
        $animal->delete();
        return redirect()->route('parks.show',['park'=>$parkid])->with('success','Animal Removed!');
    }
}
