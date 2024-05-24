@extends('layouts.app')

@section('content')
<style>
    .viewbutton {
        background-color: #D5E200 !important;
    }

    .viewbutton:hover {
        background-color: #c4c853 !important;
    }

    .btn-danger {
        color: black;
        font-family: 'Rubik', sans-serif;
    }

    .btn-danger:hover {
        color: #3C332A;
    }

    #reportTabs .nav-link {
        color: #3C332A;
    }

    #reportTabs .nav-link.active {
        color: #bdc433;
    }

    h4 {
        color: #3C332A;
    }

    .modal-backdrop {
        width: 100%;
    }

    th {
        font-family: 'Rubik', sans-serif;
    }

    .hidetd {
        display: none;
    }

    .report-image {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    .report-card {
        margin-top: 20px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col text-center">
            <h1>Report Details</h1>
        </div>
    </div>

    <div class="report-card card p-4">
        <div class="row">
            <div class="col-md-6">
                @if ($report->image)
                    <div class="text-center">
                        <img src="data:image/jpeg;base64,{{ base64_encode($report->image) }}" alt="{{ $report->SUBJECT }}" class="report-image img-fluid rounded">
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <div class="mb-2 mt-3">
                    <h4>Subject</h4>
                    <p>{{ $report->SUBJECT }}</p>
                </div>
                <div class="mb-2">
                    <h4>Description</h4>
                    <p>{{ $report->description }}</p>
                </div>
                <div class="mb-2">
                    <h4>Email</h4>
                    <p>{{ $report->email }}</p>
                </div>
                <div class="mb-2">
                    <h4>Submitted At</h4>
                    <p>{{ $report->created_at->toFormattedDateString() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4 text-center">
        <a href="{{ route('employeeReports.index') }}" class="btn viewbutton mx-2" style="font-family: 'Rubik', sans-serif;">Back to Reports</a>
    </div>
</div>
@endsection