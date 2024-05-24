<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
<style>
    .welcome {
        font-size: 20px;
    }

    .dropdown .btn {
        width: 50px;
        max-width: 100%;
        border: none;
    }

    .dropdown-menu {
        margin-right: 25px !important;
    }

    .badge {
        margin-right: 2%;
    }

    .dropdown-menu li {
        word-wrap: break-word;
    }

    .dropdown .notification li a {
        font-family: 'Noto', serif !important;
    }

    .unread-notification {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .notification-content {
        white-space: normal;
    }

    .no-notification {
        text-align: center;
    }

    .notif-text {
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

    @media (max-width: 768px) {
    .dropdown-menu {
        left: 50%;
        transform: translateX(-50%);
        max-width: 90vw;
    }
}
</style>


<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
        {{-- <div class="header_img"> <img src="https://i.imgur.com/hczKIze.jpg" alt=""> </div> --}}
        <div class="dropdown" id="notification-dropdown">
            <a class="btn mt-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                id="notification-btn">
                <span class="material-symbols-outlined me-5">
                    notifications
                </span>
                @if (auth()->user()->unreadNotifications()->count())
                    <span class="position-absolute top-50 start-100 translate-middle badge rounded-pill bg-danger me-1"
                        id="unread-notification">
                        {{ auth()->user()->unreadNotifications()->count() }}
                    </span>
                @endif

            </a>

            <ul>
                <li class="dropdown-item">
                    <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown"
                        style="background-color: white; min-width:300px; white-space:normal;">
                        <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                            @php
                                $unreadNotifications = auth()->user()->unreadNotifications;
                                $unreadCount = $unreadNotifications->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <p><span id="inside_unreadcount">{{ $unreadCount }}</span> New Notification(s)</p>
                            @endif
                            <form id="mark-all-read-form" action="{{ route('notifications.mark-all-read') }}"
                                method="POST">
                                @csrf
                                <a href="{{ route('notifications.mark-all-read') }}"
                                    onclick="document.getElementById('mark-all-read-form').submit();"
                                    class="btn-link text-muted p-0 m-0 border-0 bg-transparent">Clear all</a>
                            </form>
                        </div>
                        <div class="p-1" id="displayUnreadNotification">
                            @if ($unreadCount > 0)
                                @foreach ($unreadNotifications->take(5) as $notification)
                                    @php
                                        $createdAt = $notification->created_at;
                                        $timeDiff = $createdAt->diffForHumans();
                                    @endphp
                                    <a href="{{ route('notifications.show', ['type'=>$notification->data['type'],'id' => $notification->id]) }}"
                                        class="dropdown-item align-items-center py-2 @if ($notification->unread()) unread-notification @endif">
                                        <div>
                                            <p class="notif-text">{{ $notification->data['data'] }}</p>
                                            <span class="tx-12 text-muted timediff">{{ $timeDiff }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <p class="no-notification">No new notifications</p>
                            @endif
                        </div>
                        <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                            <a href="{{ route('notifications.index') }}">View all</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <nav class="nav">
                <div> <a href="{{ url('/') }}" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span
                            class="nav_logo-name" style="font-size:larger;"><span
                                style="font-weight: 700;font-family: 'Rubik', sans-serif;"
                                class="text-warning">ZOO</span>WILDLIFE</span> </a>
                    <div class="nav_list">
                        {{-- <a href="{{ url('/') }}" class="nav_link{{ request()->is('/') ? ' active' : '' }}"> <i
                                class='bx bx-grid-alt nav_icon'></i> <span class="nav_name">Dashboard</span> </a> --}}
                        <a href="{{ route('tickets.index') }}"
                            class="nav_link{{ request()->is('tickets*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                confirmation_number
                            </span> <span class="nav_name">Tickets</span> </a>
                        <a href="{{ route('parks.index') }}"
                            class="nav_link{{ request()->is('parks*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                cruelty_free
                            </span> <span class="nav_name">Parks</span></a>
                        <a href="{{ url('inventory') }}"
                            class="nav_link{{ request()->is('inventory*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                inventory_2
                            </span> <span class="nav_name">Inventory</span> </a>
                        <a href="{{ route('employees.index') }}"
                            class="nav_link{{ request()->is('employees*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                group
                            </span> <span class="nav_name">Employees</span> </a>
                        <a href="{{ route('discounts.index') }}"
                            class="nav_link{{ request()->is('discounts*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                percent
                            </span> <span class="nav_name">Discounts</span> </a>
                        <a href="{{ route('feedback_reviews.index') }}"
                            class="nav_link{{ request()->is('feedback-reviews*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                star
                            </span> <span class="nav_name">Feedback Reviews</span> </a>
                        <a href="{{ route('refund.refundRequest') }}"
                            class="nav_link{{ request()->is('refund*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                currency_exchange
                            </span> <span class="nav_name">Refund</span> </a>
                        {{-- <a href="{{ route('reports.index') }}"
                            class="nav_link{{ request()->is('reports*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                monitoring
                            </span> <span class="nav_name">Reports Analytics</span> </a> --}}
                        <a href="{{ route('employeeReports.index') }}"
                            class="nav_link{{ request()->is('employeeReports*') ? ' active' : '' }}"> <span
                                class="material-symbols-outlined nav_icon">
                                flag
                            </span> <span class="nav_name">Employee Reports</span> </a>
                        {{-- <a href="{{ route('reports.index') }}" class="nav_link{{ request()->is('reports*') ? ' active' : '' }}"> <span class="material-symbols-outlined nav_icon">
                            description
                            </span> <span class="nav_name">Reports</span> </a> --}}
                    </div>
                </div> <a href="{{ route('logout') }}" class="nav_link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class='bx bx-log-out nav_icon'></i>
                    <span class="nav_name">Logout</span>
                </a>
            </nav>
        </nav>
    </div>
    <!-- Container Main start -->
    {{-- <div class="height-100 bg-light">
        <h4>Main Components</h4>
    </div> --}}
    <!-- Container Main end -->

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

</body>

<script>
    var currentAdminId = {{ auth()->user()->id }};
    document.addEventListener('DOMContentLoaded', function() {

        var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        var channel = pusher.subscribe('notifications');

        channel.bind('my-event', function(data) {
            console.log(data && data.data.admin && data.data.admin.id === currentAdminId);
            if (data) {
                updateUI(data);
            } else {
                console.error('Received undefined data from Pusher');
            }
            // document.getElementById('unread-notification').innerText = data.unreadCount;
            // document.getElementById('inside_unreadcount').innerText = data.unreadCount;
            // updateNotificationDropdown(data.notificationData);
        });

        function handleNotificationClick(notificationId) {
            fetch('/notifications/mark-as-read/' + notificationId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    updateUI(data);
                    //updateUnreadCount(data.unreadCount);
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
        }

        function updateNotificationDropdown(data) {
            var dropdownContainer = document.getElementById('displayUnreadNotification');
            while (dropdownContainer.children.length >= 5) {
                dropdownContainer.lastChild.remove();
            }
            var timeDiff = moment(data.data.notification.created_at).fromNow();
            // var notificationHtml = '<a href="notifications.show/' + notificationData.id +
            //     '" class="dropdown-item d-flex align-items-center py-2 unread-notification" onclick="handleNotificationClick(\'' +
            //     notificationData.id + '\')">' +
            //     '<div class="flex-grow-1 me-2">' +
            //     '<p>' + notificationData.data.data + '</p>' +
            //     '<span class="tx-12 text-muted">' + timeDiff + '</span>' +
            //     '</div>' +
            //     '</a>';

                switch (data.type) {
                case 'feedback':
                    notificationContent = `<p class="notif-text" style="margin: 0;padding: none;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;flex-grow: 1;">New feedback has been submitted.</p>`;
                    break;
                case 'refund':
                    notificationContent = `<p class="notif-text" style="margin: 0;padding: none;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;flex-grow: 1;">New refund request submitted.</p>`;
                    break;
                case 'soldOutTicket':
                    notificationContent = `<p class="notif-text" style="margin: 0;padding: none;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;flex-grow: 1;">Sold Out Alert: ${data.data.ticket.ticket_type.ticketTypeName} ticket</p>`;
                    break;
                case 'soldOutParkTicket':
                    notificationContent = `<p class="notif-text" style="margin: 0;padding: none;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;flex-grow: 1;">Sold Out Alert: ${data.data.singleParkTicket.notification.data.singleParkTicket.park_ticket.park.parkName} park ticket</p>`;
                    break;
                default:
                    notificationContent = '<p class="notif-text" style="margin: 0;padding: none;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;flex-grow: 1;">New notification</p>';
            }

            var notificationHtml = `
                <a href="notifications.show/${data.type}/${data.data.notification.id}" class="dropdown-item align-items-center py-2 @if ($notification->unread()) unread-notification @endif">
                    <div">
                        ${notificationContent}
                        <span class="tx-12 text-muted">${timeDiff}</span>
                    </div>
                </a>`;
            dropdownContainer.insertAdjacentHTML('afterbegin', notificationHtml);
        }

        function updateUI(data) {
            var unreadCountElement = document.getElementById('unread-notification');
            var insideUnreadCountElement = document.getElementById('inside_unreadcount');
            if (unreadCountElement && insideUnreadCountElement) {
                if (data && data.data.admin && (data.data.admin.id === currentAdminId)) {
                    var currentUnreadCount = parseInt(unreadCountElement.innerText) || 0;
                var newUnreadCount = currentUnreadCount + 1;
                unreadCountElement.innerText = newUnreadCount;
                insideUnreadCountElement.innerText = newUnreadCount;
                }
            }

            if (data && data.data.admin && (data.data.admin.id === currentAdminId)) {
            updateNotificationDropdown(data);
            }
        }

        //     var storedUnreadCount = localStorage.getItem('unreadNotificationCount');
        // if (storedUnreadCount) {
        //     // Update UI with the stored count
        //     document.getElementById('unread-notification').innerText = storedUnreadCount;
        //     document.getElementById('inside_unreadcount').innerText = storedUnreadCount;
        // }
    });
</script>

{{-- <script>
    document.addEventListener("DOMContentLoaded", function() {
    var notificationBtn = document.getElementById("notification-btn");
    var notificationCount = document.getElementById("unread-notification");
    var notificationMenu = document.querySelector(".dropdown-menu.notification");
    var isActive = false;

    function updateNotificationCount() {
            notificationCount.innerText = '{{ auth()->user()->unreadNotifications->count() }}';
        }

    // Toggle function to switch between icons
    function toggleIcon() {
        if (isActive) {
            notificationBtn.innerHTML = '<span class="material-symbols-outlined">notifications_active</span>';
        } else {
            notificationBtn.innerHTML = '<span class="material-symbols-outlined">notifications</span>';
        }
    }

    // Click event listener for the bell icon
    notificationBtn.addEventListener("click", function(event) {
        event.stopPropagation(); // Prevent event from propagating to document body
        isActive = !isActive;
        toggleIcon();
    });

    // Click event listener for the document body
    document.body.addEventListener("click", function() {
        if (isActive) {
            isActive = false;
            toggleIcon();
        }

        if (!isActive && notificationMenu.classList.contains("show")) {
            updateNotificationCount();
        }
    });
});
</script> --}}
