@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <style>
        input {
            border: 1px solid #DEE2E6 !important;
        }

        .hidetd {
            display: none;
        }
    </style>
    <div class="container">
        <h1 class="d-inline">Revenue</h1>

        <div class="dropdown d-inline float-end">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="background-color: #D5E200; width: 100%; font-family: 'Rubik', sans-serif;">
                Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item export-option" href="{{ route('export_revenue_pdf', ['chartVisibility' => 'visible']) }}" data-type="pdf">PDF</a></li>

                <li><a class="dropdown-item export-option" href="{{ route('export_revenue_csv') }}"
                        data-type="excel">Excel</a></li>
            </ul>
        </div>

        <br><br>
        <form action="{{ route('reports.revenueFilter') }}" id="revenueForm" enctype="multipart/form-data" method="GET">
            <label for="filter">Filter</label>
            <select class="form-select d-inline float-start" id="filter" name="filter">
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
            <br><br><br>
            <div class="form-group row">
                <label for="date" class="col-md-2 col-form-label">Date Range</label>
                <div class="col-md-5">
                    <input type="date" name="fromdate" id="fromdate" class="form-control"
                        value="{{ Request::input('fromdate') }}"
                        {{ Request::input('filter') != 'dateRange' ? 'disabled' : '' }} placeholder="From">
                </div>
                {{-- <label for="date" class="col-md-2 col-form-label">To</label> --}}
                <div class="col-md-5">
                    <input type="date" name="todate" id="todate" class="form-control"
                        value="{{ Request::input('todate') }}"
                        {{ Request::input('filter') != 'dateRange' ? 'disabled' : '' }} placeholder="To">
                </div>
            </div>
        </form>
        {{-- column for sales, then refunds, then calculate total. filter last 12 months, last month, last week liddat --}}
        <div class="categorysection m-0 mt-2">
            <button id="toggleChartButton" class="d-inline float-end align-items-center p-1"
                style="font-family: 'Rubik', sans-serif; background-color:lightgray; border:none; border-radius:25px;"><span
                    class="material-symbols-outlined me-1" style="vertical-align: middle; line-height: 1;">
                    visibility_off
                </span>Hide Chart</button>
            <br>
            <canvas id="revenueChart" width="400" height="200"></canvas>
            <br>
            <table class="table" style="width: 100%;" id="revenue">
                <thead>
                    <th>Date</th>
                    <th>Bookings</th>
                    <th>Sales</th>
                    <th>Refunds</th>
                    <th>Total</th>
                </thead>
                <tbody>
                    @if ($getRevenueData)
                        @foreach ($getRevenueData as $data)
                            <tr>
                                <td>{{ $data->month }}</td>
                                <td>{{ $data->total_bookings }}</td>
                                <td>$ {{ $data->total_sales }}</td>
                                <td>$ {{ $data->total_refunds }}</td>
                                <td>$ {{ $data->total_sales - $data->total_refunds }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No revenue data found</td>
                            <td class="hidetd"></td>
                            <td class="hidetd"></td>
                            <td class="hidetd"></td>
                            <td class="hidetd"></td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align: right;">Total:</th>
                        <th>$ {{ $totalRevenue }}</th>
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
                $('#revenue').DataTable({
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
                        checkAndSubmitForm();
                    },
                });

                flatpickr("#todate", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr, instance) {
                        document.getElementById('todate').value = dateStr;
                        checkAndSubmitForm();
                    },
                });

                function prepareChartData(revenueData) {
                    var labels = [];
                    var revenue = [];

                    revenueData.forEach(function(data) {
                        labels.push(data.month);
                        revenue.push(data.total_sales - data.total_refunds);
                    });

                    return {
                        labels: labels,
                        datasets: [{
                            label: 'Revenue',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            data: revenue,
                            tension: 0.4
                        }]
                    };
                }

                function renderChart(revenueData) {
                    var ctx = document.getElementById('revenueChart').getContext('2d');
                    var chartData = prepareChartData(revenueData);

                    var revenueChart = new Chart(ctx, {
                        type: 'line',
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

                

                // Render the chart when the page loads
                renderChart(@json($getRevenueData));

                $('#toggleChartButton').click(function() {
                    var chart = $('#revenueChart');
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

            var filterSelect = document.getElementById('filter');
            var fromDateInput = document.getElementById('fromdate');
            var toDateInput = document.getElementById('todate');

            function toggleDateInputs() {
                if (filterSelect.value != 'dateRange') {
                    fromDateInput.disabled = true;
                    toDateInput.disabled = true;
                } else {
                    fromDateInput.disabled = false;
                    toDateInput.disabled = false;
                }
            }
            toggleDateInputs();

            function checkAndSubmitForm() {
                if ((filterSelect.value === 'dateRange' && fromDateInput.value && toDateInput.value) || (filterSelect
                        .value !== 'dateRange')) {
                    $('#revenueForm').submit();
                }
            }

            filterSelect.addEventListener('change', function() {
                toggleDateInputs();
                checkAndSubmitForm();
            });

            fromDateInput.addEventListener('change', function() {
                checkAndSubmitForm();
            });

            toDateInput.addEventListener('change', function() {
                checkAndSubmitForm();
            });

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.export-option[data-type="pdf"]').forEach(function(element) {
                    element.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior
                        
                        // Get the canvas element
                        var canvas = document.getElementById('revenueChart');
                        
                        // Convert canvas to data URL
                        var dataURL = canvas.toDataURL();
                        
                        // Get the chart visibility parameter from the link's href attribute
                        var chartVisibility = getParameterByName('chartVisibility', element.getAttribute('href'));
                        
                        // Redirect to the export_revenue_pdf route with chart image data URL and chart visibility parameter
                        window.location.href = "{{ route('export_revenue_pdf') }}" + "?chartImage=" + encodeURIComponent(dataURL) + "&chartVisibility=" + chartVisibility;
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
    @endpush
@endsection
