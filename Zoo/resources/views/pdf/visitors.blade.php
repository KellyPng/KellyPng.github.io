<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visitors Report</title>

</head>
<style>
    h1 {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-weight: bolder;
        font-size: x-large;
    }

    #visitors {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #visitors td,
    #visitors th {
        border: 1px solid #ddd;
        padding: 8px;
        font-size: small;
    }

    #visitors th {
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
    <p>Exported on: {{ $currentDate->format('F j, Y') }}</p>
    @if ($selectedCountry && $fromdate && $todate)
        @if (is_array($selectedCountry))
            <p>Showing visitors who visited from <strong>{{ $fromdate }}</strong> to <strong>{{$todate}}</strong></p>
        @endif
        @if ($selectedCountry != 'all')
            <p>Showing visitors from <strong>{{ ucfirst($selectedCountry) }}</strong> who visited from
                <strong>{{ $fromdate }}</strong> to <strong>{{$todate}}</strong></p>
        @endif
        @if ($selectedCountry == 'all')
            <p>Showing visitors who visited from
                <strong>{{ $fromdate }}</strong> to <strong>{{$todate}}</strong></p>
        @endif
    @elseif (!is_array($selectedCountry))
        @if ($selectedCountry != 'all')
        <p>Showing visitors from <strong>{{ ucfirst($selectedCountry) }}</strong></p>
        @else
        <p>Showing all visitors</p>
        @endif
        
    @endif
    <h3>Visitors Report</h3>
    @if ($chartVisibility === 'visible')
        <div style="text-align: center;">
            <img src="{{ $chartImage }}" alt="Chart" style="width:400;height:200">
        </div>
    @endif
    <br>
    <table class="table" id="visitors">
        <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Country</th>
                <th scope="col">Last Purchased</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @if (count($visitors) > 0)
                @foreach ($visitors as $visitor)
                    <tr>
                        <td>{{ $visitor->id }}</td>
                        <td>{{ $visitor->firstName }}</td>
                        <td>{{ $visitor->lastName }}</td>
                        <td>{{ $visitor->email }}</td>
                        <td>{{ $visitor->contactNo }}</td>
                        <td>{{ $visitor->country }}</td>
                        <td>{{ $visitor->latestBookingDate() }}</td>
                        <td>{{ $visitor->visitorstatus() }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8">No Visitors Found.</td>
                </tr>
            @endif

        </tbody>
    </table>
    <p>Total visitors found: {{ count($visitors) }}</p>
</body>

</html>
