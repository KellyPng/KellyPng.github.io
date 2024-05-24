@extends('layouts.app')
@section('content')
<style>
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
<div class="container mt-4">
    <h1 class="mb-4">{{ $event->eventName }}</h1>
    <div class="categorysection m-0 mt-3">
    <div class="card p-4">
    <div class="row">
        <div class="col-md-6">
            <div class="text-center">
                <img src="data:image/jpeg;base64,{{ $event->img_dir }}" alt="{{ $event->eventName }}" class="img-fluid rounded">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-2 mt-3">
                <h4>Schedule</h4>
                <p>{{ $event->schedule }}</p>
            </div>
            <div class="mb-2">
                <h4>Date</h4>
                @if ($event->endDate)
                    <p>{{$event->startDate->format('Y-m-d')}} till {{$event->endDate->format('Y-m-d')}}</p>
                @else
                    <p>{{$event->startDate}}</p>
                @endif
            </div>
            <div class="mb-2">
                <h4>Time</h4>
                <p>{{$event->startTime}} - {{$event->endTime}}</p>
            </div>
            <div>
                <h4>Description</h4>
                <p>{{ $event->description }}</p>
            </div>
        </div>
    </div>
    </div>

    

<div class="container mt-4 text-center">
    <a href="{{ route('events.edit', $event->id) }}" class="btn viewbutton mx-2" style="font-family: 'Rubik', sans-serif;">Edit</a>
    <form action="{{ action('App\Http\Controllers\EventsController@destroy', $event->id) }}" method="post" class="d-inline" id="deleteeventform">
        @csrf
        <input type="hidden" name="_method" value="delete" />
        <button class="btn btn-danger" type="button" style="font-family: 'Rubik', sans-serif;" data-id="{{ $event->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal" onclick="confirmDelete('{{ $event->id }}')">Delete</button>
    </form>
</div>
    <br></div></div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true" style="width: 100%;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="confirmationModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this event?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDelete" style="font-family: 'Rubik', sans-serif;color:white;" onclick="deleteEvent()">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="eventIdInput" value="">

    <script>
        function confirmDelete(eventId) {
            $('#eventIdInput').val(eventId);
            $('#confirmationModal').modal('show');
        }
    
        function deleteEvent() {
            var eventId = $('#eventIdInput').val();
            $('#deleteeventform').submit();
        }
    </script>
@endsection
