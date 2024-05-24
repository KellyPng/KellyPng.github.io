@extends('layouts.app')
@section('content')
    <div class="mt-4 ms-5 mb-2">
        <a href="{{ url('singleparktickets') }}" class="show-park-button">Go Back</a>
    </div>
    <div class="show-park-container mt-4">
        <h1 class="mt-4">{{ $ticket->park->parkName }}</h1>
        <table class="table table-bordered align-middle mt-5">
            <thead>
                <tr class="table-secondary">
                    <th>Category</th>
                    <th>Is Local</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pricings as $pricing)
                    <tr>
                        <td>{{ $pricing->category->demoCategoryName }}</td>
                        <td>{{ $pricing->is_local ? 'Yes' : 'No' }}</td>
                        <td>{{ $pricing->price }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr>
        <a href="{{ route('singleparktickets.edit', $ticket->id) }}" class="show-park-button float-start">Edit</a>
        
        {{-- <form action="{{ action('App\Http\Controllers\SingleParkTicketController@destroy',$ticket->id) }}" id="deleteparkform" method="post" class="float-end">
            @csrf
            <input type="hidden" name="_method" value="delete" />
            <button class="btn" type="submit" style="background-color: red; border-radius: 30px; padding: 15px;">Delete</button>
        </form> --}}
        <br><br>

    </div>
    <br>
@endsection
