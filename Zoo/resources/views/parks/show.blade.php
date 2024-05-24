@extends('layouts.app')

@section('content')
    <style>
        .viewbutton {
            background-color: #D5E200 !important;
        }

        .viewbutton:hover {
            background-color: #c4c853 !important;
        }

        .btn-danger {
            color: black;
            font-family: 'Rubik', sans-serif;
        }

        .btn-danger:hover {
            color: #3C332A;
        }

        #parkTabs .nav-link {
            color: #3C332A;
        }

        #parkTabs .nav-link.active {
            color: #bdc433;
        }

        h4 {
            color: #3C332A;
        }

        .modal-backdrop {
            width: 100%;
        }

        th {
            font-family: 'Rubik', sans-serif;
        }

        .hidetd {
            display: none;
        }
    </style>
    <div class="container">
        <div class="row">
            {{-- <div class="col">
                <a href="{{ url('parks') }}" class="show-park-button ms-0">Go Back</a>
            </div> --}}
            <div class="col text-center">
                <h1>{{ $park->parkName }}</h1>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <ul class="nav nav-tabs" id="parkTabs" style="display:flex;flex-direction:row;justify-content:initial;">
            <li class="nav-item">
                <a class="nav-link active" id="park-details-tab" data-bs-toggle="tab" href="#park-details">Park Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="park-ticket-tab" data-bs-toggle="tab" href="#park-ticket">Park Ticket</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="animals-tab" data-bs-toggle="tab" href="#animals">Animals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="events-tab" data-bs-toggle="tab" href="#events">Events</a>
            </li>
        </ul>

        <div class="tab-content mt-2">
            <div class="tab-pane fade show active" id="park-details" style="width: 100%">
                {{-- <div class="categorysection m-0 mt-3">
                <div class="text-center">
                    <img src="{{ asset('images/' . $park->img_dir) }}" alt="{{ $park->parkName }}" class="img-fluid rounded"
                        style="max-width: 65%">
                </div>
                <div class="container">
                    <h4 class="mt-5">Schedule</h4>
                    <p><span class="material-symbols-outlined">calendar_month</span>&nbsp;&nbsp;&nbsp;{{ $park->schedule }}
                    </p>
                    <h4>Description</h4>
                    <p>{!! $park->description !!}</p>


                    <div class="container mt-4 text-center">
                        <a href="{{ route('parks.edit', $park->id) }}" class="btn viewbutton mx-2"
                            style="font-family: 'Rubik', sans-serif;">Edit</a>
                        <form action="{{ action('App\Http\Controllers\ParksController@destroy', $park->id) }}"
                            method="post" class="d-inline" id="deleteparkform">
                            @csrf
                            <input type="hidden" name="_method" value="delete" />
                            <button class="btn btn-danger" type="button"
                                style="font-family: 'Rubik', sans-serif;" data-id="{{ $park->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal" onclick="confirmDelete('{{ $park->id }}')">Delete</button>
                        </form>
                    </div>
                </div></div> --}}


                <div class="categorysection m-0 mt-3">
                    <div class="card p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <img src="data:image/jpeg;base64,{{ $park->img_dir }}" alt="{{ $park->parkName }}" class="img-fluid rounded">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2 mt-3">
                                    <h4>Schedule</h4>
                                    <p>{{ $park->schedule }}</p>
                                </div>
                                <div class="mb-2">
                                    <h4>Description</h4>
                                    <p>{{ $park->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container mt-4 text-center">
                        <a href="{{ route('parks.edit', $park->id) }}" class="btn viewbutton mx-2"
                            style="font-family: 'Rubik', sans-serif;">Edit</a>
                        <form action="{{ action('App\Http\Controllers\ParksController@destroy', $park->id) }}"
                            method="post" class="d-inline" id="deleteparkform">
                            @csrf
                            <input type="hidden" name="_method" value="delete" />
                            <button class="btn btn-danger" type="button" style="font-family: 'Rubik', sans-serif;"
                                data-id="{{ $park->id }}" data-bs-toggle="modal" data-bs-target="#confirmationParkModal"
                                onclick="confirmDelete('{{ $park->id }}')">Delete</button>
                        </form>
                    </div>
                    <br>
                </div>
            </div>


            <div class="tab-pane fade" id="park-ticket" style="width: 100%">
                <div class="categorysection m-0 mt-3">

                    <h4 class="mt-5 d-inline table">Ticket Pricings</h4>
                    @if (!$ticket)
                        <a href="{{ route('create2', $park->id) }}" class="btn viewbutton mx-2 float-end d-inline"
                            style="font-family: 'Rubik', sans-serif;">Create Ticket</a>
                    @endif
                    <br>

                    <br>

                    <table class="table table-light align-middle mt-3" id="parkticketpricing" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Is Local</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($pricings) > 0)
                                @foreach ($pricings as $pricing)
                                    <tr>
                                        <td>{{ $pricing->category->demoCategoryName }}</td>
                                        <td>{{ $pricing->is_local ? 'Yes' : 'No' }}</td>
                                        <td>{{ $pricing->price }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" style="text-align: center;">No data found</td>
                                    <td class="hidetd"></td>
                                    <td class="hidetd"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <hr>
                    @if ($ticket)
                        <div class="container mt-4 text-center">
                            <a href="{{ route('singleparktickets.edit', $ticket->id) }}" class="btn viewbutton mx-2"
                                style="font-family: 'Rubik', sans-serif;">Edit
                                Pricing</a>
                            <form
                                action="{{ action('App\Http\Controllers\SingleParkTicketController@destroy', $ticket->id) }}"
                                id="deleteticketform" method="post" class="d-inline">
                                @csrf
                                <input type="hidden" name="_method" value="delete" />
                                <button class="btn btn-danger" type="button" data-id="{{ $ticket->id }}"
                                    data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                    onclick="confirmTicketDelete('{{ $ticket->id }}')">Delete</button>
                            </form>
                        </div>
                        <div class="modal fade" id="confirmationModal" tabindex="-1"
                            aria-labelledby="confirmationModalLabel" aria-hidden="true" style="width: 100%;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fs-5" id="confirmationModalLabel">Confirmation</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this ticket?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" id="confirmDelete"
                                            style="font-family: 'Rubik', sans-serif;color:white;"
                                            onclick="deleteTicket()">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="ticketIdInput" value="">
                    @endif
                    {{-- <hr> --}}
                    {{-- <a href="{{ route('singleparktickets.edit', $ticket->id) }}" class="show-park-button float-start">Edit</a> --}}

                    {{-- <form action="{{ action('App\Http\Controllers\SingleParkTicketController@destroy',$ticket->id) }}" id="deleteparkform" method="post" class="float-end">
                        @csrf
                        <input type="hidden" name="_method" value="delete" />
                        <button class="btn" type="submit" style="background-color: red; border-radius: 30px; padding: 15px;">Delete</button>
                    </form> --}}
                </div>

            </div>

            <div class="tab-pane fade" id="animals" style="width: 100%">
                <div class="categorysection m-0 mt-3">

                    <h4 class="mt-5 d-inline">Animals</h4>
                    <a href="{{ route('animals.create', $park->id) }}" class="btn viewbutton mx-2 d-inline float-end"
                        style="font-family: 'Rubik', sans-serif;">Add New Animal</a>
                    <br><br>


                    <table class="table" id="animalsTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Animal</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($animals) > 0)
                                @foreach ($animals as $animal)
                                    <tr>
                                        <td>{{ $animal->id }}</td>
                                        <td>{{ $animal->animalName }}</td>
                                        <td>{{ $animal->updated_at }}</td>
                                        <td>
                                            <button type="button" class="btn viewbutton" data-bs-toggle="modal"
                                                data-bs-target="#viewAnimalModal{{ $animal->id }}"
                                                style="font-family: 'Rubik', sans-serif;">
                                                View
                                            </button>

                                            <div class="modal fade" id="viewAnimalModal{{ $animal->id }}"
                                                tabindex="-1" aria-labelledby="viewAnimalModalLabel{{ $animal->id }}"
                                                aria-hidden="true" style="width: 100%;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5"
                                                                id="viewAnimalModalLabel{{ $animal->id }}">View Animal
                                                            </h1>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="">Name:
                                                            </label><span> {{ $animal->animalName }}</span><br>
                                                            <label for="">Image: </label><img src="data:image/jpeg;base64,{{ $animal->img_dir }}" class="img-thumbnail" alt="$animal->animalName">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a href="{{ route('animals.edit', $animal->id) }}"
                                                                class="btn viewbutton"
                                                                style="font-family: 'Rubik', sans-serif;">Edit</a>
                                                            <form
                                                                action="{{ action('App\Http\Controllers\AnimalsController@destroy', $animal->id) }}"
                                                                method="post" id="deleteanimalform">
                                                                @csrf
                                                                <input type="hidden" name="_method" value="delete" />
                                                                <button type="button" class="btn btn-danger"
                                                                    style="font-family: 'Rubik', sans-serif;"
                                                                    data-id="{{ $animal->id }}" data-bs-toggle="modal"
                                                                    data-bs-target="#confirmationAnimalModal"
                                                                    onclick="confirmDeleteAnimal('{{ $animal->id }}')">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="modal fade" id="confirmationAnimalModal" tabindex="-1"
                                                aria-labelledby="confirmationAnimalModalLabel" aria-hidden="true"
                                                style="width: 100%;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fs-5"
                                                                id="confirmationAnimalModalLabel">Confirmation</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete this animal?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger"
                                                                id="confirmDeleteAnimal"
                                                                style="font-family: 'Rubik', sans-serif;color:white;"
                                                                onclick="deleteAnimal()">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="animalIdInput" value="">
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" style="text-align: center;">No data found</td>
                                    <td class="hidetd"></td>
                                    <td class="hidetd"></td>
                                    <td class="hidetd"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="events" style="width: 100%">
                <div class="categorysection m-0 mt-3">

                    <h4 class="mt-5 d-inline">Events</h4>
                    <a href="{{ route('events.create', $park->id) }}" class="btn viewbutton mx-2 float-end d-inline"
                        style="font-family: 'Rubik', sans-serif;">Add Events</a>
                    <br><br>

                    <table class="table" id="eventstable" style="width: 100%;cursor: pointer;">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Event Name</th>
                                <th>Schedule</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($events) > 0)
                                @foreach ($events as $event)
                                    <tr>
                                        <td>{{ $event->id }}</td>
                                        <td>{{ $event->eventName }}</td>
                                        <td>{{ $event->schedule }}</td>
                                        <td>{{ $event->startDate->format('Y-m-d') }}</td>
                                        <td>{{ $event->endDate->format('Y-m-d') }}</td>
                                        <td>{{ $event->startTime }}</td>
                                        <td>{{ $event->endTime }}</td>
                                        <td>{{$event->created_at}}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" style="text-align: center;">No data found</td>
                                    <td class="hidetd"></td>
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
        </div>

        <div class="modal fade" id="confirmationParkModal" tabindex="-1" aria-labelledby="confirmationParkModalLabel"
            aria-hidden="true" style="width: 100%;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-5" id="confirmationParkModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this park?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="confirmDelete"
                            style="font-family: 'Rubik', sans-serif;color:white;" onclick="deletePark()">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="parkIdInput" value="">

        <script>
            function confirmDelete(parkId) {
                $('#parkIdInput').val(parkId);
                $('#confirmationParkModal').modal('show');
            }

            function deletePark() {
                var parkId = $('#parkIdInput').val();
                $('#deleteparkform').submit();
            }

            function confirmTicketDelete(ticketId) {
                $('#ticketIdInput').val(ticketId);
                $('#confirmationModal').modal('show');
            }

            function deleteTicket() {
                var ticketId = $('#ticketIdInput').val();
                $('#deleteticketform').submit();
            }

            function confirmDeleteAnimal(animalId) {
                $('#animalIdInput').val(animalId);
            }

            function deleteAnimal() {
                var animalId = $('#animalIdInput').val();
                $('#deleteanimalform').submit();
            }
            $(document).ready(function() {
                // For Tickets Table
                $('#animalsTable').DataTable({
                    "order": [[2, 'desc']],
                    responsive: true
                });
                $('#parkticketpricing').DataTable({
                    responsive: true
                });
                $('#eventstable').DataTable({
                    "order": [[7, 'desc']],
                    "columnDefs": [
                { "visible": false, "targets": 7 }
            ],
                    responsive: true
                });

                var table = $('#eventstable').DataTable();

                $("#eventstable").on("click", "tbody tr", function(e) {
                    const clickedRow = table.row(this);
                    const rowData = clickedRow.data();

                    if (e.target.cellIndex > 0) {
                        if (rowData && rowData[0] !== undefined) {
                            const eventId = rowData[0];
                            window.location.href = "{{ route('events.show', ['event' => ':id']) }}".replace(
                                ':id',
                                eventId);
                        } else {
                            console.error('Event ID is undefined or not present in rowData.');
                        }
                    }
                });
            });
        </script>
    @endsection
