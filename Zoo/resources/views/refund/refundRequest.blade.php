@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <style>
        .viewbutton {
            background-color: #D5E200 !important;
            font-family: 'Rubik', sans-serif;
        }

        .viewbutton:hover {
            background-color: #c4c853 !important;
        }

        .btn-danger {
            font-family: 'Rubik', sans-serif;
            color: black;
        }

        .btn-danger:hover {
            color: #3C332A;
        }

        input {
            border: 1px solid #DEE2E6 !important;
        }
        #refundChart{
            width: 100%;
            height: auto;
            max-height: 400px;
        }
        .modal-backdrop {
        width: 100%;
    }
    </style>

    <div class="container">
        <h1 class="d-inline">Refund</h1>
        <div class="dropdown d-inline float-end">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="background-color: #D5E200; width: 100%; font-family: 'Rubik', sans-serif;">
                Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item export-option" href="{{route('export_refund_pdf',['chartVisibility'=>'visible'])}}" data-type="pdf">PDF</a></li>

                <li><a class="dropdown-item export-option" href="{{route('export_refund_csv')}}" data-type="excel">Excel</a></li>
            </ul>
        </div>
        <br><br>
        <form id="date-form" enctype="multipart/form-data" method="GET" action="{{ route('refund.filter') }}">
            @csrf
            <label for="requestType">Filter request type</label>
            <select class="form-select d-inline float-start" aria-label="Default select example" id="requestType"
                name="requestType">
                <option value="all" selected>All Requests</option>
                <option value="pending">Pending Requests</option>
                <option value="processed">Processed Requests</option>
                <option value="approved">Approved Requests</option>
                <option value="disapproved">Disapproved Requests</option>
            </select>
            <br><br><br>
            <div class="form-group row">
                <label for="date" class="col-md-2 col-form-label">Filter request date</label>
                <div class="col-md-5">
                    <input type="date" name="fromdate" id="fromdate" class="form-control"
                        value="{{ Request::input('fromdate') }}" placeholder="From">
                </div>
                {{-- <label for="date" class="col-md-2 col-form-label">To</label> --}}
                <div class="col-md-5">
                    <input type="date" name="todate" id="todate" class="form-control"
                        value="{{ Request::input('todate') }}" placeholder="To">
                </div>
            </div>
        </form>

        <br>
        <div class="categorysection m-0 mt-2">
            <button id="toggleChartButton" class="d-inline float-end align-items-center p-1"
                style="font-family: 'Rubik', sans-serif; background-color:lightgray; border:none; border-radius:25px;"><span
                    class="material-symbols-outlined me-1" style="vertical-align: middle; line-height: 1;">
                    visibility_off
                </span>Hide Chart</button>
            <br>
            <canvas id="refundChart"></canvas>
            <br>
            <div id="pendingRequestsSection">
                <h2 class="d-inline">Pending Requests</h2>
                <br>
                @php
                    $pendingCount = 0;
                @endphp
                <br>
                <table class="table" id="pendingRequests" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Booking ID</th>
                            <th>Reason</th>
                            <th>Request Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($refunds) > 0)
                            @foreach ($refunds as $ref)
                                @if ($ref->status == 'pending')
                                    @php
                                        $pendingCount++;
                                    @endphp
                                    <tr>
                                        <td>{{ $ref->id }}</td>
                                        <td>{{ $ref->firstName }}</td>
                                        <td>{{ $ref->lastName }}</td>
                                        <td>{{ $ref->bookingID }}</td>
                                        <td>{{ $ref->reasons }}</td>
                                        <td>{{ $ref->created_at }}</td>
                                        <td>$ {{ $ref->priceRefund }}</td>
                                        <td>{{ ucfirst($ref->status) }}</td>
                                        <td>
                                            <form action="/refundprocess" class="d-inline" id="deleteForm">
                                                <input type="hidden" value="{{ $ref->bookingID }}" name="bookingID"
                                                    id="bookingID" />
                                                <button type="submit" name="choice" value="Approve"
                                                    class="btn viewbutton d-inline p-2"
                                                    style="font-family: 'Rubik', sans-serif;">Approve</button>
                                                {{-- <button type="submit" name="choice" value="Disapprove"
                                                    class="btn btn-danger"
                                                    style="font-family: 'Rubik', sans-serif;">Disapprove</button> --}}
                                                    <button type="button" class="btn btn-danger delete-button" data-bookingid="{{ $ref->bookingID }}" data-choice="Disapprove">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>

                {{-- @if ($pendingCount == 0)
                    <p>No Pending Requests found</p>
                @endif --}}
            </div>
        </div>

        <div class="categorysection m-0 mt-2" id="processedRequestsSection">
            <h2 class="d-inline">Processed Requests</h2>
            @php
                $processedCount = 0;
            @endphp
            <br>
            <br>
            <table class="table" id="approvedRequests" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Booking ID</th>
                        <th>Reason</th>
                        <th>Request Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action Date</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($refunds) > 0)
                        @foreach ($refunds as $refund)
                            @if ($refund->status == 'Approved' || $refund->status == 'Disapproved')
                                @php
                                    $processedCount++;
                                @endphp
                                <tr>
                                    <td>{{ $refund->id }}</td>
                                    <td>{{ $refund->firstName }}</td>
                                    <td>{{ $refund->lastName }}</td>
                                    <td>{{ $refund->bookingID }}</td>
                                    <td>{{ $refund->reasons }}</td>
                                    <td>{{ $refund->created_at }}</td>
                                    <td>$ {{ $refund->priceRefund }}</td>
                                    <td>{{ $refund->status }}</td>
                                    <td>{{ $refund->approveDate }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="width: 100%;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to disapprove this request?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Disapprove</button>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                $('.delete-button').click(function() {
            var bookingID = $(this).data('bookingid');
            var choice = $(this).data('choice');

            // Set the bookingID and choice in the modal's delete button
            $('#confirmDeleteButton').data('bookingid', bookingID);
            $('#confirmDeleteButton').data('choice', choice);

            // Show the modal
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteButton').click(function() {
            var bookingID = $(this).data('bookingid');
            var choice = $(this).data('choice');

            // Set the bookingID and choice in the form
            $('#bookingID').val(bookingID);
            $('#deleteForm').append('<input type="hidden" name="choice" value="' + choice + '">');

            // Submit the form
            $('#deleteForm').submit();
        });

                $('#pendingRequests').DataTable({
                    "order": [[5, 'desc']],
                    lengthMenu: [25],
                    pageLength: 25,
                    searching: true,
                    lengthChange: true,
                    responsive: true
                });

                $('#approvedRequests').DataTable({
                    "order": [[8, 'desc']],
                    lengthMenu: [25],
                    pageLength: 25,
                    searching: true,
                    lengthChange: true,
                    responsive: true
                });

                flatpickr("#fromdate", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr, instance) {
                        document.getElementById('fromdate').value = dateStr;
                    },
                });

                flatpickr("#todate", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr, instance) {
                        document.getElementById('todate').value = dateStr;
                    },
                });


                function prepareChartData(refundData) {
                    var refundCount = {};
                    var requestType = document.getElementById('requestType').value;

                    refundData.forEach(function(data) {
                        var date;
                        var status = data.status;
                        if (requestType === 'all' || requestType === 'processed') {
                            date = moment(data.requestDate).format('MM-YYYY');
                        } else if (requestType === 'pending' && status === 'pending') {
                            date = moment(data.requestDate).format('MM-YYYY');
                        } else if (requestType === 'approved' && status === 'Approved') {
                            date = moment(data.requestDate).format('MM-YYYY');
                        } else if (requestType === 'disapproved' && status === 'Disapproved') {
                            date = moment(data.requestDate).format('MM-YYYY');
                        }
                        if (date) {
                            if (!refundCount[date]) {
                                refundCount[date] = 1;
                            } else {
                                refundCount[date]++;
                            }
                        }
                    });
                    var labels = Object.keys(refundCount);
                    var refundCounts = Object.values(refundCount);

                    return {
                        labels: labels,
                        datasets: [{
                            label: 'Total Refund Requests',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            data: refundCounts
                        }]
                    };
                }

                function renderChart(refundData, requestType) {
                    var ctx = document.getElementById('refundChart').getContext('2d');
                    var existingChart = Chart.getChart(ctx);
                    if (existingChart) {
                        existingChart.destroy();
                    }
                    var filteredData = refundData.filter(function(data) {
                        if (requestType === 'all') {
                            return true;
                        } else if (requestType === 'pending') {
                            return data.status === 'pending';
                        } else if (requestType === 'processed') {
                            return data.status === 'Approved' || data.status ===
                                'Disapproved'; 
                        } else if (requestType === 'approved') {
                            return data.status === 'Approved';
                        } else if (requestType === 'disapproved') {
                            return data.status === 'Disapproved';
                        }
                    });
                    var chartData = prepareChartData(filteredData);

                    var refundChart = new Chart(ctx, {
                        type: 'bar',
                        data: chartData,
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });
                }
                var requestType = document.getElementById('requestType').value;
                renderChart(@json($refunds), requestType);

                $('#requestType').change(function() {
                    var selectedType = $(this).val();
                    console.log(selectedType);
                    // Re-render the chart based on the selected requestType
                    renderChart(@json($refunds), selectedType);
                });

                $('#toggleChartButton').click(function() {
                    var chart = $('#refundChart');
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

                function checkDateSelection() {
                    var fromDate = document.getElementById('fromdate').value;
                    var toDate = document.getElementById('todate').value;
                    // var requestType = document.getElementById('requestType').value;

                    if (fromDate && toDate) {
                        document.getElementById('date-form').submit();
                    }
                    // if (requestType) {
                    //     document.getElementById('date-form').submit();
                    // }
                }
                document.getElementById('fromdate').addEventListener('change', checkDateSelection);
                document.getElementById('todate').addEventListener('change', checkDateSelection);
                // document.getElementById('requestType').addEventListener('change', checkDateSelection);

                // $('.export-option').click(function(event) {
                //     event.preventDefault();
                //     var requestType = $('#requestType').val();
                //     var exportType = $(this).data('type');
                //     var exportUrl =
                //         "{{ route('refund.export', ['requestType' => ':requestType', 'exportType' => ':exportType']) }}";
                //     exportUrl = exportUrl.replace(':requestType', requestType).replace(':exportType',
                //         exportType);
                //     window.location.href = exportUrl;
                // });

                $('#requestType').change(function() {
                    var selectedType = $(this).val();
                    if (selectedType === 'pending') {
                        $('#processedRequestsSection').hide();
                    } else {
                        $('#processedRequestsSection').show();
                    }
                    if (selectedType === 'processed') {
                        $('#pendingRequestsSection').hide();
                        $('#approvedRequests').DataTable().search('').draw(); // Reset search
                    } else {
                        $('#pendingRequestsSection').show();
                    }

                    if (selectedType === 'approved' || selectedType === 'disapproved') {
                        $('#pendingRequestsSection').hide();
                        $('#processedRequestsSection').show();

                        // Filter processed requests based on status
                        var status = (selectedType === 'approved') ? '^Approved$' : '^Disapproved$';
                        $('#approvedRequests').DataTable().column(7).search(status, true, false).draw();
                    }
                });
            });
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.export-option[data-type="pdf"]').forEach(function(element) {
                    element.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior
                        
                        // Get the canvas element
                        var canvas = document.getElementById('refundChart');
                        
                        // Convert canvas to data URL
                        var dataURL = canvas.toDataURL();
                        
                        // Get the chart visibility parameter from the link's href attribute
                        var chartVisibility = getParameterByName('chartVisibility', element.getAttribute('href'));
                        var requestType = document.getElementById('requestType').value;
                        
                        // Redirect to the export_revenue_pdf route with chart image data URL and chart visibility parameter
                        window.location.href = "{{ route('export_refund_pdf') }}" + "?chartImage=" + encodeURIComponent(dataURL) + "&requestType=" + requestType + "&chartVisibility=" + chartVisibility;
                    });
                });
                document.querySelectorAll('.export-option[data-type="excel"]').forEach(function(element) {
                    element.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior
                        
                        // Get the canvas element
                        var canvas = document.getElementById('refundChart');
                        
                        // Convert canvas to data URL
                        var dataURL = canvas.toDataURL();
                        
                        // Get the chart visibility parameter from the link's href attribute
                        var chartVisibility = getParameterByName('chartVisibility', element.getAttribute('href'));
                        var requestType = document.getElementById('requestType').value;
                        
                        // Redirect to the export_revenue_pdf route with chart image data URL and chart visibility parameter
                        window.location.href = "{{ route('export_refund_csv') }}" + "?chartImage=" + encodeURIComponent(dataURL) + "&requestType=" + requestType + "&chartVisibility=" + chartVisibility;
                    });
                });});
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
    @endpush
@endsection
