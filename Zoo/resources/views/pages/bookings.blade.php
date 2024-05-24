@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <style>
        body {
            /* background-color: #F3F2EF; */
        }

        select {
            border: white 1px solid !important;
        }

        .hidetd {
            display: none;
        }
        input{
            border: 1px solid #DEE2E6 !important;
        }
        select{
            border: 1px solid #DEE2E6 !important;
        }
        .export-option{
            font-family: 'Rubik', sans-serif;
        }
    </style>
    <div class="container">
        <h1 class="d-inline">Bookings</h1>

        <div class="dropdown d-inline float-end">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="background-color: #D5E200; width: 100%; font-family: 'Rubik', sans-serif;">
                Export
            </button>
            <ul class="dropdown-menu">
                <li>
            <form id="exportPDFForm" method="POST" action="{{ route('export_bookings_pdf') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="chartVisibility" value="visible">
                <input type="hidden" name="chartImage" id="chartImageInput">
                <button type="submit" class="dropdown-item export-option" data-type="pdf" id="exportPDF">PDF</button>
            </form>
        </li>

                <li><a class="dropdown-item export-option" href="{{ route('export_bookings_csv') }}" id="exportCSV"
                        data-type="excel">Excel</a></li>
            </ul>
        </div>
        <br><br>
        <form id="date-form" enctype="multipart/form-data" method="GET" action="{{ route('bookings.filter') }}">
            @csrf
            <div class="dropdown form-group">
                <label for="dateFilter">Filter by:</label>
                <select class="form-select form-control" id="dateFilter" name="dateFilter">
                    <option value="all" {{ Request::input('dateFilter') === 'all' ? 'selected' : '' }} selected>All
                    </option>
                    <option value="visitdate" {{ Request::input('dateFilter') === 'visitdate' ? 'selected' : '' }}>Visit
                        Date
                    </option>
                    <option value="bookingdate" {{ Request::input('dateFilter') === 'bookingdate' ? 'selected' : '' }}>
                        Booking Date</option>
                </select>
            </div>
            <br>
            <select class="form-select d-inline float-start" id="filter" name="filter" {{ Request::input('dateFilter') === 'all' ? 'disabled' : '' }}>
                <option value="last12months" {{ Request::input('filter') === 'last12months' ? 'selected' : '' }}>Last 12
                    months</option>
                <option value="last6months" {{ Request::input('filter') === 'last6months' ? 'selected' : '' }}>Last 6 months
                </option>
                <option value="last3months" {{ Request::input('filter') === 'last3months' ? 'selected' : '' }}>Last 3 months
                </option>
                <option value="lastmonth" {{ Request::input('filter') === 'lastmonth' ? 'selected' : '' }}>Last month
                </option>
                <option value="thismonth" {{ Request::input('filter') === 'thismonth' ? 'selected' : '' }}>This month
                </option>
                <option value="dateRange" {{ Request::input('filter') === 'dateRange' ? 'selected' : '' }}>Select date range
                </option>
            </select>
            <br><br>
            <div class="form-group row">
                <label for="date" class="col-md-2 col-form-label">Filter Date</label>
                <div class="col-md-5">
                    <input type="date" name="fromdate" id="fromdate" class="form-control"
                        value="{{ Request::input('fromdate') }}"
                        {{ Request::input('dateFilter') === 'all' ? 'disabled' : '' }} placeholder="From">
                </div>
                <div class="col-md-5">
                    <input type="date" name="todate" id="todate" class="form-control"
                        value="{{ Request::input('todate') }}"
                        {{ Request::input('dateFilter') === 'all' ? 'disabled' : '' }} placeholder="To">
                </div>
            </div>
            <br>
        </form>
        <div class="categorysection m-0 mt-3" style="border-radius: 5px;">
        <button id="toggleChartButton" class="d-inline float-end align-items-center p-1"
            style="font-family: 'Rubik', sans-serif; background-color:lightgray; border:none; border-radius:25px;"><span
                class="material-symbols-outlined me-1" style="vertical-align: middle; line-height: 1;">
                visibility_off
            </span>Hide Chart</button>
        <br>
        <canvas id="bookingsChart" width="400" height="200"></canvas>
        <br>

        <table class="table" id="bookings" style="width: 100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Visitor Name</th>
                    <th>Ticket Type</th>
                    <th>Visit Date</th>
                    <th>Booking Date</th>
                    <th>Ticket Status</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>eTicket</th>
                </tr>
            </thead>
            <tbody>
                @if (count($bookings) > 0)
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>{{ $booking->bookingID }}</td>
                            <td>{{ $booking->visitor->firstName }} {{ $booking->visitor->lastName }}</td>
                            <td>
                                @if ($booking->ticketType->ticketTypeName == 'Single Park')
                                    {{ $booking->ticketType->ticketTypeName }} : {{ $booking->bookParks->park->parkName }}
                                @else
                                    {{ $booking->ticketType->ticketTypeName }}
                                @endif
                            </td>
                            <td>{{ $booking->bookingDate }}</td>
                            <td>{{ $booking->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <span
                                    style="background-color: {{ $booking->bookingStatus == 0 ? 'green' : 'red' }};
                                color: white;
                                font-family: 'Rubik', sans-serif;
                                padding: 3px;
                                border-radius: 3px;">
                                    {{ $booking->bookingStatus == 0 ? 'Valid' : 'Used' }}
                                </span>
                            </td>
                            <td>
                                @if (isset($demographicQuantities[$booking->id]))
                                    @foreach ($demographicQuantities[$booking->id] as $categoryName => $quantity)
                                        {{ $categoryName }} : {{ $quantity }}<br>
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ $booking->totalPrice }}</td>
                            <td>
                                <form action="/generate-pdf" method="POST">
                                    @csrf
                                    <input type="hidden" name="bookingid" id="bookingid"
                                        value="{{ $booking->bookingID }}">
                                    <button type="submit" class="btn btn-warning"
                                        style="font-family: 'Rubik', sans-serif;">Download</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9">No bookings found</td>
                        <td class="hidetd"></td>
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
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align: right;">Total:</th>
                    <th>{{ $totalQuantity }}</th>
                    <th colspan="2">$ {{ $totalPrice }}</th>
                </tr>
            </tfoot>
        </table>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                flatpickr("#fromdate", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr, instance) {
                        document.getElementById('fromdate').value = dateStr;
                        checkDateSelection();
                    },
                });

                flatpickr("#todate", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr, instance) {
                        document.getElementById('todate').value = dateStr;
                        checkDateSelection();
                    },
                });

                function prepareBookingsChartData(bookingData) {

var labels = Object.keys(bookingData);
var counts = Object.values(bookingData);

return {
    labels: labels,
    datasets: [{
        label: 'Bookings Count',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        tension: 0.4,
        data: counts
    }]
};
}

function renderBookingsChart(bookingData) {
var ctx = document.getElementById('bookingsChart').getContext('2d');
var chartData = prepareBookingsChartData(bookingData);

var bookingsChart = new Chart(ctx, {
    type: 'line',
    data: chartData,
});
}
                renderBookingsChart(@json($bookingsCount));

                $('#toggleChartButton').click(function() {
                    var chart = $('#bookingsChart');
                    var button = $(this);
                    var visibility = chart.is(':visible') ? 'hidden' : 'visible';

                    // Toggle chart visibility
                    if (chart.is(':visible')) {
                        chart.hide();
                        button.html(
                            '<span class="material-symbols-outlined me-1" style="vertical-align: middle; line-height: 1;">visibility</span>View Chart'
                        );
                    } else {

                        chart.show();
                        button.html(
                            '<span class="material-symbols-outlined me-1" style="vertical-align: middle; line-height: 1;">visibility_off</span>Hide Chart'
                        );

                    }

                    // Update export links with chart visibility parameter
                    $('.export-option').each(function() {
                        var currentHref = $(this).attr('href');
                        var newHref = currentHref.replace(/(chartVisibility=)[^\&]+/, '$1' +
                            visibility);
                        $(this).attr('href', newHref);
                    });
                });
            });

            function checkDateSelection() {
                var fromDate = document.getElementById('fromdate').value;
                var toDate = document.getElementById('todate').value;

                if (fromDate && toDate) {
                    document.getElementById('date-form').submit();
                } else {
                    document.getElementById('bookings').innerHTML = '<p style="color: red;">Please select both dates.</p>';
                }
            }
            document.getElementById('fromdate').addEventListener('change', checkDateSelection);
            document.getElementById('todate').addEventListener('change', checkDateSelection);

            document.addEventListener("DOMContentLoaded", function() {
                // Get the date filter select element
                var dateFilterSelect = document.getElementById('dateFilter');

                // Get the date inputs
                var fromDateInput = document.getElementById('fromdate');
                var toDateInput = document.getElementById('todate');
                var filter = document.getElementById('filter');

                // Function to enable/disable date inputs based on selected option
                function toggleDateInputs() {
                    if (dateFilterSelect.value === 'all') {
                        filter.disabled = true;
                    } else {
                        filter.disabled = false;
                    }

                    if(filter.value === 'dateRange'){
                        fromDateInput.disabled = false;
                        toDateInput.disabled = false;
                    }else{
                        fromDateInput.disabled = true;
                        toDateInput.disabled = true;
                    }
                }

                // Initial call to toggleDateInputs function
                toggleDateInputs();

                dateFilterSelect.addEventListener('change', function() {
                    toggleDateInputs();
                    if (dateFilterSelect.value === 'all') {
                        document.getElementById('date-form').submit();
                    }
                });

                filter.addEventListener('change', function() {
                    toggleDateInputs();
                    if (filter.value != 'dateRange') {
                        document.getElementById('date-form').submit();
                    }
                });
            });
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.export-option[data-type="pdf"]').forEach(function(element) {
                    element.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior
                        
                        // Get the canvas element
                        var canvas = document.getElementById('bookingsChart');
                        
                        // Convert canvas to data URL
                        var dataURL = canvas.toDataURL();
                        
                        document.getElementById('chartImageInput').value = dataURL;
        
        // Submit the form
        document.getElementById('exportPDFForm').submit();
                    });
                });
            });

            // Function to extract URL parameters
            function getParameterByName(name, url) {
                if (!url) url = window.location.href;
                name = name.replace(/[\[\]]/g, '\\$&');
                var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                    results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, ' '));
            }
        </script>
        <script>
            $(document).ready(function() {
                var table = $('#bookings').DataTable({
                    "order": [[4, 'desc']],
                    responsive: true,
                });
            });
        </script>
    @endpush
@endsection
