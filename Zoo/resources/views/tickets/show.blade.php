@extends('layouts.app')
@section('content')
<style>
    .btn-edit {
        background-color: #D5E200;
        color: black;
        border-color: #D5E200;
        font-family: 'Rubik', sans-serif;
    }
    .btn-edit:hover {
        background-color: #c4c853;
        color: black;
        border-color: #c4c853;
    }
    .btn-delete {
        background-color: #DC3545;
        color: black;
        border-color: #DC3545;
        font-family: 'Rubik', sans-serif;
    }
    .btn-delete:hover {
        background-color: #c82333;
        color: black;
        border-color: #bd2130;
    }
    .modal-title {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .modal-backdrop {
        width: 100%;
    }
</style>

<div class="container mt-4">
    <div class="categorysection m-0 mt-3">
    <div class="row justify-content-between">
        <div class="col-8">
            <h2 class="d-inline">{{ $ticket->ticketTypeName }}</h2>

            <div>
                @if ($ticket->minpax)
                    <p class="mt-4"><strong>Minimum Pax:</strong> {{ $ticket->minpax }}</p>
                @endif
                <p class="mt-2"><strong>Capacity:</strong> {{ $ticket->capacity }}</p>
            </div>
        </div>
        <div class="col-4 text-end">
            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-edit">Edit</a>
            <button class="btn btn-delete" data-id="{{ $ticket->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal" onclick="confirmDelete('{{ $ticket->id }}')">Delete</button>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <table class="table table-bordered">
                <thead>
                    <tr>
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
        </div>
    </div></div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true" style="width: 100%;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this ticket?
            </div>
            <div class="modal-footer">
                <form action="{{ action('App\Http\Controllers\TicketTypeController@destroy', $ticket->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(ticketId) {
        $('#confirmationModal').modal('show');
    }
</script>

@endsection
