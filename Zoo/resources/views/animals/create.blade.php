@extends('layouts.app')
@section('content')
<div class="form mt-4">

    <h1>Add Animal for {{$park->parkName}}</h1>
    <br>
    <form method="POST" action="{{ action('App\Http\Controllers\AnimalsController@store') }}"
        enctype="multipart/form-data">
        @csrf
        <div class="formconntainer">

            <input type="number" name="selectedPark" id="selectedPark" value="{{ $park->id }}" hidden>
       
            <div class="mb-3">
                <label for="animalname" class="form-label mb-2">Name</label>
                <input type="text" class="form-control" id="animalname" name="animalname"
                    placeholder="Enter animal name" />
                @error('animalname')
                    <span class="park-error-message" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="" class="mb-2">Image</label>
                <div id="dragger_wrapper">
                    <div id="dragger">
                        <div class="imageicon"><i class="fa-solid fa-image"></i></div>
                        <h2 id="dragger_text">Drag and Drop Image</h2>
                        <h3>OR</h3>
                        <button type="button" class="btn btn-secondary browseFileBtn" onclick="browseFileHandler()"
                            style="font-family: 'Rubik', sans-serif">Browse File</button>
                        <input class="form-control" type="file" id="fileSelectorInput" name="imgdir"
                            accept=".png,.jpg,.jpeg" hidden>
                    </div>
                    <div id="filename"></div>
                </div>
            </div>
            <input type="file" id="fileUploadInput" name="animalimage" accept=".jpg, .jpeg, .png" hidden />
            @error('animalimage')
                <span class="park-error-message" role="alert">{{ $message }}</span><br><br>
            @enderror
        </div>

        <div>
            <button type="submit" class="btn p-2 mt-3" name="addanimal"
                style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
            border-radius: 30px; border-color: #D5E200; color: black;">Add
                Animal</button>
        </div>
    </form>
</div>

<br>
<script src="{{ asset('js/uploadImage.js') }}"></script>

@endsection