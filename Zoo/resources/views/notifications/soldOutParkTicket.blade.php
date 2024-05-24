@extends('layouts.app')

@section('content')
    <style>
        .categorysection {
            background-color: #f1f1f1;
            padding: 2%;
            border-radius: 5px;
        }

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

        .viewbutton{
        background-color: #D5E200!important;
        font-family: 'Rubik', sans-serif;
    }
    .viewbutton:hover{
        background-color: #c4c853!important;
    }
    .ticket-info {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .ticket-info h4 {
            margin-top: 0;
            color: #333;
        }

        .ticket-info p {
            color: #666;
        }

        .footer {
            margin-top: 20px;
            color: #999;
        }
    </style>
    <div class="container">


        <div class="categorysection m-0 mt-3">
            <h4 class="d-inline">{{ $notification->data['data'] }}</h4>
            <span class="d-inline float-end">{{ $notification->created_at->format('D, M d Y, H:i a') }}</span>
            <hr>
            <?php $admin = \App\Models\User::find($notification->data['admin']['id']); ?>
        <p>Dear {{ $admin->firstname }},</p>

            <p>The park ticket stated below has sold out.</p>
            <p>You may consider adding more capacity to meet the demand or taking no action if deemed appropriate. Please review the ticket availability and make the necessary decisions.</p>
            <div class="ticket-info">
                <h4>Ticket Details:</h4>
                <?php $ticketObject = \App\Models\SingleParkTicketAvailability::find($notification->data['singleParkTicket']['id']); ?>
                <p><strong>Park Ticket:</strong> {{$ticketObject->parkTicket->park->parkName}}</p>
                <p><strong>Sold Out Date:</strong> {{$notification->data['singleParkTicket']['date']}}</p>
                <p><strong>Current Capacity:</strong> {{$ticketObject->parkTicket->capacity}}</p>
            </div>
            <br>
            <p>Click here to add more capacity for this ticket.</p>
            <a href="{{ route('inventory_page', ['date' => $notification->data['singleParkTicket']['date'], 'active_tab' => 'single-park-tickets']) }}" class="btn viewbutton">Add Capacity</a>
                <br>
                <div class="footer">
                    <p style="font-size: medium;">Best Regards,</p>
                    <p style="font-size: medium;">Zoo Wildlife</p>
                </div>
            {{-- <p><strong>Date:</strong> {{ $notification->created_at->format('Y-m-d H:i:s') }}</p> --}}
        </div>
    </div>
@endsection
