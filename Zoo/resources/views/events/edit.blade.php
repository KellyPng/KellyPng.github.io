@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-maxlength/src/bootstrap-maxlength.js"></script>
@endpush

@section('content')

    <div class="form">

        <h1>Edit Event</h1>
        <br>
        <form method="POST" action="{{ action('App\Http\Controllers\EventsController@update',$event->id) }}"
            enctype="multipart/form-data">
            @csrf
            <div class="formconntainer">
                {{-- <div class="mb-3">
                    <label for="eventTarget">Zoo-Wide or Individual Parks</label>
                    <select class="form-select" aria-label="Event Target" id="eventTarget">
                        <option selected>Select</option>
                        <option value="Zoo-Wide">Zoo-Wide</option>
                        <option value="Bird Paradise">Bird Paradise</option>
                        <option value="Tiger Territory">Tiger Territory</option>
                    </select>
                </div> --}}
                <div class="mb-3">
                    <label for="eventname" class="form-label mb-2">Name</label>
                    <input type="text" class="form-control" id="eventname" name="eventname"
                        placeholder="Enter event name" value="{{$event->eventName}}"/>
                    @error('eventname')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- <div class="mb-3">
                    <label for="capacity" class="form-label mb-2">Available Capacity</label>
                    <input type="number" class="form-control" id="capacity" name="capacity"
                        placeholder="Enter event name" value="{{$event->capacity}}"/>
                    @error('capacity')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div> --}}

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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('Start Date') }}</label>
                        <input type="datetime-local" class="form-control" name="startDate" value="{{$event->startDate}}" id="fromdate">
                        @error('startDate')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('End Date') }}</label>
                        <input type="datetime-local" class="form-control" name="endDate" value="{{$event->endDate}}" id="todate">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('Start Time') }}</label>

                        <div class="col-md-6">
                            <input class="form-control startTime" name="startTime" value="{{$event->startTime}}">
                        </div>
                        @error('startTime')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('End Time') }}</label>

                        <div class="col-md-6">
                            <input class="form-control endTime" name="endTime" value="{{$event->endTime}}">
                        </div>
                        @error('endTime')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label>{{ __('Description') }}</label>
                    <textarea class="form-control" placeholder="Enter event description" name="eventdesc" style="height: 150px" maxlength="200" id="description">{{$event->description}}</textarea>

                    @error('eventdesc')
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
                <label for="parkImage">Current Image</label><br>
                <img src="data:image/jpeg;base64,{{ $event->img_dir }}" class="img-thumbnail mb-3" alt="Current Event Image" style="max-width: 250px">
                <br>
                <input type="file" id="fileUploadInput" name="eventImage" accept=".jpg, .jpeg, .png" hidden />
                @error('eventImage')
                    <span class="park-error-message" role="alert">{{ $message }}</span><br><br>
                @enderror

            </div>

            <div>
                <button type="submit" class="btn p-2 mt-3" name="addpark"
                    style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
                border-radius: 30px; border-color: #D5E200; color: black;">Edit
                    Event</button>
            </div>
        </form>
    </div><br><br>
    @push('scripts')
        <script src="{{ asset('js/uploadImage.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            $(function() {
        'use strict';

        $('#description').maxlength({
            alwaysShow: true,
            warningClass: "badge bg-success",
            limitReachedClass: "badge bg-danger",
            // placement: 'bottom-right', // Ensure placement is set correctly
            customText: ' characters left', // Optional: custom text to append
            appendToParent: true // Ensure it is appended to the correct parent
        }).on('maxlength.shown', function() {
            $('.bootstrap-maxlength').css({
                'background-color': '#ffdddd',
            });
        });
    });
            flatpickr("input[type=datetime-local]", {
                minDate: "today"
            });

            $('.startTime').flatpickr({
                noCalendar: true,
                enableTime: true,
                dateFormat: 'h:i K'
            });

            $('.endTime').flatpickr({
                noCalendar: true,
                enableTime: true,
                dateFormat: 'h:i K'
            });
            flatpickr("#fromdate", {
                enableTime: false,
                dateFormat: "Y-m-d",
            });

            flatpickr("#todate", {
                enableTime: false,
                dateFormat: "Y-m-d",
            });
        </script>
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
        })
        </script>
    @endpush
@endsection
