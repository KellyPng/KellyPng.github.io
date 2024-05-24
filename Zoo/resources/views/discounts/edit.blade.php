
@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
<div class="form mt-4">
    <h1>Edit Discount</h1>
    
    <form action="{{ action('App\Http\Controllers\DiscountsController@update', $discount->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="discountFor">{{ __('Discount For: ') }}</label>
            <select name="discountFor" id="discountFor" class="form-select" aria-label="discountFor" disabled>
                @if(isset($item->ticketTypeName))
                    <option value="ticket_{{ $item->id }}" selected>
                        {{ $item->ticketTypeName }}
                    </option>
                @elseif(isset($item->parkName))
                    <option value="park_{{ $item->id }}" selected>
                        {{ $item->parkName }}
                    </option>
                @elseif(isset($item))
                    <option value="all_parks" selected>
                        {{ $item }}
                    </option>
                @endif
            </select>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label mb-2">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $discount->title }}"/>
            @error('title')
                <span class="park-error-message" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
            <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="{{ $discount->discount_percentage }}" required min="1" max="100">
            @error('discount_percentage')
                <span class="park-error-message" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="datetime-local" id="start_date" class="form-control" name="start_date" value="{{$formattedStartDate}}" required>
            @error('start_date')
                <span class="park-error-message" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="datetime-local" id="end_date" class="form-control" name="end_date" required value="{{$formattedEndDate}}">
            @error('end_date')
                <span class="park-error-message" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div class="eligibility-section mb-3">
            <label class="title">Who is eligible</label>

            {{-- Add the "All Visitors" option --}}
            <div class="form-check">
                <input class="form-check-input" type="radio" name="eligibility" id="allVisitors"
                    value="All Visitor" {{ old('eligibility') == 'All Visitor' ? 'checked' : '' }} checked>
                <label class="form-check-label" for="allVisitors">
                    All visitors
                </label>
            </div>

            {{-- Generate radio buttons based on categories --}}
            @foreach ($categories as $category)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="eligibility"
                        id="{{ $category->demoCategoryName }}" value="{{ $category->demoCategoryName }}" {{ old('eligibility') == $category->demoCategoryName ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $category->id }}">
                        {{ $category->demoCategoryName }}
                    </label>
                </div>
            @endforeach
            @error('eligibility')
                <span class="park-error-message" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required style="height: 100px;">{{ $discount->description }}</textarea>
            @error('description')
                <span class="park-error-message" role="alert">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <button type="submit" class="btn p-2 mt-3" name="addpark"
                style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
            border-radius: 30px; border-color: #D5E200; color: black;">Update Discount</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            flatpickr("input[type=datetime-local]", {
                minDate: "today"
            });
            flatpickr("#start_date", {
                enableTime: false,
                dateFormat: "Y-m-d",
            });

            </script>

{{-- <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script>
        ClassicEditor
            .create( document.querySelector( '#description' ) )
            .catch( error => {
                console.error( error );
            } );
    </script> --}}

@endsection
