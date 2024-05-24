@extends('layouts.app')

@section('content')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Define styles for headings and paragraphs */
        h4 {
            color: #333333;
            font-size: 18px;
            margin-bottom: 10px;
        }

        p {
            color: #555555;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .viewbutton {
            background-color: #D5E200 !important;
            font-family: 'Rubik', sans-serif;
        }

        .viewbutton:hover {
            background-color: #c4c853 !important;
        }
        .request-info {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .request-info h2 {
            margin-top: 0;
            color: #333;
        }

        .request-info p {
            color: #666;
        }
    </style>


    <div class="container">


        <div class="categorysection m-0 mt-3">
            <h4 class="d-inline">{{ $notification->data['data'] }}</h4>
            <span class="d-inline float-end">{{ $notification->created_at->format('D, M d Y, H:i a') }}</span>
            <hr>
            <?php $admin = \App\Models\User::find($notification->data['admin']['id']); ?>
        <p>Dear {{ $admin->firstname }},</p>
            <p>A refund request has been submitted by a customer for the following booking:</p>

            <div class="request-info">
                <p><strong>Booking ID:</strong> {{ $notification->data['refundRequest']['bookingID'] }}</p>
                <p><strong>Customer Name:</strong> {{ $notification->data['refundRequest']['firstName'] }} {{ $notification->data['refundRequest']['lastName'] }}</p>
                <p><strong>Reason:</strong> {{ $notification->data['refundRequest']['reasons'] }}</p>
                <p><strong>Amount:</strong> $ {{ $notification->data['refundRequest']['priceRefund'] }}</p>
            </div>
            <br>
            <p>Please review the request and take appropriate action accordingly. You can process the refund here:</p>
            <a href="{{ route('refund.refundRequest') }}" class="btn viewbutton">Manage refund</a>
            <br><br>
            <div class="footer">
                <p style="font-size: medium;">Best Regards,</p>
                <p style="font-size: medium;">Zoo Wildlife</p>
            </div>


        </div>
    </div>
@endsection
