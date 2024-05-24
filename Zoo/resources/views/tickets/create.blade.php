@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

<div class="form" style="background-color: #f1f1f1;">
    <h1>Create Ticket</h1>
    <br>
    <form method="POST" action="{{ action('App\Http\Controllers\TicketTypeController@store') }}" enctype="multipart/form-data">
        @csrf

            <div class="mb-3">
                <label for="ticketname" class="form-label mb-2">Ticket Name</label>
                <input type="text" class="form-control" id="ticketname" name="ticketname" required/>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label mb-2">Description (Optional)</label>
                <textarea class="form-control" placeholder="Enter event description" name="eventdesc" id="description" style="height: 100px;"></textarea>
            </div>

            <div class="mb-3">
                <label for="capacity" class="form-label mb-2">Available Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" min="1" required/>
            </div>

            <div class="mb-3">
                <label for="minpax" class="form-label mb-2">Minimum Pax (Optional)</label>
                <input type="number" class="form-control" id="minpax" name="minpax" min="1"/>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="join" class="text-md-end">{{ __('Valid from') }}</label>
                    <input type="datetime-local" class="form-control" name="validfrom">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="join" class="text-md-end">{{ __('Valid till') }}</label>
                    <input type="datetime-local" class="form-control" name="validtill">
                </div>
            </div>

            {{-- Loop through categories created to set pricing --}}
            @foreach ($demoCategories as $category)
            <div class="mb-3">
            <label for="{{ $category->demoCategoryName }}_local">Local Price for {{ $category->demoCategoryName }}:</label>
            <input type="number" class="form-control" name="{{ $category->demoCategoryName }}_local" step="0.01" min="1" required>
            
            </div>

            <div class="mb-3">
            <label for="{{ $category->demoCategoryName }}_foreigner">Foreigner Price for {{ $category->demoCategoryName }}:</label>
            <input type="number" class="form-control" name="{{ $category->demoCategoryName }}_foreigner" step="0.01" min="1" required>
            </div>
            @endforeach

            <div>
                <button type="submit" class="btn p-2 mt-3" name="addpark"
                    style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
                border-radius: 30px; border-color: #D5E200; color: black;">Create Ticket</button>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("input[type=datetime-local]", {
                minDate: "today"
            });
</script>
<script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    {{-- <script>
        ClassicEditor
            .create( document.querySelector( '#description' ) )
            .catch( error => {
                console.error( error );
            } );
    </script> --}}
@endpush
<br>
@endsection