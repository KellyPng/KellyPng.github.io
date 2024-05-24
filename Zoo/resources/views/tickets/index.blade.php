@extends('layouts.app')

@section('content')
<style>
    #tickets td{
        cursor: pointer;
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
    .modal-backdrop {
        width: 100%;
    }
</style>
<div class="container">
    <h1 class="mb-4 d-inline">Tickets</h1>
    {{-- <a href="{{ route('singleparktickets.index') }}" class="btn viewbutton mx-2 float-end d-inline" style="font-family: 'Rubik', sans-serif;">Park Tickets</a> --}}
    <a href="{{ route('tickets.create') }}" class="btn viewbutton mx-2 float-end d-inline" style="font-family: 'Rubik', sans-serif;">New Ticket</a>
    
    <div class="categorysection m-0 mt-3 mb-3" style="background-color: #f1f1f1; padding: 2%; border-radius: 5px;">
        <h3>Categories</h3>
        @include('category.category')
    </div>
    
    <div class="categorysection m-0 mt-3" style="background-color: #f1f1f1; padding: 2%; border-radius: 5px;">
        <h3>Tickets Created</h3>
    @if (count($tickets) > 0)
        <table class="table" id="tickets" style="width: 100%;">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Ticket Name</th>
                    <th>Capacity</th>
                    <th>Valid From</th>
                    <th>Valid Till</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    @if ($ticket->id != 1)
                        <tr data-id="{{ $ticket->id }}">
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->ticketTypeName }}</td>
                            <td>{{$ticket->capacity}}</td>
                            <td>{{$ticket->validfrom}}</td>
                            <td>{{$ticket->validtill}}</td>
                            <td>{{ $ticket->updated_at }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <p>No Tickets Created.</p>
    @endif
    </div>
</div>

    <script>
        $(document).ready(function() {
            var table = $('#tickets').DataTable({
                "order": [[5, 'desc']],
                responsive: true
            });
    
            // Add a click event listener to the table rows
            $("#tickets tbody").on("click", "tr", function(e) {
                var rowData = table.row(this).data();
                //console.log('Row Data:', rowData);
                if (e.target.cellIndex > 0) {
                if (rowData) {
                    var id = rowData[0];
    
                    window.location.href = "{{ route('tickets.show', ['ticket' => ':id']) }}".replace(':id', id);
                }}
            });
        });
    </script>
@endsection
