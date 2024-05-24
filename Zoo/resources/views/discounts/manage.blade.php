@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <div class="container">
        <h1 class="m-4 mt-5">Create New Discount</h1>
        <div class="parkstable m-4">
            <form action="{{ route('discounts.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="discountFor">{{ __('Discount For: ') }}</label>

                    <select name="discountFor" id="discountFor" class="form-select" aria-label="discountFor">
                        <optgroup label="Ticket Types">
                            @foreach ($ticketTypes as $type)
                                @if ($type->id != 1)
                                <option value="ticket_{{ $type->id }}">{{ $type->ticketTypeName }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                        <optgroup label="Parks">
                            @foreach ($parks as $park)
                                <option value="park_{{ $park->id }}">{{ $park->parkName }}</option>
                            @endforeach
                            <option value="all_parks">All Parks</option>
                        </optgroup>
                    </select>

                    @error('discountFor')
                        <span class="text-danger" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label mb-2">Title</label>
                    <input type="text" class="form-control" id="title" name="title" />
                    @error('title')
                        <span class="park-error-message" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="discount">{{ __('Discount percentage (%)') }}</label>
                    <input type="number" id="discount" name="discount_percentage" class="form-control" placeholder="10"
                    min="1" max="100">
                    @error('discount_percentage')
                        <span class="text-danger" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="start_date">{{ __('Start Date') }}</label>
                    
                    <input type="datetime-local" id="start_date" class="form-control" name="start_date">
                    @error('start_date')
                        <span class="text-danger" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="end_date">{{ __('End Date') }}</label>
                    
                    <input type="datetime-local" id="end_date" class="form-control" name="end_date">
                    @error('end_date')
                        <span class="text-danger" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="eligibility-section mb-3">
                    <label class="title">Who is eligible</label>

                    {{-- Add the "All Visitors" option --}}
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="eligibility" id="allVisitors"
                            value="All Visitor" checked>
                        <label class="form-check-label" for="allVisitors">
                            All visitors
                        </label>
                    </div>

                    {{-- Generate radio buttons based on categories --}}
                    @foreach ($categories as $category)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="eligibility"
                                id="{{ $category->demoCategoryName }}" value="{{ $category->demoCategoryName }}">
                            <label class="form-check-label" for="{{ $category->id }}">
                                {{ $category->demoCategoryName }}
                            </label>
                        </div>
                    @endforeach
                </div>


                <div class="mb-3">
                    <label for="description">{{ __('Description') }}</label>
                    <textarea id="description" name="description" class="form-control" style="height: 100px;"></textarea>
                    @error('description')
                        <span class="text-danger" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="btn p-2 mt-3" name="addpark"
                        style="font-family: 'Rubik', sans-serif;background-color: #D5E200;
                    border-radius: 30px; border-color: #D5E200; color: black;">Add Discount</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            flatpickr("input[type=datetime-local]", {
                minDate: "today"
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
