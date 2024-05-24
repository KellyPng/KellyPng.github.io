@extends('layouts.app')
@section('content')
    <div class="form mt-4">

        <h1>Edit Animal</h1>
        <br>
        <form method="POST" action="{{ action('App\Http\Controllers\AnimalsController@update',$animal->id) }}"
            enctype="multipart/form-data">
            @csrf
            <div class="formconntainer">
           
                <div class="mb-3">
                    <label for="animalname" class="form-label mb-2">Name</label>
                    <input type="text" class="form-control" id="animalname" name="animalname"
                        placeholder="Enter animal name" value="{{$animal->animalName}}"/>
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
                <label for="animalImage">Current Image</label><br>
                <img src="data:image/jpeg;base64,{{ $animal->img_dir }}" class="img-thumbnail mb-3" alt="Current Animal Image" style="max-width: 250px">
                <br>
                <input type="file" id="fileUploadInput" name="animalimage" accept=".jpg, .jpeg, .png" hidden />
                @error('animalimage')
                    <span class="park-error-message" role="alert">{{ $message }}</span><br><br>
                @enderror
            </div>
    
            <div>
                <button type="submit" class="btn p-2 mt-3" name="editanimal"
                    style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
                border-radius: 30px; border-color: #D5E200; color: black;">Save</button>
            </div>
        </form>
    </div>
    
    <br>
    <script src="{{ asset('js/uploadImage.js') }}"></script>

@endsection