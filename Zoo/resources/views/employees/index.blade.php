@extends('layouts.app')

@section('content')
<style>
    .viewbutton{
        background-color: #D5E200!important;
        font-family: 'Rubik', sans-serif;
    }
    .viewbutton:hover{
        background-color: #c4c853!important;
    }
    .btn-danger{
        font-family: 'Rubik', sans-serif;
        color: black;
    }
    .btn-danger:hover{
        color: #3C332A;
    }
    .modal-backdrop {
        width: 100%;
    }
    .hidetd{
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const employeeTypeFilter = document.getElementById('employeeTypeFilter');
        const searchInput = document.getElementById('search');
        const rows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();

            rows.forEach((row, index) => {
                const firstName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const lastName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

                // Check if any of the fields match the search value
                if (firstName.includes(searchValue) || lastName.includes(searchValue) || email.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        employeeTypeFilter.addEventListener('change', function () {
            const selectedType = this.value.toLowerCase();

            rows.forEach((row, index) => {
                const type = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                row.style.display = type.includes(selectedType) ? '' : 'none';
            });
        });
    });
</script>

<div class="container">
    <h1 class="d-inline">Employees</h1>
    <a href="{{route('employees.create')}}" class="btn viewbutton mx-2 float-end d-inline" style="font-family: 'Rubik', sans-serif;">New Employee</a>
    <br><br>
    <div class="categorysection m-0 mt-2">
    
    <div class="search-filter-container">
        <div class="search-bar">
            <label for="search">Search: </label>
            <input type="text" class="form-control border" id="search" placeholder="Search for employees">
        </div>
    </div>
    <br>
    <div class="dropdown">
        <label for="countryFilter">Filter by Employee Type: </label>
        <select class="form-select" id="employeeTypeFilter">
            <option value="">All</option>
            @foreach($employeeTypes as $type)
            <option value="{{ strtolower($type) }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <br>
    <table class="table" id="employeetable" style="width: 100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Type</th>
                <th>Email</th>
                <th></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if (count($employees)>0)
        @foreach ($employees as $employee)
            <tr>
                <td>{{$employee->id}}</td>
                <td>{{$employee->firstname}}</td>
                <td>{{$employee->lastname}}</td>
                <td>{{$employee->employeeType}}</td>
                <td>{{$employee->email}}</td>
                <td>{{$employee->created_at}}</td>
                <td>
                    <a href="{{route('employees.edit',['employee' => $employee->id])}}" class="btn viewbutton d-inline p-2" type="button">Edit</a>
                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline" id="deleteemployeeform-{{ $employee->id }}">
                        @csrf
                        @method('DELETE')
                        {{-- <button type="button" class="btn btn-danger" style="font-family: 'Rubik', sans-serif;" data-id="{{ $employee->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal{{$employee->id}}" onclick="confirmDelete('{{ $employee->id }}')">Delete</button> --}}
                        <button type="button" class="btn btn-danger" style="font-family: 'Rubik', sans-serif;" data-id="{{ $employee->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal{{$employee->id}}" onclick="confirmDelete('{{ $employee->id }}')">Delete</button>
                    </form>
                    {{-- <a href="{{route('employees.destroy',['employee' => $employee->id])}}" class="btn btn-danger">Delete</a> --}}
                </td>
            </tr>
        @endforeach
        @else
        <tr>
            <td colspan="6">No employees found</td>
            <td class="hidetd"></td>
            <td class="hidetd"></td>
            <td class="hidetd"></td>
            <td class="hidetd"></td>
            <td class="hidetd"></td>
            <td class="hidetd"></td>
        </tr>
    @endif
    </tbody>
    </table>
    
    </div>
</div>

<div class="modal fade" id="confirmationModal{{$employee->id}}" tabindex="-1" aria-labelledby="confirmationModalLabel{{$employee->id}}" aria-hidden="true" style="width: 100%;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="confirmationModalLabel{{$employee->id}}">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove {{$employee->firstname}} {{$employee->lastname}}?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDelete" style="font-family: 'Rubik', sans-serif;color:white;" onclick="deleteEmployee()">Delete</button>
            </div>
        </div>
    </div>
</div>



<script>
    function confirmDelete(employeeId) {
    $('#employeeIdInput').val(employeeId);
    //console.log(employeeId);
    $('#confirmationModal' + employeeId).modal('show');
}
    
        function deleteEmployee() {
            var employeeId = $('#employeeIdInput').val();
            $('#deleteemployeeform-' + employeeId).submit();
        }
    $(document).ready(function() {
    $('#employeetable').DataTable({
        "order": [[5, 'desc']],
        "columnDefs": [
                { "visible": false, "targets": 5 }
            ],
        lengthMenu: [25],
        pageLength: 25,
        searching: false,
        lengthChange: false,
        responsive: true
    });
    });
</script>
@endsection