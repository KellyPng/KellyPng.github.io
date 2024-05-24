<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Revenue Report</title>
</head>
<style>
    h1 {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-weight: bolder;
        font-size: x-large;
    }

    #revenues {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #revenues td,
    #revenues th {
        border: 1px solid #ddd;
        padding: 8px;
        font-size: small;
    }

    #revenues th {
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
    <h3>Revenue Report</h3>
    <p>Exported on: {{ $currentDate->format('F j, Y') }}</p>
    @if ($filter === 'last12months')
        <p>Showing revenue for the last 12 months</p>
    @elseif ($filter === 'last6months')
        <p>Showing revenue for the last 6 months</p>
    @elseif ($filter === 'last3months')
        <p>Showing revenue for the last 3 months</p>
    @elseif ($filter === 'lastmonth')
        <p>Showing last months revenue</p>
    @elseif ($filter === 'thismonth')
        <p>Showing this month's revenue</p>
    @elseif ($filter === 'dateRange')
        <p>Showing revenue from {{ $startDate }} to {{ $endDate }}</p>
    @endif

    @if ($chartVisibility === 'visible')
        <div style="text-align: center;">
            <img src="{{ $chartImage }}" alt="Chart">
        </div>
    @endif
    <br>
    <table id="revenues">
        <thead>
            <tr>
                <th>Date</th>
                <th>Bookings</th>
                <th>Sales</th>
                <th>Refunds</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @if (count($getRevenueData) > 0)
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
                    <td colspan="5">No data found.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">Total:</th>
                <th>$ {{ $totalRevenue }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
