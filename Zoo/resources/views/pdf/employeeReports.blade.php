<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Employee Reports</title>
</head>
<style>
    h1 {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-weight: bolder;
        font-size: x-large;
    }

    #reports {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #reports td,
    #reports th {
        border: 1px solid #ddd;
        padding: 8px;
        font-size: small;
    }

    #reports th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #eeeeee;
    }

    .zoo {
        color: #FFC107;
    }
</style>
<body>
    <h1><span class="nav_logo-name" style="font-size:larger;"><span style="font-family: 'Rubik', sans-serif;"
        class="zoo">ZOO</span>WILDLIFE</span></h1>
<h3>Bookings Report</h3>
<p>Exported on: {{ $currentDate->format('F j, Y') }}</p>

@if ($filter)
    @if ($filter=='dateRange')
        <p>Showing employee reports submitted between {{$fromdate}} to {{$todate}}</p>
    @else
        @if ($filter == 'thisweek')
            <p>Showing employee reports submitted this week.</p>
        @elseif ($filter == 'thismonth')
        <p>Showing employee reports submitted this month.</p>
        @elseif ($filter == 'lastmonth')
        <p>Showing employee reports submitted last month.</p>
        @elseif ($filter == 'last3months')
        <p>Showing employee reports submitted for the past 3 months.</p>
        @elseif ($filter == 'last6months')
        <p>Showing employee reports submitted for the past 6 months.</p>
        @elseif ($filter == 'last12months')
        <p>Showing employee reports submitted for the past 12 months.</p>
        @endif
    @endif
@endif

@if ($chartVisibility === 'visible')
        <div style="text-align: center;">
            <img src="{{ $chartImage }}" alt="Chart" style="width:400;height:200">
        </div>
    @endif
<br>
<table class="table" id="reports">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Subject</th>
            <th scope="col">Description</th>
            <th scope="col">Employee Email</th>
            <th scope="col">Submitted At</th>
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
                <td colspan="5" style="text-align: center;">No employee reports found.</td>
            </tr>
        @endif

    </tbody>
</table>
<p>Total employee reports found: {{ count($reports) }}</p>
</body>
</html>