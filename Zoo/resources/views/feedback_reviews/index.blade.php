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

        #reviewsChart {
            width: 100%;
            height: auto;
            max-height: 400px;
        }
    </style>
    <div class="container">
        <h1 class="d-inline">Feedback and Reviews</h1>
        <div class="dropdown d-inline float-end mb-4">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="background-color: #D5E200; width: 100%; font-family: 'Rubik', sans-serif;">
                Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item export-option"
                        href="{{ route('export_feedbackReview_pdf', ['chartVisibility' => 'visible']) }}" id="exportPDF"
                        data-type="pdf">PDF</a></li>

                <li><a class="dropdown-item export-option" href="{{ route('export_feedbackReview_csv') }}" id="exportCSV"
                        data-type="excel">Excel</a></li>
            </ul>
        </div>
        <br>
        <form id="filter-form" enctype="multipart/form-data" method="GET" action="{{ route('feedback.filter') }}">
            @csrf
            <div class="search-filter-container">
                <br>
                <div class="search-bar">
                    <label for="search">Search: </label>
                    <input type="text" class="form-control border" id="search" placeholder="Search for feedback"
                        name="search">
                </div>
            </div>
            <br>
            <div class="dropdown">
                <label for="starsFilter">Filter by Stars: </label>
                <select class="form-select" id="starsFilter" name="starsFilter">
                    <option value="all" {{ Request::input('starsFilter') === 'all' ? 'selected' : '' }}>All</option>
                    <option value="1" {{ Request::input('starsFilter') === '1' ? 'selected' : '' }}>1 Star</option>
                    <option value="2" {{ Request::input('starsFilter') === '2' ? 'selected' : '' }}>2 Stars</option>
                    <option value="3" {{ Request::input('starsFilter') === '3' ? 'selected' : '' }}>3 Stars</option>
                    <option value="4" {{ Request::input('starsFilter') === '4' ? 'selected' : '' }}>4 Stars</option>
                    <option value="5" {{ Request::input('starsFilter') === '5' ? 'selected' : '' }}>5 Stars</option>
                </select>
            </div><br>
            <div class="form-group row">
                <label for="date" class="col-md-2 col-form-label">Filter Date</label>
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
            <canvas id="reviewsChart"></canvas>
            <br>

            <table class="table" id="feedback-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Visitor Name</th>
                        <th>Feedback</th>
                        <th>Stars</th>
                        <th>Submitted At</th>
                        <th>Visibility</th>

                    </tr>
                </thead>
                <tbody>
                    @if (count($reviews) > 0)
                        @foreach ($reviews as $review)
                            <tr>
                                <td>{{ $review->id }}</td>
                                <td>{{ $review->visitor_name }}</td>
                                <td>{{ $review->description }}</td>
                                <td>{{ $review->stars }}</td>
                                <td>{{ $review->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <form action="{{ url('feedback-reviews/visibility/' . $review->id) }}" method="post">
                                        @csrf
                                        <input type="checkbox" name="is_visible" onchange="this.form.submit()"
                                            {{ $review->is_visible ? 'checked' : '' }}>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">No Feedback and Reviews available.</td>
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

            // Call the function on page load and window resize
            addBreakOnSmallScreen();
            $(window).resize(addBreakOnSmallScreen);

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
            var table = $('#feedback-table').DataTable({
                searching: false,
                "order": [[4, 'desc']],
                responsive: true
            });

            $('#toggleChartButton').click(function() {
                event.preventDefault();
                var chart = $('#reviewsChart');
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

            function prepareChartData(reviewData) {
                var starCounts = [0, 0, 0, 0, 0];

                reviewData.forEach(function(review) {
                    starCounts[review.stars - 1]++;
                });

                return {
                    labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
                    datasets: [{
                        label: 'Number of Feedback and Reviews',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        data: starCounts
                    }]
                };
            }

            function renderChart(reviewData) {
                var ctx = document.getElementById('reviewsChart').getContext('2d');
                var chartData = prepareChartData(reviewData);

                var reviewChart = new Chart(ctx, {
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

            renderChart(@json($reviews));

            function checkFilterSelection() {
                var stars = document.getElementById('starsFilter');
                var fromdate = document.getElementById('fromdate');
                var todate = document.getElementById('todate');
                if (stars.value || (fromdate.value && todate.value)) {
                    document.getElementById('filter-form').submit();
                }
            }

            document.getElementById('starsFilter').addEventListener('change', checkFilterSelection);
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
            const rows = document.querySelectorAll('#feedback-table tbody tr');
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
                    url: '{{ route('feedback.search') }}',
                    type: 'GET',
                    data: {
                        search: searchValue
                    },
                    success: function(response) {
                        const tbody = document.querySelector(
                            '#feedback-table tbody');
                        tbody.innerHTML = '';

                        response.forEach(review => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${review.id}</td>
            <td>${review.visitor_name}</td>
            <td>${review.description}</td>
            <td>${review.stars}</td>
            <td>${review.formatted_created_at}</td>
            <td><input type="checkbox" class="toggle-visibility" data-id="${ review.id }"
        ${ review.is_visible ? 'checked' : '' }></td>
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

            $('.toggle-visibility').on('change', function() {
                let id = $(this).data('id');
                let isVisible = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/feedback-reviews/visibility/' + id,
                    method: 'POST',
                    data: {
                        is_visible: isVisible,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Visibility updated');
                    },
                    error: function(xhr) {
                        console.error('Error updating visibility');
                    }
                });
            });

            document.querySelectorAll('.export-option[data-type="pdf"]').forEach(function(element) {
                element.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default link behavior

                    // Get the canvas element
                    var canvas = document.getElementById('reviewsChart');

                    // Convert canvas to data URL
                    var dataURL = canvas.toDataURL();

                    // Get the chart visibility parameter from the link's href attribute
                    var chartVisibility = getParameterByName('chartVisibility', element
                        .getAttribute('href'));

                    // Redirect to the export_revenue_pdf route with chart image data URL and chart visibility parameter
                    window.location.href = "{{ route('export_feedbackReview_pdf') }}" +
                        "?chartImage=" +
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
@endsection
