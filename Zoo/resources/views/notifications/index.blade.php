@extends('layouts.app')

@section('content')
    <style>
        .noti-data {
            /* font-weight: bolder; */
            padding: 15px;
            padding-top: 10px;
            padding-bottom: 10px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            display: flex;
            flex-grow: 1;
            justify-content: space-between;
        }

        .noti-data:hover {
            background-color: #F8F9FA;
        }

        .unread-notification {
            font-weight: bolder;
        }

        input {
            border: 1px solid #DEE2E6 !important;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            font-family: 'Rubik', sans-serif;
        }

        .btn {
            white-space: nowrap;
        }

        p {
            font-size: 16px;
            margin: 0;
            padding: none;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            flex-grow: 1;
        }

        .timediff {
            display: inline-block;
        }

        .modal-backdrop {
            width: 100%;
        }
        .viewbutton{
        background-color: #D5E200!important;
    }
    .viewbutton:hover{
        background-color: #c4c853!important;
    }

        @media (max-width: 768px) {
            .noti-text {
                font-size: 14px;
                word-wrap: break-word;
            }
        }
    </style>
    <div class="container">
        <h4 class="mb-4 float-start">Notifications</h4>
        <span class="text-muted ms-2">({{ auth()->user()->unreadNotifications()->count() }} unread)</span>

<br><br>
        <form id="notificationForm" action="{{ route('notifications.deleteSelected') }}" method="POST">
            @csrf
            @method('DELETE')
            @if ($notifications->count() > 0)
                <div class="buttons">
                    <button type="button" class="btn viewbutton mb-2 me-2" onclick="deleteSelectedNotifications()">Delete
                        Selected</button>
                    <button type="button" class="btn viewbutton mb-2" data-bs-toggle="modal"
                        data-bs-target="#deleteAllModal">Clear All</button>
                </div>
                <div class="categorysection m-0 mt-3">
                    @foreach ($notifications as $notification)
                        @php
                            $createdAt = $notification->created_at;
                            $timeDiff = $createdAt->diffForHumans();
                        @endphp
                        <div class="border-bottom not">
                            <label
                                class="dropdown-item d-flex align-items-center  @if ($notification->unread()) unread-notification @endif">
                                <a href="{{ route('notifications.show', ['type'=>$notification->data['type'],'id' => $notification->id]) }}"
                                    class="dropdown-item align-items-center @if ($notification->unread()) unread-notification @endif">

                                    <div class="noti-data">
                                        <input type="checkbox" class="form-check-input me-3" name="selectedNotifications[]"
                                            value="{{ $notification->id }}">
                                        <p class="noti-text">{{ $notification->data['data'] }}</p>
                                        <span class="text-muted timediff"
                                            style="font-size: 14px;">{{ $timeDiff }}</span>
                                    </div>

                                </a>
                            </label>
                            {{-- <a href="{{ route('notifications.show', ['id' => $unreadNotification->id]) }}"
                        class="dropdown-item d-flex align-items-center py-2 @if ($unreadNotification->unread()) unread-notification @endif">
                        <div class="d-flex flex-grow-1 justify-content-between noti-data">
                            <p>{{ $unreadNotification->data['data'] }}</p>
                            <span class="text-muted">{{ $timeDiff }}</span>
                        </div>
                    </a> --}}
                        </div>
                    @endforeach
                </div>
            @else
                <p style="text-align: center;">No notifications found.</p>
            @endif

        </form>

        <form id="deleteAllForm" action="{{ route('notifications.deleteAll') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <div class="modal fade" id="deleteSelectedModal" tabindex="-1" aria-labelledby="deleteSelectedModalLabel"
            aria-hidden="true" style="width: 100%;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-5" id="deleteSelectedModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="deleteSelectedModalBody">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="confirmDelete"
                            style="font-family: 'Rubik', sans-serif;color:white;"
                            onclick="confirmDeleteSelectedNotif()">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true"
            style="width: 100%;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-5" id="deleteAllModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete all notifications?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="confirmDelete"
                            style="font-family: 'Rubik', sans-serif;color:white;"
                            onclick="deleteAllNotifications()">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="selectedInputCount" value="">
    <script>
        function deleteSelectedNotifications() {
            var selectedCount = $('input[name="selectedNotifications[]"]:checked').length;
            $('#selectedInputCount').val(selectedCount);
            if (selectedCount != 0) {
                $('#deleteSelectedModalBody').html('Are you sure you want to delete ' + selectedCount + ' notifications?');
                $('#deleteSelectedModal').modal('show');
            }

        }

        function confirmDeleteSelectedNotif() {
            var selctedCount = $('#selectedInputCount').val();
            console.log('deleting '.selectedCount);
            $('#notificationForm').submit();
        }

        function deleteAllNotifications() {
            $('#deleteAllForm').submit();
        }
    </script>
@endsection
