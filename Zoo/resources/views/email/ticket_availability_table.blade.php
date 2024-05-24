@component('mail::message')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    th {
        background-color: #f2f2f2;
    }
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
    .button {
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 4px;
    }
</style>
<h4>Dear {{$admin->firstname}},</h4>
<p>This is a notification to inform you that the tickets below have been sold out.</p>
<p>You may consider adding more capacity to meet the demand or taking no action if deemed appropriate. Please review the ticket availability and make the necessary decisions.</p>
@if (!is_null($soldOutTickets) && $soldOutTickets->isNotEmpty())
<h2>Sold Out Tickets</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ticket Name</th>
            <th>Capacity</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($soldOutTickets as $ticket)
        <tr>
            <td>{{ $ticket->id }}</td>
            <td>{{ $ticket->ticketType->ticketTypeName }}</td>
            <td>{{ $ticket->ticketType->capacity }}</td>
            <td>{{ $ticket->date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if (!is_null($soldOutParkTickets) && $soldOutParkTickets->isNotEmpty())
<h2>Sold Out Park Tickets</h2>
<table>
    <thead>
        <tr>
            <th>Park</th>
            <th>Ticket ID</th>
            <th>Capacity</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($soldOutParkTickets as $ticket)
        <tr>
            <td>{{ $ticket->parkTicket->park->parkName }}</td>
            <td>{{ $ticket->parkTicketId }}</td>
            <td>{{ $ticket->parkTicket->capacity }}</td>
            <td>{{ $ticket->date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<br>
<p>Thank you for your attention to this matter.</p>
@endcomponent