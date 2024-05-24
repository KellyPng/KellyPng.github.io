@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <style>
        .box {
            box-shadow: 0px 1px 22px -12px #607D8B;
            background-color: #fff;
            padding: 25px 35px 25px 30px;
        }

        .top-box {
            display: flex;
            align-items: center;
        }

        .top-box .text {
            text-align: center;
            width: 100%;
        }

        .top-box .text p {
            font-family: 'Rubik', sans-serif;
        }

        /* .top-box .material-symbols-outlined {
                                        font-size: 3rem;
                                        background-color: rgba(255, 255, 255, 0.5);
                                        width: 80px;
                                        height: 80px;
                                        border-radius: 20px;
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                    } */

        h4 {
            font-size: 20px;
            font-family: 'Rubik', sans-serif;
        }

        #dashboardfilter {
            background-color: #F8F9FA;
            border: 1px solid #DEE2E6 !important;
        }

        .downloaddropdown button {
            background-color: lightgray;
            font-family: 'Rubik', sans-serif;
            position: relative;
            display: inline-block;
            align-self: flex-end;
        }

        .downloaddropdown button:hover {
            background-color: #F8F9FA;
            border: 1px solid #DEE2E6;
            font-family: 'Rubik', sans-serif;
        }

        input {
            border: 1px solid #DEE2E6 !important;
        }

        .hidetd {
            display: none;
        }
    </style>
    <h1>Dashboard</h1>
    {{-- <h4 class="welcome">Welcome, {{ auth()->user()->firstname }}!</h4> --}}

    <div id="wrapper">
        <div class="content-area">
            <div class="align-items-center ">
                <div>
                    <h4 class="mb-3 mb-md-0">Welcome, {{ auth()->user()->firstname }}!</h4>
                </div>
                <div class="d-flex controlcontainer align-items-center justify-content-end">
                    <form action="{{ route('dashboard.filter') }}" method="GET" id="dashboardFilterForm"
                        enctype="multipart/form-data" autocomplete="off">

                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">
                                            calendar_today
                                        </span>
                                    </span>
                                    <input type="datetime-local" class="form-control" id="startDate" name="startDate"
                                        placeholder="Filter date from"
                                        value="{{ Request::input('startDate') ? date('Y-m-d\TH:i', strtotime(Request::input('startDate'))) : '' }}">
                                    <input type="datetime-local" class="form-control" id="endDate" name="endDate"
                                        placeholder="To"
                                        value="{{ Request::input('endDate') ? date('Y-m-d\TH:i', strtotime(Request::input('endDate'))) : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <select name="filter" id="filter" class="form-select"
                                    aria-label="Default select example">
                                    <option value="thismonth"
                                        {{ Request::input('filter') === 'thismonth' ? 'selected' : '' }} selected>This month</option>
                                        <option value="thisweek" {{ Request::input('filter') === 'thisweek' ? 'selected' : '' }}>This week</option>
                                    <option value="lastmonth"
                                        {{ Request::input('filter') === 'lastmonth' ? 'selected' : '' }}>Last month</option>
                                    <option value="last3months"
                                        {{ Request::input('filter') === 'last3months' ? 'selected' : '' }}>Last 3 months
                                    </option>
                                    <option value="last6months"
                                        {{ Request::input('filter') === 'last6months' ? 'selected' : '' }}>Last 6 months
                                    </option>
                                    <option value="last12months"
                                        {{ Request::input('filter') === 'last12months' ? 'selected' : '' }}>Last 12 months
                                    </option>
                                </select>
                            </div>
                        </div>


                        {{-- <div class="datefilter wd-200 me-2 mb-2 mb-md-0">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <span class="material-symbols-outlined">
                                        calendar_today
                                    </span>
                                </span>
                                <input type="datetime-local" class="form-control" id="selectedDateRange"
                                    name="selectedDateRange">
                            </div>
                        </div>
                        <div class="filter wd-200 me-2 mb-2 mb-md-0">
                            <label for="Filter"></label>
                            <select name="filter" id="filter" class="form-select" aria-label="Default select example">
                                <option value="last12month">Last 12 months</option>
                                <option value="last6months">Last 6 months</option>
                                <option value="last3months">Last 3 months</option>
                                <option value="lastmonth">Last month</option>
                                <option value="thismonth">This month</option>
                                <option value="thisweek">This week</option>
                            </select>
                        </div> --}}
                    </form>
                    {{-- <div class="dropdown downloaddropdown mb-2 mb-md-0">
                        <button class="btn btn-icon-text dropdown-toggle mb-2 mb-md-0 mt-1" type="button"
                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="width:100%;">
                            Download Report
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Bookings Report</a></li>
                            <li><a class="dropdown-item" href="#">Visitors Report</a></li>
                            <li><a class="dropdown-item" href="#">Refund Report</a></li>
                            <li><a class="dropdown-item" href="#">Sales Report</a></li>
                        </ul>
                    </div> --}}
                </div>
            </div>

            <div class="container-fluid">
                <div class="main">
                    <div class="row mt-4">
                        <div class="col-md-4 mb-4">
                            <div class="box bg-primary-subtle top-box" style="height: 100%;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h4 class="d-flex align-items-baseline">Total Bookings</h4>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="material-symbols-outlined">more_horiz</span>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('bookings.index') }}"><i data-feather="eye"
                                                        class="icon-sm me-2"></i> <span class="">View</span></a>
                                                {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a> --}}
                                                {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                        data-feather="download" class="icon-sm me-2"></i> <span
                                                        class="">Download</span></a> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <div class="d-flex align-items-baseline">
                                                <h3 class="mb-2 me-2">{{ $currentPeriodData['bookingsCountforMetrics'] }}</h3>
                                            </div>
                                            {{-- <p class="text-success">
                                                <span class="text-success">{{ $metrics['bookings']['changePercentage'] }}%, Change Direction - {{ $metrics['bookings']['changeDirection'] }}</span>
                                                <span class="material-symbols-outlined text-success"
                                                    style="font-size: 15px">arrow_upward</span>
                                            </p> --}}
                                            <p>
                                                @if ($metrics['bookings']['changeDirection'] != null)
                                                @if ($metrics['bookings']['changeDirection'] === 'increase')
                                                <span class="material-symbols-outlined text-success" style="font-size: 15px">trending_up</span>
                                                    <span class="text-success">{{ $metrics['bookings']['changePercentage'] }}%</span>
                                                @elseif ($metrics['bookings']['changeDirection'] === 'decrease')
                                                <span class="material-symbols-outlined text-danger" style="font-size: 15px">trending_down</span>
                                                    <span class="text-danger">{{ abs($metrics['bookings']['changePercentage']) }}%</span>
                                                @elseif ($metrics['bookings']['changeDirection'] === 'stable')
                                                <span class="material-symbols-outlined text-warning" style="font-size: 15px">drag_indicator</span>
                                                    <span class="text-warning">{{ $metrics['bookings']['changePercentage'] }}%</span>
                                                @endif
                                                @else
                                                No previous data
                                                @endif
                                                
                                            </p>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <canvas id="bookingsChart" class="mt-md-3 mt-xl-0"></canvas>
                                        </div>
                                    </div>
                                </div>
                                {{-- <a href="{{ route('bookings.index') }}">View</a> --}}
                            </div>

                        </div>
                        <div class="col-md-4 mb-4">
                            {{-- <div class="box box2 bg-success-subtle top-box" style="height: 100%;">
                            <span class="material-symbols-outlined" style="color: green">
                                group
                            </span>
                            <span class="text">
                                <h3>{{ $totalVisitors }}</h3>
                                <h4>New Visitors</h4>
                            </span>
                            <a href="{{route('visitors.index')}}">View</a>
                        </div> --}}
                            <div class="box box2 bg-success-subtle top-box" style="height: 100%;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h4 class="d-flex align-items-baseline">Total Visitors</h4>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="material-symbols-outlined">more_horiz</span>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('visitors.index') }}"><i data-feather="eye"
                                                        class="icon-sm me-2"></i> <span class="">View</span></a>
                                                {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a> --}}
                                                {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                        data-feather="download" class="icon-sm me-2"></i> <span
                                                        class="">Download</span></a> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <div class="d-flex align-items-baseline">
                                                <h3 class="mb-2 me-2">{{ $currentPeriodData['visitorsCountforMetrics'] }}</h3>
                                            </div>
                                            <p>
                                                @if ($metrics['visitors']['changeDirection'] != null)
                                                @if ($metrics['visitors']['changeDirection'] === 'increase')
                                                <span class="material-symbols-outlined text-success" style="font-size: 15px">trending_up</span>
                                                    <span class="text-success">{{ $metrics['visitors']['changePercentage'] }}%</span>
                                                @elseif ($metrics['visitors']['changeDirection'] === 'decrease')
                                                <span class="material-symbols-outlined text-danger" style="font-size: 15px">trending_down</span>
                                                    <span class="text-danger">{{ abs($metrics['visitors']['changePercentage']) }}%</span>
                                                @elseif ($metrics['visitors']['changeDirection'] === 'stable')
                                                <span class="material-symbols-outlined text-warning" style="font-size: 15px">drag_indicator</span>
                                                    <span class="text-warning">{{ $metrics['visitors']['changePercentage'] }}%</span>
                                                @endif
                                                @else
                                                No previous data
                                                @endif
                                                
                                            </p>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <canvas id="visitorsChart" class="mt-md-3 mt-xl-0"></canvas>
                                        </div>
                                    </div>
                                </div>
                                {{-- <a href="{{ route('bookings.index') }}">View</a> --}}
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            {{-- <div class="box box3 bg-danger-subtle top-box" style="height: 100%;">
                            <span class="material-symbols-outlined" style="color: red">
                                confirmation_number
                            </span>
                            <span class="text">
                                <h3>{{ $totalSoldOut }}</h3>
                                <h4>Refunded</h4>
                            </span>
                            <a href="">View</a>
                        </div> --}}
                            <div class="box box2 bg-danger-subtle top-box" style="height: 100%;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h4 class="d-flex align-items-baseline">Refund Requests</h4>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="material-symbols-outlined">more_horiz</span>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('refund.refundRequest') }}"><i data-feather="eye"
                                                        class="icon-sm me-2"></i> <span class="">View</span></a>
                                                {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                                      <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a> --}}
                                                {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                        data-feather="download" class="icon-sm me-2"></i> <span
                                                        class="">Download</span></a> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <div class="d-flex align-items-baseline">
                                                <h3 class="mb-2 me-2">{{ $currentPeriodData['refundsCount'] }}</h3>
                                            </div>
                                            <p>
                                                @if ($metrics['refunds']['changeDirection'] != null)
                                                @if ($metrics['refunds']['changeDirection'] === 'increase')
                                                <span class="material-symbols-outlined text-success" style="font-size: 15px">trending_up</span>
                                                    <span class="text-success">{{ $metrics['refunds']['changePercentage'] }}%</span>
                                                @elseif ($metrics['refunds']['changeDirection'] === 'decrease')
                                                <span class="material-symbols-outlined text-danger" style="font-size: 15px">trending_down</span>
                                                    <span class="text-danger">{{ abs($metrics['refunds']['changePercentage']) }}%</span>
                                                @elseif ($metrics['refunds']['changeDirection'] === 'stable')
                                                <span class="material-symbols-outlined text-warning" style="font-size: 15px">drag_indicator</span>
                                                    <span class="text-warning">{{ $metrics['refunds']['changePercentage'] }}%</span>
                                                @endif
                                                @else
                                                No previous data
                                                @endif
                                                
                                            </p>
                                        </div>
                                        <div class="col-6 col-md-12 col-xl-7">
                                            <canvas id="refundsChart" class="mt-md-3 mt-xl-0"></canvas>
                                        </div>
                                    </div>
                                </div>
                                {{-- <a href="{{ route('bookings.index') }}">View</a> --}}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6 mb-4">
                            <div class="box">
                                <div class="col d-flex justify-content-between align-items-center">
                                    <h3 class="d-line">Product Ranking</h3>
                                    <div class="dropdown mb-2 d-line">
                                        <button class="btn p-0" type="button" id="dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="material-symbols-outlined">more_horiz</span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('reports.productRanking') }}"><i data-feather="eye"
                                                    class="icon-sm me-2"></i> <span class="">View</span></a>
                                            {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="download" class="icon-sm me-2"></i> <span
                                                    class="">Download</span></a> --}}
                                        </div>
                                    </div>
                                </div>
                                <canvas id="productRankingChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="box">
                                <div class="col d-flex justify-content-between align-items-center">
                                    <h3 class="d-line">Revenue</h3>
                                    <div class="dropdown mb-2 d-line">
                                        <button class="btn p-0" type="button" id="dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="material-symbols-outlined">more_horiz</span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('reports.revenue') }}"><i data-feather="eye"
                                                    class="icon-sm me-2"></i> <span class="">View</span></a>
                                            {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a> --}}
                                            {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="download" class="icon-sm me-2"></i> <span
                                                    class="">Download</span></a> --}}
                                        </div>
                                    </div>
                                </div>
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="box p-3" style="height: 100%;">
                                <div class="col d-flex justify-content-between align-items-center">
                                    <h3 class="d-line">Employee Reports</h3>
                                    <div class="dropdown mb-2 d-line">
                                        <button class="btn p-0" type="button" id="dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="material-symbols-outlined">more_horiz</span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item d-flex align-items-center" href="{{route('employeeReports.index')}}"><i
                                                    data-feather="eye" class="icon-sm me-2"></i> <span
                                                    class="">View</span></a>
                                            {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a> --}}
                                            {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="download" class="icon-sm me-2"></i> <span
                                                    class="">Download</span></a> --}}
                                        </div>
                                    </div>
                                </div>
                                <table id="employeeReports" class="table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Subject</th>
                                            <th>Submitted At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($currentPeriodData['employeeReports'])
                                        @foreach ($currentPeriodData['employeeReports'] as $report)
                                        <tr>
                                            <td>{{$report->id}}</td>
                                            <td>{{$report->SUBJECT}}</td>
                                            <td>{{$report->created_at->toDateTimeString()}}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="3">No reports found.</td>
                                            <td class="hidetd"></td>
                                            <td class="hidetd"></td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="box" style="height: 100%;">
                                <div class="col d-flex justify-content-between align-items-center">
                                    <h3 class="d-line">Feedback and Reviews</h3>
                                    <div class="dropdown mb-2 d-line">
                                        <button class="btn p-0" type="button" id="dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="material-symbols-outlined">more_horiz</span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('feedback_reviews.index') }}"><i data-feather="eye"
                                                    class="icon-sm me-2"></i> <span class="">View</span></a>
                                            {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a> --}}
                                            {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="download" class="icon-sm me-2"></i> <span
                                                    class="">Download</span></a> --}}
                                        </div>
                                    </div>
                                </div>
                                <table class="table" id="reviewstable" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th class="col">#</th>
                                            <th class="col">Name</th>
                                            <th class="col">Feedback</th>
                                            <th class="col">Stars</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($currentPeriodData['reviews'])
                                            @foreach ($currentPeriodData['reviews'] as $review)
                                                <tr>
                                                    <td>{{ $review->id }}</td>
                                                    <td>{{ $review->visitor_name }}</td>
                                                    <td>{{ $review->description }}</td>
                                                    <td>{{ $review->stars }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" style="text-align: center;">No reviews found.</td>
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
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const today = new Date().toISOString()

            flatpickr("#startDate", {
                enableTime: false,
                dateFormat: "Y-m-d",
            });
            flatpickr("#endDate", {
                enableTime: false,
                dateFormat: "Y-m-d",
            });
            $(document).ready(function() {

                $('#reviewstable').DataTable({
                    responsive: true
                });

                $('#employeeReports').DataTable({
                    responsive: true,
                    "order": [[2, 'desc']]
                });

                document.getElementById('startDate').addEventListener('change', function() {
                    if (document.getElementById('endDate').value && document.getElementById('startDate')
                        .value) {
                            document.getElementById('filter').value = '';
                        document.getElementById('dashboardFilterForm').submit();
                    }
                });

                document.getElementById('endDate').addEventListener('change', function() {
                    if (document.getElementById('startDate').value && document.getElementById('endDate')
                        .value) {
                            document.getElementById('filter').value = '';
                        document.getElementById('dashboardFilterForm').submit();
                    }
                });
                document.getElementById('filter').addEventListener('change', function() {
                    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
                    document.getElementById('dashboardFilterForm').submit();
                });

                


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
                    options: {
                        scales: {
                            x: {
                                display: false
                            },
                            y: {
                                display: false
                            }
                        },
                        plugins: {
                            tooltip: {
                                enabled: true
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
            renderBookingsChart(@json($currentPeriodData['bookingsCount']));


            function prepareVisitorsChartData(visitorData) {

                var labels = Object.keys(visitorData);
                var counts = Object.values(visitorData);

                return {
                    labels: labels,
                    datasets: [{
                        label: 'Visitor Count',
                        backgroundColor: 'rgba(76, 175, 80, 0.6)',
                        borderColor: 'rgba(76, 175, 80, 1)',
                        data: counts
                    }]
                };
            }

            function renderVisitorsChart(visitorData) {
                var ctx = document.getElementById('visitorsChart').getContext('2d');
                var chartData = prepareVisitorsChartData(visitorData);

                var visitorsChart = new Chart(ctx, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        scales: {
                            x: {
                                display: false
                            },
                            y: {
                                display: false
                            }
                        },
                        plugins: {
                            tooltip: {
                                enabled: true
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            renderVisitorsChart(@json($currentPeriodData['visitorsCount']));


            function prepareRefundsChartData(refundsData) {
                var refundCounts = {};

                refundsData.forEach(function(data) {
                    var date = data.requestDate;
                    if (!refundCounts[date]) {
                        refundCounts[date] = 0;
                    }
                    refundCounts[date]++;
                });
                var labels = Object.keys(refundCounts);
                var counts = Object.values(refundCounts);

                return {
                    labels: labels,
                    datasets: [{
                        label: 'Request Count',
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        data: counts
                    }]
                };
            }

            function renderRefundsChart(refundData) {
                var ctx = document.getElementById('refundsChart').getContext('2d');
                var chartData = prepareRefundsChartData(refundData);

                var refundsChart = new Chart(ctx, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        scales: {
                            x: {
                                display: false
                            },
                            y: {
                                display: false
                            }
                        },
                        plugins: {
                            tooltip: {
                                enabled: true
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
            renderRefundsChart(@json($currentPeriodData['refunds']));

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
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192)',
                            borderWidth: 1,
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

            renderProductRankingChart(@json($currentPeriodData['productRankingData']));

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
                        backgroundColor: 'rgb(75, 192, 192)',
                        borderColor: 'rgba(75, 192, 192, 1)',
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

            renderChart(@json($currentPeriodData['revenue']));

            
        </script>
    @endpush

@endsection
