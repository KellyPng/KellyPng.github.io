<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Feedback and Reviews Report</title>
</head>
<style>
    h1 {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-weight: bolder;
        font-size: x-large;
    }

    #reviews {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #reviews td,
    #reviews th {
        border: 1px solid #ddd;
        padding: 8px;
        font-size: small;
    }

    #reviews th {
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

    @if ($starsFilter && $fromdate && $todate)
        @if ($starsFilter === 'all')
            <p>Showing feedback and reviews from <strong>{{ $fromdate }}</strong> to
                <strong>{{ $todate }}</strong></p>
        @else
            <p>Showing feedback and reviews with <strong>{{ $starsFilter }} Star(s)</strong> from
                <strong>{{ $fromdate }}</strong> to <strong>{{ $todate }}</strong>
            </p>
        @endif
    @elseif ($starsFilter)
        @if ($starsFilter != 'all')
            <p>Showing feedback and reviews with <strong>{{ $starsFilter }} Star(s)</strong></p>
        @else
            <p>Showing all feedback and reviews</p>
        @endif

    @endif

    <h3>Feedback and Reviews Report</h3>
    @if ($chartVisibility === 'visible')
        <div style="text-align: center;">
            <img src="{{ $chartImage }}" alt="Chart" style="width:400;height:200">
        </div>
    @endif
    <br>
    <table class="table" id="reviews">
        <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Visitor Name</th>
                <th scope="col">Feedback</th>
                <th scope="col">Stars</th>
                <th scope="col">Submitted at</th>
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
                        <td>{{ $review->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">No Reviews Found.</td>
                </tr>
            @endif

        </tbody>
    </table>
    <p>Total reviews found: {{ count($reviews) }}</p>
</body>

</html>
