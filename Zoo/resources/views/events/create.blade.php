@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-maxlength/src/bootstrap-maxlength.js"></script>
@endpush

@section('content')
<style>
    .bootstrap-maxlength{
        margin-right: 20px;
    }
</style>
    {{-- <div class="mt-5 ms-5">
        <a href="{{ url('events') }}" class="show-park-button">Go Back</a>
    </div> --}}

    <div class="form mt-4">

        <h1>Add Event</h1>
        <br>
        <form method="POST" action="{{ action('App\Http\Controllers\EventsController@store') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="formconntainer">
                <div class="mb-3">
                    <label for="eventname" class="form-label mb-2">Name</label>
                    <input type="text" class="form-control" id="eventname" name="eventname"
                        placeholder="Enter event name" />
                    @error('eventname')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- <div class="mb-3">
                    <label for="capacity" class="form-label mb-2">Available Capacity</label>
                    <input type="number" class="form-control" id="capacity" name="capacity"
                        placeholder="Enter capacity" />
                    @error('capacity')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div> --}}

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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('Start Date') }}</label>
                        <input type="datetime-local" class="form-control" name="startDate">
                        @error('startDate')
                            <span class="park-error-message" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('End Date') }}</label>
                        <input type="datetime-local" class="form-control" name="endDate">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('Start Time') }}</label>

                        <div class="col-md-6">
                            <input class="form-control startTime" name="startTime">
                        </div>
                        @error('startTime')
                            <span class="park-error-message" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('End Time') }}</label>

                        <div class="col-md-6">
                            <input class="form-control endTime" name="endTime">
                        </div>
                        @error('endTime')
                            <span class="park-error-message" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descriptionnn">Description</label>
                    <textarea class="form-control" name="eventdesc" id="description" maxlength="100" style="height: 200px;"></textarea>

                    @error('eventdesc')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="selectedPark">{{ __('Park') }}</label>
                    <select class="form-select form-control" id="selectedPark" name="selectedPark">
                        <option selected value="{{ $park->id }}">{{ $park->parkName }}</option>
                    </select>
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
                <input type="file" id="fileUploadInput" name="eventImage" accept=".jpg, .jpeg, .png" hidden />
                @error('eventImage')
                    <span class="park-error-message" role="alert">{{ $message }}</span><br><br>
                @enderror

            </div>

            <div>
                <button type="submit" class="btn p-2 mt-3" name="addpark"
                    style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
                border-radius: 30px; border-color: #D5E200; color: black;">Add
                    Event</button>
            </div>
        </form>
    </div><br><br>
    @push('scripts')
        {{-- <script>
        // Word count limit
        var descriptionTextarea = document.getElementById('description');
        var wordCountDisplay = document.getElementById('wordCount');
    
        descriptionTextarea.addEventListener('input', function () {
            var words = this.value.trim().split(/\s+/).filter(Boolean).length;
            var maxLength = parseInt(this.getAttribute('maxlength'));
    
            if (words > maxLength) {
                // Trim excess words
                var trimmedValue = this.value.trim().split(/\s+/).filter(Boolean).slice(0, maxLength).join(' ');
                this.value = trimmedValue;
                words = maxLength;
            }
    
            wordCountDisplay.textContent = words + '/' + maxLength + ' words';
        });
    
        // Trigger input event on page load to initialize word count
        var event = new Event('input');
        descriptionTextarea.dispatchEvent(event);
    </script> --}}
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
        </script>
        <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
        {{-- <script>
            $('.form-control').maxlength({
                alwaysShow: true,
                validate: false,
                allowOverMax: true,
                customMaxAttribute: "90"
            });

            function updateWordCount(content) {
                // Use a regex pattern to split words (excluding spaces)
                var words = content.trim().split(/\s+/).filter(Boolean).length;
                var maxLength = parseInt(document.getElementById('description').getAttribute('maxlength'));

                if (words > maxLength) {
                    // Disable the editor if the word limit is reached or exceeded
                    editor.isReadOnly = true;

                    // Optionally, you can notify the user or handle the situation in another way
                    alert('Word limit reached. You cannot type or paste more than ' + maxLength + ' words.');
                } else {
                    // Re-enable the editor if within the word limit
                    editor.isReadOnly = false;
                }

                document.getElementById('wordCount').textContent = words + '/' + maxLength + ' words';
            }
        </script> --}}
    @endpush
@endsection
