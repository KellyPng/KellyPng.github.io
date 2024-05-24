<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Rankings</title>
</head>
<style>
    h1{
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-weight: bolder;
        font-size: x-large;
    }
    #ranking {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #ranking td,
    #ranking th {
        border: 1px solid #ddd;
        padding: 8px;
        font-size: small;
    }

    #ranking th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #eeeeee;
    }
    .zoo{
        color: #FFC107;
    }
</style>

<body>
<h1><span class="nav_logo-name" style="font-size:larger;"><span style="font-family: 'Rubik', sans-serif;" class="zoo">ZOO</span>WILDLIFE</span></h1>
    <h3>Product Ranking Report</h3>
    <p>Exported on: {{ $currentDate->format('F j, Y') }}</p>
    @if ($filter === 'last12months')
        <p>Showing product ranking for the past 12 months</p>
    @elseif ($filter === 'last6months')
    <p>Showing product ranking for the past 6 months</p>
    @elseif ($filter === 'last3months')
    <p>Showing product ranking for the past 3 months</p>
    @elseif ($filter === 'lastmonth')
    <p>Showing last month's product ranking</p>
    @elseif ($filter === 'thismonth')
    <p>Showing this month's product ranking</p>
    @elseif ($filter === 'dateRange')
    <p>Showing product ranking filtered from {{ $startDate }} to {{ $endDate }}</p>
    @endif
    
    @if ($chartVisibility === 'visible')
        <div style="text-align: center;">
            <img src="{{ $chartImage }}" alt="Chart" style="width:400;height:200">
        </div>
    @endif
    <br>
    <table class="table" id="ranking">
        <thead>
            <tr>
                <th scope="col">Ranking</th>
                <th scope="col">Ticket</th>
                <th scope="col">Quantity Sold</th>
                <th scope="col">Trend</th>
            </tr>
        </thead>
        <tbody>
            @if (count($currentPeriodData) > 0)
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
                                <span class="text-success" style="color: green;">{{ $product['percentageChange'] }}% Increase</span>
                            @elseif ($product['changeDirection'] === 'decrease')
                                <span class="text-danger" style="color: red;">{{ $product['percentageChange'] }}% Decrease</span>
                            @elseif ($product['changeDirection'] === 'stable')
                                <span class="text-warning" style="color: orange;">{{ $product['percentageChange'] }}% Stable</span>
                            @endif
                        @else
                            No Previous Data
                        @endif
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">No Tickets Found.</td>
                </tr>
            @endif

        </tbody>
    </table>
    <p>Total bookings found: {{ count($currentPeriodData) }}</p>
</body>
</html>