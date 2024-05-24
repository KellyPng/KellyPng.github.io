@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <style>
        .categorysection {
            background-color: #f1f1f1;
            padding: 2%;
            border-radius: 5px;
        }

        .modal-content {
            background-color: #F3F2EF;
        }

        table tbody th,
        td {
            font-family: 'Trocchi', serif;
            font-size: medium;
        }

        table td strong {
            color: red;
        }

        p {
            text-align: center;
        }

        .inventory-btn {
            background-color: #D5E200;
            font-family: 'Rubik', sans-serif;
        }

        .inventory-btn:hover {
            background-color: #D5E200;
        }

        .modal-title {
            font-family: 'Rubik', sans-serif;
        }

        .modal-backdrop {
            width: 100%;
        }

        table {
            border: 1px solid gray;
        }

        input {
            border: 1px solid #DEE2E6 !important;
        }

        #inventoryTabs .nav-link {
            color: #3C332A;
        }

        #inventoryTabs .nav-link.active {
            color: #bdc433;
        }

        .hidetd {
            display: none;
        }
    </style>
    <div class="container">
        <h1 class="mb-4 d-inline">Inventory</h1>

        <br><br>
        <label for="inventorydate">Filter by Date</label>
        <input type="datetime-local" class="form-control mb-4" id="inventorydate"
            value="{{ $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : '' }}">

        <div class="mt-4">
            <ul class="nav nav-tabs" id="inventoryTabs" style="display:flex;flex-direction:row;justify-content:initial;">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'tickets' ? 'active' : '' }}" id="tickets-tab" data-bs-toggle="tab" href="#tickets">Tickets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'single-park-tickets' ? 'active' : '' }}" id="single-park-tickets-tab" data-bs-toggle="tab" href="#single-park-tickets">Single Park
                        Tickets</a>
                </li>
            </ul>

            <div class="tab-content mt-2">
                <div class="tab-pane fade {{ $activeTab === 'tickets' ? 'show active' : '' }}" id="tickets" style="width: 100%">
                    <div class="categorysection m-0 mt-3">

                        <h3>Tickets</h3>

                        <table class="table" id="ticketsTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Ticket Name</th>
                                    <th>Available</th>
                                    {{-- <th>Sold</th> --}}
                                    <th>Capacity</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($tickets) > 0)
                                    @foreach ($tickets as $ticket)
                                        @if ($ticket->id != 1)
                                            <tr>
                                                <th class="col">{{ $ticket->ticketTypeName }}</th>
                                                <td class="col">
                                                    @if ($availabilityData[$ticket->id]['available_quantity'] == 0)
                                                        <strong>Sold Out</strong>
                                                    @else
                                                        {{ $availabilityData[$ticket->id]['available_quantity'] }}
                                                    @endif
                                                </td>
                                                {{-- <td class="col">
                                        @if ($ticket->quantity_sold == $ticket->capacity)
                                            <strong>Sold Out</strong>
                                        @else
                                            {{ $ticket->quantity_sold }}
                                        @endif
                                    </td> --}}
                                                <td class="col">
                                                    {{ $ticket->capacity }}
                                                <td class="w-auto">
                                                    @if (
                                                        $availabilityData[$ticket->id]['available_quantity'] == 0 &&
                                                            \Carbon\Carbon::parse($date)->startOfDay()->isAfter(now()->startOfDay()))
                                                        <button type="button" class="btn inventory-btn"
                                                            data-bs-toggle="modal" data-bs-target="#addTicketCapacityModal"
                                                            data-ticket-id="{{ $ticket->id }}">Add</button>

                                                        <div class="modal fade" id="addTicketCapacityModal" tabindex="-1"
                                                            aria-labelledby="addTicketCapacityModalLabel"aria-hidden="true"
                                                            style="width: 100%;">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="addTicketCapacityModalLabel">Add
                                                                            Capacity
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form
                                                                            action="{{ route('addTicketCapacity', $ticket->id) }}"
                                                                            method="post">
                                                                            @csrf
                                                                            <p>Current Capacity:
                                                                                <span>{{ $ticket->capacity }}</span>
                                                                            </p>
                                                                            <label for="addCapacity">Amount:</label>
                                                                            <input type="number" id="addCapacity"
                                                                                min="1" class="form-control"
                                                                                name="addCapacity" required>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn inventory-btn"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit"
                                                                            class="btn inventory-btn">Save
                                                                            changes</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" style="text-align: center;">No Tickets Found</td>
                                        <td class="hidetd"></td>
                                        <td class="hidetd"></td>
                                        <td class="hidetd"></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab === 'single-park-tickets' ? 'show active' : '' }}" id="single-park-tickets" style="width: 100%">
                    <div class="categorysection m-0 mt-3">

                        <h3>Single Park Tickets</h3>
                        <table class="table" id="singleParkTicketsTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Ticket Name</th>
                                    <th>Available</th>
                                    <th>Capacity</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($singleParkTickets)>0)
                                @foreach ($singleParkTickets as $parkTicket)
                                    <tr>
                                        <td>{{$parkTicket->id}}</td>
                                        <td>{{$parkTicket->park->parkName}}</td>
                                        <td>@if ($parkAvailabilityData[$parkTicket->id]['available_quantity'] == 0)
                                            <strong>Sold Out</strong>
                                        @else
                                            {{ $parkAvailabilityData[$parkTicket->id]['available_quantity'] }}
                                        @endif
                                    </td>
                                    <td>
                                        {{$parkTicket->capacity}}
                                        <td class="w-auto">
                                            @if (
                                                $parkAvailabilityData[$parkTicket->id]['available_quantity'] == 0 &&
                                                    \Carbon\Carbon::parse($date)->startOfDay()->isAfter(now()->startOfDay()))
                                                <button type="button" class="btn parkinventory-btn"
                                                    data-bs-toggle="modal" data-bs-target="#addParkTicketCapacityModal_{{ $parkTicket->id }}"
                                                    data-ticket-id="{{ $parkTicket->id }}">Add</button>

                                                <div class="modal fade" id="addParkTicketCapacityModal_{{ $parkTicket->id }}" tabindex="-1"
                                                    aria-labelledby="addParkTicketCapacityModalLabel_{{ $parkTicket->id }}" aria-hidden="true"
                                                    style="width: 100%;">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="addParkTicketCapacityModalLabel">Add
                                                                    Capacity
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form
                                                                    action="{{ route('addParkTicketCapacity', $parkTicket->id) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    <p>Current Capacity:
                                                                        <span>{{ $parkTicket->capacity }}</span>
                                                                    </p>
                                                                    <label for="addCapacity">Amount:</label>
                                                                    <input type="number" id="addCapacity"
                                                                        min="1" class="form-control"
                                                                        name="addCapacity" required>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn inventory-btn"
                                                                    data-bs-dismiss="modal">Close</button>
                                                                <button type="submit"
                                                                    class="btn inventory-btn">Save
                                                                    changes</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </td>
                                    </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="4" style="text-align: center;">No Tickets Found</td>
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

        </div>
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script>
                flatpickr("#inventorydate", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    defaultDate: "{{ $date ?? '' }}",
                    onChange: function(selectedDates, dateStr, instance) {
                        // Reload the page with the selected date as a parameter
                        window.location.href = '{{ url('inventory') }}?date=' + dateStr;
                    },
                });
            </script>
            <script>
                $(document).ready(function() {
                    $('#ticketsTable').DataTable({
                        responsive: true
                    });
                    $('#singleParkTicketsTable').DataTable({
                        responsive: true
                    });

                    $('#addTicketCapacityModal').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var ticketId = button.data('ticket-id');
                        var modal = $(this);
                        modal.find('form').attr('action',
                            '{{ route('addTicketCapacity', ['id' => '__ticketId__']) }}'.replace(
                                '__ticketId__', ticketId));
                    });

                    $('#addParkTicketCapacityModal').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var ticketId = button.data('ticket-id');
                        var modal = $(this);
                        modal.find('form').attr('action',
                            '{{ route('addParkTicketCapacity', ['id' => '__ticketId__']) }}'.replace(
                                '__ticketId__', ticketId));
                    });

                    $('.inventory-btn').on('click', function(event) {
    var button = $(this);
    var ticketId = button.data('ticket-id');
    var modal = $('#addParkTicketCapacityModal_' + ticketId);
    var form = modal.find('form');
    var action = form.attr('action');
    form.attr('action', action.replace('__ticketId__', ticketId));
});


                });
            </script>
        @endpush
        <br>
    @endsection
