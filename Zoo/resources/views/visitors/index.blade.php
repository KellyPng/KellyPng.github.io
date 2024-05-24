@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
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
        }

        .btn-danger:hover {
            color: #3C332A;
        }

        .material-symbols-outlined {
            color: grey;
        }

        .emailto {
            color: #afb502;
        }

        th {
            font-family: 'Rubik', sans-serif;
        }

        .fakecolumn {
            display: none;
        }

        input {
            border: 1px solid #DEE2E6 !important;
        }
    </style>

    {{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.checkbox');
        const exportCSVButton = document.getElementById('exportCSV');
        const exportPDFButton = document.getElementById('exportPDF');
        const searchInput = document.getElementById('search');
        const countryFilter = document.getElementById('countryFilter');
        const visitDateFilter = document.getElementById('visitDateFilter');
        const rows = document.querySelectorAll('tbody tr');

        let selectedRows = [];

        checkboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    const visitorId = this.value;
                    // Use the visitorId as needed
                    console.log('Visitor ID:', visitorId);
                } else {
                    selectedRows = selectedRows.filter(item => item !== index);
                }
            });
        });

        exportCSVButton.addEventListener('click', function () {
            exportToCSV(selectedRows);
        });

        exportPDFButton.addEventListener('click', function () {
            exportToPDF(selectedRows);
        });

        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();

            rows.forEach((row, index) => {
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                row.style.display = name.includes(searchValue) ? '' : 'none';
            });
        });

        countryFilter.addEventListener('change', function () {
        const selectedCountry = this.value.toLowerCase();

        rows.forEach((row, index) => {
            const country = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            row.style.display = country.includes(selectedCountry) ? '' : 'none';
        });
    });

    visitDateFilter.addEventListener('change', function () {
        const selectedDate = visitDateFilter.value;
        rows.forEach((row, index) => {
            const visitDate = row.querySelector('td:nth-child(6)').textContent; // Adjusted index to match the column
            row.style.display = visitDate.includes(selectedDate) ? '' : 'none';
        });
    });

    function filterRowsByCountry() {
        const selectedCountry = countryFilter.value.toLowerCase();

        rows.forEach((row, index) => {
            const country = row.querySelector('td:nth-child(4)').textContent.toLowerCase(); // Adjusted index to match the column
            row.style.display = country.includes(selectedCountry) ? '' : 'none';
        });
    }

    countryFilter.addEventListener('change', function () {
        filterRowsByCountry();
    });

    function exportToCSV(selectedRows) {
        const selectedCountry = countryFilter.value.toLowerCase();
        // Implement CSV export logic using selectedRows and selectedCountry
        console.log('Exporting to CSV:', selectedRows, 'for country:', selectedCountry);
    }

    function exportToPDF(selectedRows) {
        const selectedCountry = countryFilter.value.toLowerCase();
        // Implement PDF export logic using selectedRows and selectedCountry
        console.log('Exporting to PDF:', selectedRows, 'for country:', selectedCountry);
    }
    });
</script> --}}
    <div class="container">
        <h1 class="d-inline">Visitors</h1>

        <div class="dropdown d-inline float-end">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="background-color: #D5E200; width: 100%; font-family: 'Rubik', sans-serif;">
                Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item export-option"
                        href="{{ route('export_visitors_pdf', ['chartVisibility' => 'visible']) }}" id="exportPDF"
                        data-type="pdf">PDF</a></li>

                <li><a class="dropdown-item export-option" href="{{ route('export_visitors_csv') }}" id="exportCSV"
                        data-type="excel">Excel</a></li>
            </ul>
        </div>

        <br><br>

        <form id="filter-visitors-form" enctype="multipart/form-data" method="GET"
            action="{{ route('visitors.filter') }}">
            <div class="search-filter-container">
                <div class="search-bar">
                    <label for="search">Search: </label>
                    <input type="text" class="form-control border" id="search" name="search"
                        placeholder="Search for visitors">
                </div>
            </div>
            <br>
            <div class="dropdown">
                <label for="countryFilter">Filter by Country: </label>
                <select class="form-select" id="countryFilter" name="countryFilter">
                    <option value="all" {{ Request::input('countryFilter') === 'all' ? 'selected' : '' }}>All
                    </option>
                    @foreach ($countries as $country)
                        <option value="{{ strtolower($country) }}"
                            {{ Request::input('countryFilter') === strtolower($country) ? 'selected' : '' }}>
                            {{ $country }}</option>
                    @endforeach
                </select>
                <br>
                {{-- <label for="visitDateFilter">Filter by Visit Date: </label>
                    <input type="datetime-local" class="form-control" id="visitDateFilter" name="visitDateFilter"
                        value="{{ Request::input('visitDateFilter') ? date('Y-m-d\TH:i', strtotime(Request::input('visitDateFilter'))) : '' }}"> --}}
                <div class="form-group row">
                    <label for="date" class="col-md-2 col-form-label">Filter by Visit Date</label>
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

        <div class="categorysection m-0 mt-3">
            <button id="toggleChartButton" class="d-inline float-end align-items-center p-1"
                style="font-family: 'Rubik', sans-serif; background-color:lightgray; border:none; border-radius:25px;"><span
                    class="material-symbols-outlined me-1" style="vertical-align: middle; line-height: 1;">
                    visibility_off
                </span>Hide Chart</button>
            <br>
            <canvas id="visitorsChart" width="400" height="200"></canvas>
            <br>
            <table class="table mt-3" id="visitorstable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Last Purchase</th>
                        <th>Visit Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($visitors) > 0)
                        @foreach ($visitors as $visitor)
                            <tr>
                                <td>{{ $visitor->id }}</td>
                                <td>{{ $visitor->firstName }} {{ $visitor->lastName }}</td>
                                <td><a href="mailto:{{ $visitor->email }}" class="emailto">{{ $visitor->email }}</a></td>
                                <td>{{ $visitor->contactNo }}</td>
                                <td>{{ $visitor->country }}</td>
                                <td>{{ $visitor->latestBookingDate() }}</td>
                                <td>{{ $visitor->visitDate() }}</td>
                                <td>{{ $visitor->visitorstatus() }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">No Visitors Yet.</td>
                            <td class="fakecolumn"></td>
                            <td class="fakecolumn"></td>
                            <td class="fakecolumn"></td>
                            <td class="fakecolumn"></td>
                            <td class="fakecolumn"></td>
                            <td class="fakecolumn"></td>
                            <td class="fakecolumn"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                $('#visitorstable').DataTable({
                    lengthMenu: [25],
                    pageLength: 25,
                    searching: false,
                    lengthChange: false,
                    responsive: true
                });

                $('#toggleChartButton').click(function() {
                    event.preventDefault();
                    var chart = $('#visitorsChart');
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

                var ctx = document.getElementById('visitorsChart').getContext('2d');
                var visitorChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['New', 'Existing'],
                        datasets: [{
                            label: 'Number of Visitors',
                            data: [{{ $newVisitorCount }}, {{ $existingVisitorCount }}],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                const today = new Date().toISOString()

                flatpickr("#fromdate", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                });
                flatpickr("#todate", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                });

                var search = document.getElementById('search');
                var countryFilter = document.getElementById('countryFilter');
                var fromdate = document.getElementById('fromdate');
                var todate = document.getElementById('todate');

                function checkFilterSelection() {
                    if (search.value || countryFilter.value || (fromdate.value && todate.value)) {
                        document.getElementById('filter-visitors-form').submit();
                    }
                }
                countryFilter.addEventListener('change', function() {
                    checkFilterSelection();
                });
                document.getElementById('fromdate').addEventListener('change', function() {
                    if (document.getElementById('fromdate').value && document.getElementById('todate').value) {
                        checkFilterSelection();
                    }
                });

                document.getElementById('todate').addEventListener('change', function() {
                    if (document.getElementById('fromdate').value && document.getElementById('todate').value) {
                        checkFilterSelection();
                    }
                });


            });


            document.addEventListener('DOMContentLoaded', function() {
                const search = document.getElementById('search');
                const rows = document.querySelectorAll('#visitorstable tbody tr');
                search.addEventListener('input', function() {
                    const searchValue = this.value.toLowerCase();

                    rows.forEach((row) => {
                        const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                        row.style.display = name.includes(searchValue) ? '' : 'none';
                    });
                    $.ajax({
                        url: '{{ route('visitors.search') }}',
                        type: 'GET',
                        data: {
                            search: searchValue
                        },
                        success: function(response) {
                            const tbody = document.querySelector('#visitorstable tbody');
                            tbody.innerHTML = '';

                            response.forEach(visitor => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                <td>${visitor.id}</td>
            <td>${visitor.firstName} ${visitor.lastName}</td>
            <td><a href="mailto:${visitor.email}" class="emailto">${visitor.email}</a></td>
            <td>${visitor.contactNo}</td>
            <td>${visitor.country}</td>
            <td>${visitor.latestBookingDate}</td>
            <td>${visitor.visitDate}</td>
            <td>${visitor.visitorstatus}</td>
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

                document.querySelectorAll('.export-option[data-type="pdf"]').forEach(function(element) {
                    element.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent the default link behavior

                        // Get the canvas element
                        var canvas = document.getElementById('visitorsChart');

                        // Convert canvas to data URL
                        var dataURL = canvas.toDataURL();

                        // Get the chart visibility parameter from the link's href attribute
                        var chartVisibility = getParameterByName('chartVisibility', element
                            .getAttribute('href'));

                        // Redirect to the export_revenue_pdf route with chart image data URL and chart visibility parameter
                        window.location.href = "{{ route('export_visitors_pdf') }}" + "?chartImage=" +
                            encodeURIComponent(dataURL) + "&chartVisibility=" + chartVisibility;
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
    @endpush
@endsection
