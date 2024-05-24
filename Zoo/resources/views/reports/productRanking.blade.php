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
        <h1 class="d-inline">Product Ranking</h1>
        <div class="dropdown d-inline float-end">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="background-color: #D5E200; width: 100%; font-family: 'Rubik', sans-serif;">
                Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item export-option" href="{{ route('export_productRanking_pdf',['chartVisibility' => 'visible']) }}" id="exportPDF" data-type="pdf">PDF</a></li>

                <li><a class="dropdown-item export-option" href="{{ route('export_productRanking_csv') }}" id="exportCSV" data-type="excel">Excel</a></li>
            </ul>
        </div><br><br>
        <form action="{{ route('reports.productRankingFilter') }}" id="productRankingForm" enctype="multipart/form-data"
            method="GET">
            @csrf
            <label for="filterby">Filter By:</label>
            <select class="form-select d-inline float-start" aria-label="Default select example" id="filterby"
                name="filterby">
                <option value="last12months" {{ Request::input('filterby') === 'last12months' ? 'selected' : '' }}>Last 12
                    months</option>
                <option value="last6months" {{ Request::input('filterby') === 'last6months' ? 'selected' : '' }}>Last 6 months
                </option>
                <option value="last3months" {{ Request::input('filterby') === 'last3months' ? 'selected' : '' }}>Last 3 months
                </option>
                <option value="lastmonth" {{ Request::input('filterby') === 'lastmonth' ? 'selected' : '' }}>Last month
                </option>
                <option value="thismonth" {{ Request::input('filterby') === 'thismonth' ? 'selected' : '' }}>This month
                </option>
                {{-- <option value="7days" selected>7 days</option>
                <option value="30days" {{ Request::input('filterby') === '30days' ? 'selected' : '' }}>30 days</option> --}}
                <option value="dateRange" {{ Request::input('filterby') === 'dateRange' ? 'selected' : '' }}>Select Date
                    Range</option>
            </select>
            <br><br><br>
            <div class="form-group row">
                <label for="date" class="col-md-2 col-form-label">Filter Date</label>
                <div class="col-md-5">
                    <input type="date" name="fromdate" id="fromdate" class="form-control"
                        value="{{ Request::input('fromdate') }}"
                        {{ Request::input('filterby') != 'dateRange' ? 'disabled' : '' }} placeholder="From">
                </div>
                {{-- <label for="date" class="col-md-2 col-form-label">To</label> --}}
                <div class="col-md-5">
                    <input type="date" name="todate" id="todate" class="form-control"
                        value="{{ Request::input('todate') }}"
                        {{ Request::input('filterby') != 'dateRange' ? 'disabled' : '' }} placeholder="To">
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
            <canvas id="productRankingChart" width="400" height="200"></canvas>
            <br>
            <table class="table" style="width: 100%;" id="productRanking">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Ticket</th>
                        <th>Quantity Sold</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($currentPeriodData)
                        @foreach ($currentPeriodData as $product)
                            <tr>
                                <td>{{ $product['rank'] }}</td>
                                <td>{{ $product['ticketTypeName'] }}</td>
                                <td>{{ $product['totalQuantitySold'] }}</td>
                                <td>
                                    {{-- <p>
                                        <span class="material-symbols-outlined text-danger"
                                            style="font-size: 15px">arrow_downward</span>
                                        <span class="text-danger">3.3%</span>
                                        Decrease
                                    </p> --}}
                                    @if ($product['percentageChange'] !== null)
                                        @if ($product['changeDirection'] === 'increase')
                                            <span class="text-success">{{ $product['percentageChange'] }}% Increase</span>
                                        @elseif ($product['changeDirection'] === 'decrease')
                                            <span class="text-danger">{{ $product['percentageChange'] }}% Decrease</span>
                                        @elseif ($product['changeDirection'] === 'stable')
                                            <span class="text-warning">{{ $product['percentageChange'] }}% Stable</span>
                                        @endif
                                    @else
                                        No Previous Data
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">No tickets found</td>
                            <td class="hidetd"></td>
                            <td class="hidetd"></td>
                            <td class="hidetd"></td>
                        </tr>
                </tbody>
            </table>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                $('#productRanking').DataTable({
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

                function renderProductRankingChart(productData) {
                // Prepare data for the chart
                var labels = productData.map(function(product) {
                    return product['ticketTypeName'];
                });

                var quantities = productData.map(function(product) {
                    return product['totalQuantitySold'];
                });

                // Create chart data
                var ctx = document.getElementById('productRankingChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Quantity Sold',
                            data: quantities,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
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

            // Render the product ranking chart when the page loads
            renderProductRankingChart(@json($currentPeriodData));

            $('#toggleChartButton').click(function() {
                    var chart = $('#productRankingChart');
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
            })

            var filterBySelect = document.getElementById('filterby');
            var fromDateInput = document.getElementById('fromdate');
            var toDateInput = document.getElementById('todate');

            function toggleDateInputs() {
                if (filterBySelect.value != 'dateRange') {
                    fromDateInput.disabled = true;
                    toDateInput.disabled = true;
                } else {
                    fromDateInput.disabled = false;
                    toDateInput.disabled = false;
                }
            }

            function checkAndSubmitForm() {
                if ((filterBySelect.value === 'dateRange' && fromDateInput.value && toDateInput.value) || (filterBySelect
                        .value !== 'dateRange')) {
                    $('#productRankingForm').submit();
                }
            }

            toggleDateInputs();


            filterBySelect.addEventListener('change', function() {
                toggleDateInputs();
                checkAndSubmitForm();
            });

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.export-option[data-type="pdf"]').forEach(function(element) {
                    element.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior
                        
                        // Get the canvas element
                        var canvas = document.getElementById('productRankingChart');
                        
                        // Convert canvas to data URL
                        var dataURL = canvas.toDataURL();
                        
                        // Get the chart visibility parameter from the link's href attribute
                        var chartVisibility = getParameterByName('chartVisibility', element.getAttribute('href'));
                        
                        // Redirect to the export_revenue_pdf route with chart image data URL and chart visibility parameter
                        window.location.href = "{{ route('export_productRanking_pdf') }}" + "?chartImage=" + encodeURIComponent(dataURL) + "&chartVisibility=" + chartVisibility;
                    });
                });
            })
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
