@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
{{-- <div class="mt-5 ms-5">
    <a href="{{ url('singleparktickets') }}" class="show-park-button ">Go Back</a>
</div> --}}

<div class="form mb-4">

    <h1>Create Single Park Ticket</h1>
    <br>
    <form method="POST" action="{{ action('App\Http\Controllers\SingleParkTicketController@store') }}" enctype="multipart/form-data">
        @csrf
        <div class="formconntainer">
            <div class="mb-3">
                <label for="selectedPark">{{ __('Select a Park') }}</label>
                <select class="form-select form-control" id="selectedPark" name="selectedPark" required>
                    <option disabled selected>Select a Park</option>
                    @foreach ($parks as $park)
                        <option value="{{ $park->id }}">{{ $park->parkName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="capacity">Available Capacity</label>
                <input type="number" class="form-control" name="capacity" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="join" class="text-md-end">{{ __('Valid From') }}</label>
                    <input type="datetime-local" class="form-control" name="validfrom">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="join" class="text-md-end">{{ __('Valid Date') }}</label>
                    <input type="datetime-local" class="form-control" name="validtill">
                </div>
            </div>

            @foreach ($demoCategories as $category)
            <div class="mb-3">
            <label for="{{ $category->demoCategoryName }}_local">Local Price for {{ $category->demoCategoryName }}:</label>
            <input type="number" class="form-control" name="{{ $category->demoCategoryName }}_local" step="0.01" required>
            
            </div>

            <div class="mb-3">
            <label for="{{ $category->demoCategoryName }}_foreigner">Foreigner Price for {{ $category->demoCategoryName }}:</label>
            <input type="number" class="form-control" name="{{ $category->demoCategoryName }}_foreigner" step="0.01" required>
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
<br>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("input[type=datetime-local]", {
                minDate: "today"
            });
</script>
@endpush
    
@endsection