@extends('layouts.app')
@section('content')
    {{-- <div class="mt-4 ms-5">
        <a href="{{ route('parks.show', ['park' => $park->id]) }}" class="show-park-button">Go Back</a>
    </div> --}}

    <div class="form">
        <h1>Edit Park</h1>
        <br>
        <form method="POST" action="{{ action('App\Http\Controllers\ParksController@update', $park->id) }}"
            enctype="multipart/form-data" name="editparksform">
            <input type="hidden" name="_method" value="put" />
            @csrf
            <div class="formconntainer">
                <div class="mb-3">
                    <label for="parkname" class="form-label mb-2">Name</label>
                    <input type="text" class="form-control" id="parkname" name="parkname" placeholder="Enter park name"
                        value="{{ $park->parkName }}" />
                    @error('parkname')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control" placeholder="Enter park description" name="parkdesc" id="description"
                        style="height: 150px">{{ $park->description }}</textarea>

                    @error('parkdesc')
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
                                accept=".png,.jpg,.jpeg" value="{{ $park->img_dir }}" hidden>
                        </div>
                        <div id="filename"></div>
                    </div>
                </div>
                <label for="parkImage">Current Image</label><br>
                <img src="data:image/jpeg;base64,{{ $park->img_dir }}" class="img-thumbnail mb-3" alt="Current Park Image"
                    style="max-width: 250px">
                <br>
                <input type="file" id="fileUploadInput" name="parkImage" accept=".jpg, .jpeg, .png"
                    value="{{ $park->img_dir }}" hidden />

                @error('parkImage')
                    <span class="park-error-message" role="alert">{{ $message }}</span><br><br>
                @enderror

                <div class="mb-3">
                    <label for="" class="mb-2">Schedule</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Monday" id="schedule1"
                            name="schedules[]" {{ in_array('Monday', $schedule) ? 'checked' : '' }}>
                        <label class="form-check-label" for="schedule1">
                            Monday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Tuesday" id="schedule2"
                            name="schedules[]" {{ in_array('Tuesday', $schedule) ? 'checked' : '' }}>
                        <label class="form-check-label" for="schedule2">
                            Tuesday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Wednesday" id="schedule3"
                            name="schedules[]" {{ in_array('Wednesday', $schedule) ? 'checked' : '' }}>
                        <label class="form-check-label" for="schedule3">
                            Wednesday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Thursday" id="schedule4"
                            name="schedules[]" {{ in_array('Thursday', $schedule) ? 'checked' : '' }}>
                        <label class="form-check-label" for="schedule4">
                            Thursday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Friday" id="schedule5"
                            name="schedules[]" {{ in_array('Friday', $schedule) ? 'checked' : '' }}>
                        <label class="form-check-label" for="schedule5">
                            Friday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Saturday" id="schedule6"
                            name="schedules[]" {{ in_array('Saturday', $schedule) ? 'checked' : '' }}>
                        <label class="form-check-label" for="schedule6">
                            Saturday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Sunday" id="schedule7"
                            name="schedules[]" {{ in_array('Sunday', $schedule) ? 'checked' : '' }}>
                        <label class="form-check-label" for="schedule7">
                            Sunday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Every Day" id="schedule8"
                            name="schedules[]" {{ in_array('Every Day', $schedule) ? 'checked' : '' }}>
                        <label class="form-check-label" for="schedule8">
                            Every Day
                        </label>
                    </div>
                    @error('schedules')
                        <br><span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="btn p-2 mt-3" name="addpark"
                        style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
                    border-radius: 30px; border-color: #D5E200; color: black;">Edit
                        Park</button>
                </div>
            </div>
        </form>
    </div>
    </div>
    <script src="{{ asset('js/uploadImage.js') }}"></script>
    {{-- <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script> --}}
    <script>
        // ClassicEditor
        //     .create(document.querySelector('#description'))
        //     .catch(error => {
        //         console.error(error);
        //     });

        $(document).ready(function() {
            // Function to handle the "Every Day" checkbox
            $('#schedule8').change(function() {
                if (this.checked) {
                    // Disable other checkboxes
                    $('[name^="schedules"]').not(this).prop('disabled', true);
                } else {
                    // Enable other checkboxes
                    $('[name^="schedules"]').prop('disabled', false);
                }
            });
        })
    </script>
    <br><br>
@endsection
