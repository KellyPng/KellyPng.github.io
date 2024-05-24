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

        .viewbutton {
            background-color: #D5E200 !important;
            font-family: 'Rubik', sans-serif;
        }

        .viewbutton:hover {
            background-color: #c4c853 !important;
        }

        .feedback-info {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .feedback-info h2 {
            margin-top: 0;
            color: #333;
        }

        .feedback-info p {
            color: #666;
        }

        .star {
            font-size: 20px;
            color: gold;
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
        <p>A new feedback review has been submitted by a customer for your attention. Here are the details:</p>

        <div class="feedback-info">
            <p><strong>Customer Name:</strong> {{ $notification->data['feedback']['visitor_name'] }}</p>
            <p><strong>Rating:</strong>
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $notification->data['feedback']['stars'])
                        <span class="star">&#9733;</span>
                    @else
                        <span class="star">&#9734;</span>
                    @endif
                @endfor
            </p>
            <p><strong>Description:</strong> {{ $notification->data['feedback']['description'] }}</p>
        </div>
        <br>
        <p>Click here to view all feedbacks.</p>
        <a href="{{ route('feedback_reviews.index') }}" class="btn viewbutton">View All Feedbacks</a>
        <br>
        <div class="footer">
            <p style="font-size: medium;">Best Regards,</p>
            <p style="font-size: medium;">Zoo Wildlife</p>
        </div>
        </div>
    </div>
@endsection
