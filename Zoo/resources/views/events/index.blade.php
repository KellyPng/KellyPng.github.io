@extends('layouts.app')

@section('content')

<h1 class="m-4 mt-5">Events</h1>

<div class="container ms-5">
    <div class="row">
        <div class="mt-4">
            <a href="{{route('events.create')}}" class="addparksbutton">Add Events</a>
        </div>
    </div>
</div>

<div class="parkstable m-4">
    {!!$dataTable->table(['style' => 'width: 100%;'])!!}
</div>
<br><br>
{!!$dataTable->scripts()!!}

<script>
    
    $(document).ready(function() {
        var table = $('#events-table').DataTable();

        $("#events-table").on("click", "tbody tr", function(e) {
            // Get the clicked row
            const clickedRow = table.row(this);
            
            // Check if the clicked cell is not in the first column (index 0), because resizing, there should be a dropdown button at the first column
            if (e.target.cellIndex > 0) {
                const row = clickedRow.data();
                if (row) {
                    window.location.href = "{{ route('events.show', ['event' => ':id']) }}".replace(':id', row.id);
                }
            }
        });
    });
</script>
@endsection