@extends('layouts.app')
@section('content')
    <style>
        .modal-backdrop {
        width: 100%;
    }
    .viewbutton{
        background-color: #D5E200!important;
    }
    .viewbutton:hover{
        background-color: #c4c853!important;
    }
    .btn-danger{
        color: black;
    }
    .btn-danger:hover{
        color: #3C332A;
    }
    </style>

    <div class="form">

        <h1>Add Park</h1>
        <br>
        <form method="POST" id="addParkForm" action="{{ action('App\Http\Controllers\ParksController@store') }}" enctype="multipart/form-data">
            @csrf
            <div class="formconntainer">
                <div class="mb-3">
                    <label for="parkname" class="form-label mb-2">Name</label>
                    <input type="text" class="form-control" id="parkname" name="parkname"
                        placeholder="Enter park name" />
                    @error('parkname')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="parkdesc"id="description" style="height: 100px;"></textarea>

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
                                accept=".png,.jpg,.jpeg" hidden>
                        </div>
                        <div id="filename"></div>
                    </div>
                </div>
                <input type="file" id="fileUploadInput" name="parkImage" accept=".jpg, .jpeg, .png" hidden />
                @error('parkImage')
                    <span class="park-error-message" role="alert">{{ $message }}</span><br><br>
                @enderror

                <div class="mb-3">
                    <label for="" class="mb-2">Schedule</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Monday" id="schedule1"
                            name="schedules[]">
                        <label class="form-check-label" for="schedule1">
                            Monday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Tuesday" id="schedule2"
                            name="schedules[]">
                        <label class="form-check-label" for="schedule2">
                            Tuesday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Wednesday" id="schedule3"
                            name="schedules[]">
                        <label class="form-check-label" for="schedule3">
                            Wednesday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Thursday" id="schedule4"
                            name="schedules[]">
                        <label class="form-check-label" for="schedule4">
                            Thursday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Friday" id="schedule5"
                            name="schedules[]">
                        <label class="form-check-label" for="schedule5">
                            Friday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Saturday" id="schedule6"
                            name="schedules[]">
                        <label class="form-check-label" for="schedule6">
                            Saturday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Sunday" id="schedule7"
                            name="schedules[]">
                        <label class="form-check-label" for="schedule7">
                            Sunday
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input me-1" type="checkbox" value="Every Day" id="schedule8"
                            name="schedules[]">
                        <label class="form-check-label" for="schedule8">
                            Every Day
                        </label>
                    </div>
                    @error('schedules')
                        <br><span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <input type="hidden" name="action" id="action">
                <div>
                    <button type="button" class="btn p-2 mt-3" data-bs-toggle="modal" data-bs-target="#confirmationModal" name="addpark" id="submitBtn"
                        style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
                    border-radius: 30px; border-color: #D5E200; color: black;">Add
                        Park</button>
                </div>
        </form>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true" style="width: 100%;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="confirmationModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Do you want to create ticket for this park as well?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="noBtn" style="font-family: 'Rubik', sans-serif;">No</button>
                    <button type="button" class="btn viewbutton" id="yesBtn" style="font-family: 'Rubik', sans-serif;">Yes</button>
                </div>
            </div>
        </div>
    </div>

    </div>

    <script src="{{ asset('js/uploadImage.js') }}"></script>
    <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script>
        // ClassicEditor
        //     .create( document.querySelector( '#description' ) )
        //     .catch( error => {
        //         console.error( error );
        //     } );
    
        $(document).ready(function () {
            // Function to handle the "Every Day" checkbox
            $('#schedule8').change(function () {
                if (this.checked) {
                    // Disable other checkboxes
                    $('[name^="schedules"]').not(this).prop('disabled', true);
                } else {
                    // Enable other checkboxes
                    $('[name^="schedules"]').prop('disabled', false);
                }
            });

            $('#yesBtn').click(function () {
                $('#action').val('yes');
                $('#addParkForm').submit();
            });

            $('#noBtn').click(function () {
                $('#action').val('no');
                $('#addParkForm').submit();
            });
        });
    </script>
    <br><br>
@endsection
