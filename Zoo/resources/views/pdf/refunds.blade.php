{{-- filter if is pending/all/or processed which one is chosen then display --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Refunds Report</title>
</head>
<style>
    h1 {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-weight: bolder;
        font-size: x-large;
    }

    #refunds {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #refunds td,
    #refunds th {
        border: 1px solid #ddd;
        padding: 8px;
        font-size: small;
    }

    #refunds th {
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
    <h3>Refunds Report</h3>
    <p>Exported on: {{ $currentDate->format('F j, Y') }}</p>


    @if ($fromdate && $todate)
    <p>Showing {{ $requestType }} refunds requests from {{ $fromdate }} to {{ $todate }}.</p>
    @else
        <p>Showing {{ $requestType }} refunds requests.</p>
    @endif
    
    @if ($chartVisibility == 'visible')
    <div style="text-align: center;">
        <img src="{{ $chartImage }}" alt="Chart" style="width:400;height:200">
    </div>
    @endif

    <table class="table" id="refunds">
        <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Booking ID</th>
                <th scope="col">Reason</th>
                <th scope="col">Request Date</th>
                <th scope="col">Amount</th>
                <th scope="col">Status</th>
                <th scope="col">Action Date</th>
            </tr>
        </thead>
        <tbody>
            @if (count($refunds) > 0)
                @foreach ($refunds as $refund)
                    <tr>
                        <td>{{ $refund->id }}</td>
                        <td>{{ $refund->firstName }}</td>
                        <td>{{ $refund->lastName }}</td>
                        <td>{{ $refund->bookingID }}</td>
                        <td>{{ $refund->reasons }}</td>
                        <td>{{ $refund->requestDate }}</td>
                        <td>{{ $refund->priceRefund }}
                        <td>{{ $refund->status }}</td>
                        <td>{{ $refund->approveDate }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9">No refunds found.</td>
                </tr>
            @endif

        </tbody>
        @if ($requestType === 'approved' || $requestType === 'disapproved')
            <tfoot>
                <tr>
                    <th colspan="6">Total:</th>
                    <th colspan="3">$ {{ $totalAmount }}</th>
                </tr>
            </tfoot>
        @endif
    </table>
    <p>Refunds Count: {{ count($refunds) }}</p>
</body>

</html>
