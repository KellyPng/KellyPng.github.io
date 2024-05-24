@extends('layouts.app')

@section('content')
<style>
    .viewbutton{
        background-color: #D5E200!important;
    }
    .viewbutton:hover{
        background-color: #c4c853!important;
    }
    #parks-table td{
        cursor: pointer;
    }
</style>
<div class="container">
<h1 class="mt-5 d-inline">Parks</h1>
<a href="{{route('parks.create')}}" class="btn viewbutton mx-2 d-inline float-end" style="font-family: 'Rubik', sans-serif;">Add Parks</a>
<div class="categorysection m-0 mt-3" style="border-radius: 5px;">

{{-- {!!$dataTable->table(['style' => 'width: 100%;'])!!} --}}

<table class="table" id="parks" style="width: 100%;cursor: pointer;">
    <thead>
        <tr>
            <th>Id</th>
            <th>Park Name</th>
            <th>Schedule</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($parks as $park)
                <tr data-id="{{ $park->id }}">
                    <td>{{ $park->id }}</td>
                    <td>{{ $park->parkName }}</td>
                    <td>{{$park->schedule}}</td>
                    <td>{{$park->updated_at}}</td>
                </tr>
        @endforeach
    </tbody>
</table>

</div>
<br><br>
{{-- {!!$dataTable->scripts()!!} --}}
</div>
{{-- <script>
    $(document).ready(function() {
        var table = $('#parks-table').DataTable();

        $("#parks-table").on("click", "tbody tr", function() {
            const row = table.row(this).data();
            if (row) {
                window.location.href = "{{ route('parks.show', ['park' => ':id']) }}".replace(':id', row.id);
            }
        });
    });
</script> --}}

<script>
    
    $(document).ready(function() {
        var table = $('#parks').DataTable({
                "order": [[3, 'desc']],
                responsive: true
            });

        $("#parks").on("click", "tbody tr", function(e) {
            // Get the clicked row
            var clickedRow = table.row(this).data();
            console.log('Row Data:', clickedRow);
            // Check if the clicked cell is not in the first column (index 0), because resizing, there should be a dropdown button at the first column
            if (e.target.cellIndex > 0) {
                var id = clickedRow[0]; // Assuming the first column contains the ID
                if (id) {
                    window.location.href = "{{ route('parks.show', ['park' => ':id']) }}".replace(':id', id);
                }
            }
        });
    });
</script>


@endsection
        