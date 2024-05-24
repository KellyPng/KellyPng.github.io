<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookings Report</title>

</head>
<style>
    h1 {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-weight: bolder;
        font-size: x-large;
    }

    #bookings {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #bookings td,
    #bookings th {
        border: 1px solid #ddd;
        padding: 8px;
        font-size: small;
    }

    #bookings th {
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
    @if ($dateFilter === 'all')
        <p>Showing all bookings</p>
    @elseif ($dateFilter === 'visitdate')
        @if ($filter === 'last12months')
            <p>Showing bookings for the last 12 months based on visit date.</p>
        @elseif($filter === 'last6months')
            <p>Showing bookings for the last 6 months based on visit date.</p>
        @elseif($filter === 'last3months')
            <p>Showing bookings for the last 3 months based on visit date.</p>
        @elseif($filter === 'lastmonth')
            <p>Showing last month bookings based on visit date.</p>
        @elseif($filter === 'thismonth')
            <p>Showing this month bookings based on visit date.</p>
        @elseif($filter === 'dateRange')
            <p>Showing bookings from {{ $fromdate }} to {{ $todate }} based on visit date.</p>
        @endif
    @elseif ($dateFilter === 'bookingdate')
        @if ($filter === 'last12months')
            <p>Showing bookings for the last 12 months based on booking date.</p>
        @elseif($filter === 'last6months')
            <p>Showing bookings for the last 6 months based on booking date.</p>
        @elseif($filter === 'last3months')
            <p>Showing bookings for the last 3 months based on booking date.</p>
        @elseif($filter === 'lastmonth')
            <p>Showing last month bookings based on booking date.</p>
        @elseif($filter === 'thismonth')
            <p>Showing this month bookings based on booking date.</p>
        @elseif($filter === 'dateRange')
            <p>Showing bookings from {{ $fromdate }} to {{ $todate }} based on booking date.</p>
        @endif
    @endif

    @if ($chartVisibility === 'visible')
        <div style="text-align: center;">
            <img src="{{ $chartImage }}" alt="Chart" style="width:400;height:200">
        </div>
    @endif
    <br>
    <table class="table" id="bookings">
        <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Visitor Name</th>
                <th scope="col">Ticket Type</th>
                <th scope="col">Visit Date</th>
                <th scope="col">Booking Date</th>
                <th scope="col">Ticket Status</th>
                <th scope="col">Quantity</th>
                <th scope="col">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @if (count($bookings) > 0)
                @foreach ($bookings as $booking)
                    <tr>
                        <td>{{ $booking->bookingID }}</td>
                        <td>{{ $booking->visitor->firstName }} {{ $booking->visitor->lastName }}</td>
                        <td>
                            @if ($booking->ticketType->ticketTypeName == 'Single Park')
                                {{ $booking->ticketType->ticketTypeName }} : {{ $booking->bookParks->park->parkName }}
                            @else
                                {{ $booking->ticketType->ticketTypeName }}
                            @endif
                        </td>
                        <td>{{ $booking->bookingDate }}</td>
                        <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                        <td>
                            {{ $booking->bookingStatus == 0 ? 'Valid' : 'Used' }}
                        </td>
                        <td>
                            @if (isset($demographicQuantities[$booking->id]))
                                @foreach ($demographicQuantities[$booking->id] as $categoryName => $quantity)
                                    {{ $categoryName }} : {{ $quantity }}<br>
                                @endforeach
                            @endif
                        </td>
                        <td>$ {{ $booking->totalPrice }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>No Visitors Found.</td>
                </tr>
            @endif

        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align: right;">Total:</th>
                <th>{{ $totalQuantity }}</th>
                <th>$ {{ $totalPrice }}</th>
            </tr>
        </tfoot>
    </table>
    <p>Total bookings found: {{ count($bookings) }}</p>
</body>

</html>
