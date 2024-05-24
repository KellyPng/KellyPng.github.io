@extends('layouts.app')

@section('content')
<style>
    #singleparkticket-table td{
        cursor: pointer;
    }
</style>
{{-- <div class="mt-5 ms-5">
    <a href="{{ url('tickets') }}" class="show-park-button ">Go Back</a>
</div> --}}
<div class="container">
    <h1>Single Park Tickets</h1>
    {{-- <a href="{{ route('singleparktickets.create') }}" class="addparksbutton">Create New Tickets</a> --}}

    <div class="categorysection m-0 mt-3 mb-3" style="background-color: #f1f1f1; padding: 2%; border-radius: 5px;">
    {!!$dataTable->table(['style' => 'width: 100%;'])!!}
    </div>
</div>
    {{-- <div class="categorysection" style="background-color: #F3F2EF; padding: 2%; border-radius: 10px; margin: 3%; ">
        <h3>Manage Tickets</h3>
        <table>
            <tbody>
                @if (count($tickets)>0)
                    @foreach ($tickets as $ticket)
                        <tr>
                            <td><strong>{{ $ticket->park->parkName }}</strong></td>
                            <td><a href="{{ route('singleparktickets.edit', $ticket->id) }}" class="addparksbutton">Edit</a></td>
                            <td>
                                <form action="{{ action('App\Http\Controllers\SingleParkTicketController@destroy', $ticket->id) }}"
                                    id="deleteparkform" method="post">
                                    @csrf
                                    <input type="hidden" name="_method" value="delete" />
                                    {{-- <a href="" class="show-park-button-delete float-end" onclick="deletePark()">Delete</a> --}}
                                    {{--<button class="btn" type="submit"
                                        style="background-color: red; border-radius: 30px; padding: 10px; font-family: 'Rubik', sans-serif;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <p>No tickets created.</p>
                @endif
            </tbody>
        </table>
    </div> --}}
    {!!$dataTable->scripts()!!}

    <script>
    
        $(document).ready(function() {
            var table = $('#singleparkticket-table').DataTable();
    
            $("#singleparkticket-table").on("click", "tbody tr", function(e) {
                // Get the clicked row
                const clickedRow = table.row(this);
                
                // Check if the clicked cell is not in the first column (index 0), because resizing, there should be a dropdown button at the first column
                if (e.target.cellIndex > 0) {
                    const row = clickedRow.data();
                    if (row) {
                        window.location.href = "{{ route('singleparktickets.show2', ['singleparkticket' => ':id']) }}".replace(':id', row.id);
                    }
                }
            });
        });
    </script>
    <br>
@endsection