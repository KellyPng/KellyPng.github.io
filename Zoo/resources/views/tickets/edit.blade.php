@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="form mb-4">

        <h1>Edit Ticket</h1>
        <br>
        <form method="POST" action="{{ action('App\Http\Controllers\TicketTypeController@update', $ticket->id) }}"
            enctype="multipart/form-data">
            <input type="hidden" name="_method" value="put" />
            @csrf
            <div class="formconntainer">
                <div class="mb-3">
                    <label for="ticketname" class="form-label mb-2">Ticket Name</label>
                    <input type="text" class="form-control" id="ticketname" name="ticketname"
                        value="{{ $ticket->ticketTypeName }}" />
                    @error('ticketname')
                        <span class="ticket-error-message" role="alert" style="color: red">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label mb-2">Description (Optional)</label>
                    <textarea class="form-control" placeholder="Enter event description" name="eventdesc" id="description">{{$ticket->description}}</textarea>
                </div>
    
                <div class="mb-3">
                    <label for="capacity" class="form-label mb-2">Available Capacity</label>
                    <input type="number" class="form-control" id="capacity" name="capacity" value="{{$ticket->capacity}}" required/>
                </div>

                <div class="mb-3">
                    <label for="minpax" class="form-label mb-2">Minimum Pax (Optional)</label>
                    <input type="text" class="form-control" id="minpax" name="minpax"
                        value="{{ $ticket->minpax }}" />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('Valid from') }}</label>
                        <input type="datetime-local" class="form-control" name="validfrom" value="{{$ticket->validfrom}}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="join" class="text-md-end">{{ __('Valid till') }}</label>
                        <input type="datetime-local" class="form-control" name="validtill" value="{{$ticket->validtill}}">
                    </div>
                </div>

                @foreach ($demoCategories as $category)
                    <div class="mb-3">
                        <label for="{{ $category->demoCategoryName }}_local">Local Price for
                            {{ $category->demoCategoryName }}:</label>
                        <input type="number" class="form-control" name="{{ $category->demoCategoryName }}_local"
                            step="0.01" value="{{ $pricingData[$category->id]['local'] ?? '' }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="{{ $category->demoCategoryName }}_foreigner">Foreigner Price for
                            {{ $category->demoCategoryName }}:</label>
                        <input type="number" class="form-control" name="{{ $category->demoCategoryName }}_foreigner"
                            step="0.01" value="{{ $pricingData[$category->id]['foreigner'] ?? '' }}" required>
                    </div>
                @endforeach

                <div>
                    <button type="submit" class="btn p-2 mt-3" name="createticket"
                        style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
                border-radius: 30px; border-color: #D5E200; color: black;">Edit
                        Ticket</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            flatpickr("input[type=datetime-local]", {
                minDate: "today"
            });</script>
    {{-- <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script>
        ClassicEditor
            .create( document.querySelector( '#description' ) )
            .catch( error => {
                console.error( error );
            } );
    </script> --}}
    <br>
@endsection
