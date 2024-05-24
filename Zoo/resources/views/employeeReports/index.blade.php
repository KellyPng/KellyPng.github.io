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
        #reportsChart{
            width: 100%;
            height: auto;
            max-height: 400px;
        }
    </style>
    <div class="container">
        <h1 class="d-inline">Employee Reports</h1>
        <div class="dropdown d-inline float-end mb-4">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="background-color: #D5E200; width: 100%; font-family: 'Rubik', sans-serif;">
                Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item export-option" href="{{ route('export_employeeReports_pdf',['chartVisibility' => 'visible']) }}" id="exportPDF"
                        data-type="pdf">PDF</a></li>

                <li><a class="dropdown-item export-option" href="{{ route('export_employeeReports_csv') }}" id="exportCSV"
                        data-type="excel">Excel</a></li>
            </ul>
        </div>
        <br><br>
        <form id="date-form" enctype="multipart/form-data" method="GET" action="{{ route('employeeReports.filter') }}">
            @csrf
            <div class="search-filter-container">
                <br>
                <div class="search-bar">
                    <label for="search">Search: </label>
                    <input type="text" class="form-control border" id="search" placeholder="Search for reports"
                        name="search">
                </div>
            </div>
            <br>
            <label for="filter">Filter</label>
            <select class="form-select d-inline float-start" id="filter" name="filter"
                {{ Request::input('dateFilter') === 'all' ? 'disabled' : '' }}>
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
                <option value="thisweek" {{ Request::input('filter') === 'thisweek' ? 'selected' : '' }}>This week
                </option>
                <option value="dateRange" {{ Request::input('filter') === 'dateRange' ? 'selected' : '' }}>Select date
                    range
                </option>
            </select>
            <br><br><br>
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
        </form>
        <br>
        <div class="categorysection m-0 mt-2">
            <button id="toggleChartButton" class="d-inline float-end align-items-center p-1"
                style="font-family: 'Rubik', sans-serif; background-color:lightgray; border:none; border-radius:25px;"><span
                    class="material-symbols-outlined me-1" style="vertical-align: middle; line-height: 1;">
                    visibility_off
                </span>Hide Chart</button>
            <br>
            <canvas id="reportsChart"></canvas>
            <br>
            <table class="table" id="reports-table" style="width: 100%;cursor: pointer;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Email</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($reports) > 0)
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report['id'] }}</td>
                                <td>{{ $report['subject'] }}</td>
                                <td>{{ $report['description'] }}</td>
                                <td>{{ $report['email'] }}</td>
                                <td>{{ $report['created_at'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">No reports found</td>
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

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            function addBreakOnSmallScreen() {
                var dropdownButton = $('.dropdown.float-end.mb-4');
                if ($(window).width() < 768) {
                    dropdownButton.after('<br>');
                } else {
                    dropdownButton.next('br').remove();
                }
            }

            addBreakOnSmallScreen();
            $(window).resize(addBreakOnSmallScreen);

            var table = $('#reports-table').DataTable({
                "order": [
                    [4, 'desc']
                ],
                searching: false,
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

            function prepareData(reportData) {
                var dateCounts = {};
                reportData.forEach(function(report) {
                    var dateObj = new Date(report.created_at);
                    var year = dateObj.getFullYear();
                    var month = String(dateObj.getMonth() + 1).padStart(2, '0');
                    var day = String(dateObj.getDate()).padStart(2, '0');
                    var formattedDate = `${year}-${month}-${day}`;

                    if (!dateCounts[formattedDate]) {
                        dateCounts[formattedDate] = 0;
                    }
                    dateCounts[formattedDate]++;
                });

                var labels = Object.keys(dateCounts);
                var counts = Object.values(dateCounts);

                return {
                    labels: labels,
                    datasets: [{
                        label: 'Reports Count',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        tension: 0.4,
                        data: counts
                    }]
                };
            }

            function renderReportsChart(reportData) {
                var ctx = document.getElementById('reportsChart').getContext('2d');
                var chartData = prepareData(reportData);

                var reportsChart = new Chart(ctx, {
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
            };

            renderReportsChart(@json($reports));

            $('#toggleChartButton').click(function() {
                    var chart = $('#reportsChart');
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

                 // Enable row-click redirection to detailed report view
                 $('#reports-table tbody').on('click', 'tr', function(event) {
    var cellIndex = event.target.cellIndex; // Get the index of the clicked cell
    if (cellIndex !== 0) { // Exclude the first column
        var data = table.row(this).data();
        if (data) { // Check if data is not undefined
            window.location.href = "{{ route('employeeReports.show', '') }}/" + data[0]; // Assuming the first column contains the report ID
        }
    }
});
        });

        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('search');
            const rows = document.querySelectorAll('#reports-table tbody tr');
            search.addEventListener('input', function() {
                const searchValue = this.value.toLowerCase();

                rows.forEach((row) => {
                    const visitorName = row.querySelector('td:nth-child(2)').textContent
                        .toLowerCase();
                    const description = row.querySelector('td:nth-child(3)').textContent
                        .toLowerCase();
                    row.style.display = visitorName.includes(searchValue) || description.includes(
                        searchValue) ? '' : 'none';
                });
                $.ajax({
                    url: '{{ route('employeeReports.search') }}',
                    type: 'GET',
                    data: {
                        search: searchValue
                    },
                    success: function(response) {
                        const tbody = document.querySelector(
                            '#reports-table tbody');
                        tbody.innerHTML = '';

                        response.forEach(report => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${report['id']}</td>
            <td>${report['subject']}</td>
            <td>${report['description']}</td>
            <td>${report['email']}</td>
            <td>${report['created_at']}</td>
        `;
                            tbody.appendChild(row);
                        });
                    },
                    error: function(xhr) {
                        console.error('An error occurred:', xhr.statusText);
                        alert(
                            'An error occurred while fetching search results. Please try again later.'
                        );
                    }
                });
            });

            var fromDateInput = document.getElementById('fromdate');
            var toDateInput = document.getElementById('todate');
            var filter = document.getElementById('filter');

            function checkFilterSelection() {
                if ((filter.value != 'dateRange') || (fromDateInput.value && toDateInput.value)) {
                    document.getElementById('date-form').submit();
                }
            }

            filter.addEventListener('change', function() {
                toggleDateInputs();
                checkFilterSelection();
            });

            fromDateInput.addEventListener('change', function() {
                if (document.getElementById('fromdate').value && document.getElementById('todate').value) {
                    checkFilterSelection();
                }
            });

            toDateInput.addEventListener('change', function() {
                if (document.getElementById('fromdate').value && document.getElementById('todate').value) {
                    checkFilterSelection();
                }
            });

            function toggleDateInputs() {

                if (filter.value === 'dateRange') {
                    fromDateInput.disabled = false;
                    toDateInput.disabled = false;
                } else {
                    fromDateInput.disabled = true;
                    toDateInput.disabled = true;
                }
            }

            // Initial call to toggleDateInputs function
            toggleDateInputs();

            document.querySelectorAll('.export-option[data-type="pdf"]').forEach(function(element) {
                    element.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior
                        
                        // Get the canvas element
                        var canvas = document.getElementById('reportsChart');
                        
                        // Convert canvas to data URL
                        var dataURL = canvas.toDataURL();
                        
                        // Get the chart visibility parameter from the link's href attribute
                        var chartVisibility = getParameterByName('chartVisibility', element.getAttribute('href'));
                        
                        // Redirect to the export_revenue_pdf route with chart image data URL and chart visibility parameter
                        window.location.href = "{{ route('export_employeeReports_pdf') }}" + "?chartImage=" + encodeURIComponent(dataURL) + "&chartVisibility=" + chartVisibility;
                    });
                });
        });
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


    <style>
        #feedback-table_filter {
            display: none;
        }
    </style>


@endsection
