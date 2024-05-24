@extends('layouts.app')

@section('content')
<style>
    .discounts-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
        width: 100%;
    }

    .discount-box {
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .discount-box:hover {
        transform: scale(1.05);
    }

    .discount-details {
        margin-top: 10px;
    }

    .search-filter-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-bar {
        flex-grow: 1;
    }

    .pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
    .expired{
        font-family: 'Rubik', sans-serif;
        color: red;
        padding: 10px
    }
    .viewbutton{
        background-color: #D5E200!important;
        font-family: 'Rubik', sans-serif;
    }
    .viewbutton:hover{
        background-color: #c4c853!important;
    }
    .btn-danger{
        color: black;
    }
    .btn-danger:hover{
        color: #3C332A;
    }
    .modal-backdrop {
        width: 100%;
    }
    .expired-discount-box {
        background-color: #ff6666;
    }
    #discounttable{
        table-layout: fixed;
    }
.action{
    display: flex;
}
</style>

<script>
    $(document).ready(function () {
        $('#search').on('input', function() {
            filterDiscounts();
        });

        $('#discountFilter').change(function() {
            filterDiscounts();
        });

        function filterDiscounts() {
            var searchTerm = $('#search').val().toLowerCase();
            var filter = $('#discountFilter').val();

            $('.table tbody tr').each(function() {
                var row = $(this);
                var title = row.find('td:nth-child(2)').text().toLowerCase();
                var description = row.find('td:nth-child(7)').text().toLowerCase();
                var startDate = new Date(row.find('td:nth-child(5)').text());
                var endDate = new Date(row.find('td:nth-child(6)').text());
                var today = new Date();

                // Check if the discount matches the search term
                var matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);

                // Check if the discount matches the filter option
                var matchesFilter;
                if (filter === 'valid') {
                    matchesFilter = startDate <= today && endDate >= today;
                } else if (filter === 'expired') {
                    matchesFilter = endDate < today || startDate > today;
                } else {
                    matchesFilter = true; // Show all discounts
                }

                if (matchesSearch && matchesFilter) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        }
    });
</script>

<div class="container">
    <h1 class="d-inline">Discounts</h1>
    <a href="{{ route('discounts.manage') }}" class="btn viewbutton mx-2 d-inline float-end" style="font-family: 'Rubik', sans-serif;">New</a>
    <br><br>
    <div class="categorysection m-0 mt-2">
    
    <!-- Search and Filter Section -->
    <div class="search-filter-container">
        <div class="search-bar">
            <label for="search">Search: </label>
            <input type="text" class="form-control" id="search" placeholder="Search for title or description">
        </div>
    </div>
        <div class="dropdown">
            <label for="discountFilter">Filter: </label>
            <select class="form-select" id="discountFilter">
                <option value="all">All</option>
                <option value="valid">Valid</option>
                <option value="expired">Expired</option>
            </select>
        </div>
    <br>
    <table class="table" id="discounttable" style="width: 100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Discount For</th>
                <th>Amount</th>
                <th>Valid From</th>
                <th>Valid Till</th>
                <th>Eligibility</th>
                <th></th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if (count($discounts)>0)
        @foreach ($discounts as $discount)
            <tr>
                <td>{{$discount->id}}</td>
                <td>{{$discount->title}}</td>
                <td>{{$discount->item}}</td>
                <td>{{$discount->discount_percentage}} %</td>
                <td>{{$discount->start_date}}</td>
                <td>{{$discount->end_date}}</td>
                <td>{{$discount->eligibility}}</td>
                <td>{{$discount->created_at}}</td>
                <td>{{$discount->description}}</td>
                <td>
                    @php
                        $endDate = \Carbon\Carbon::parse($discount->end_date);
                        $isExpired = $endDate->isPast();
                    @endphp
                    @if ($isExpired)
                    <span class="expired">Expired</span>
                    @else
                    <button type="button" class="btn viewbutton p-2 me-1" data-id="{{ $discount->id }}" data-bs-toggle="modal" data-bs-target="#discountModal-{{ $discount->id }}">Manage</button>
                    @endif
                </td>
                {{-- <td >
                    <a href="{{ route('discounts.edit', $discount->id) }}" class="btn viewbutton p-2 me-1 d-inline" style="font-family: 'Rubik', sans-serif;">Edit</a>
                    <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" style="display: inline-block;" >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="font-family: 'Rubik', sans-serif;" class="d-inline">Delete</button>
                    </form>
                </td> --}}
            </tr>

            <div class="modal fade" id="discountModal-{{ $discount->id }}" tabindex="-1" aria-labelledby="discountModalLabel-{{ $discount->id }}" aria-hidden="true" style="width: 100%;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fs-5" id="discountModalLabel-{{ $discount->id }}">View Discount</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="">Title: </label><span> {{ $discount->title }}</span><br>
                            <label for="">Discount For: </label><span> {{ $discount->item }}</span><br>
                            <label for="">Discount Percentage: </label><span> {{ $discount->discount_percentage }}%</span><br>
                            <label for="">Start Date: </label><span> {{ $discount->start_date}}</span><br>
                            <label for="">End Date: </label><span> {{ $discount->end_date}}</span><br>
                            <label for="">Eligibility: </label><span> {{ $discount->eligibility }}</span><br>
                            <label for="">Description: </label><span>{{ $discount->description }}</span><br>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('discounts.edit', $discount->id) }}" class="btn viewbutton mx-2" style="font-family: 'Rubik', sans-serif;">Edit</a>
                            <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" id="deletediscountform-{{$discount->id}}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger" style="font-family: 'Rubik', sans-serif;" data-id="{{ $discount->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal{{$discount->id}}" onclick="confirmDelete('{{ $discount->id }}')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @else
        <td colspan="9">No discounts created</td>
        <td class="hidetd"></td>
        <td class="hidetd"></td>
        <td class="hidetd"></td>
        <td class="hidetd"></td>
        <td class="hidetd"></td>
        <td class="hidetd"></td>
        <td class="hidetd"></td>
        <td class="hidetd"></td>
    @endif
    </tbody>
    </table>

    <div class="modal fade" id="confirmationModal{{$discount->id}}" tabindex="-1" aria-labelledby="confirmationModalLabel{{$discount->id}}" aria-hidden="true" style="width: 100%;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="confirmationModalLabel{{$discount->id}}">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove this discount?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDelete" style="font-family: 'Rubik', sans-serif;color:white;" onclick="deleteDiscount()">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="discountIdInput" value="">

    
    </div>
</div>

<script>
    function confirmDelete(discountId) {
            $('#discountIdInput').val(discountId);
            $('#confirmationModal').modal('show');
        }
    
        function deleteDiscount() {
            var discountId = $('#discountIdInput').val();
            $('#deletediscountform-' + discountId).submit();
        }
    $(document).ready(function() {
    $('#discounttable').DataTable({
        lengthMenu: [25],
        pageLength: 25,
        searching: false,
        lengthChange: false,
        "order": [[7, 'desc']],
                    "columnDefs": [
                { "visible": false, "targets": 7 }
            ],
        responsive: true
    });
    });
</script>
@endsection

{{-- <div class="container">
    <h1 class="mb-4 d-inline">Discounts</h1>
    <a href="{{ route('discounts.manage') }}" class="btn viewbutton mx-2 d-inline float-end" style="font-family: 'Rubik', sans-serif;">New</a>
    <div class="categorysection m-0 mt-3" style="background-color: #f1f1f1; border-radius:5px;">
    <!-- Search and Filter Section -->
    <div class="search-filter-container">
        <div class="search-bar">
            <label for="search">Search: </label>
            <input type="text" class="form-control" id="search" placeholder="Search for title or description">
        </div>
    </div>
        <div class="dropdown">
            <label for="discountFilter">Filter: </label>
            <select class="form-select" id="discountFilter">
                <option value="all">All</option>
                <option value="valid">Valid</option>
                <option value="expired">Expired</option>
            </select>
        </div>

    <!-- Discounts Container -->
    <div class="discounts-container" id="discounts-container">
        @forelse ($discounts as $discount)
            <div class="discount-box" data-toggle="modal" data-target="#discountModal-{{ $discount->id }}" data-title="{{ $discount->title }}" data-description="{{ $discount->description }}" data-start-date="{{ $discount->start_date }}" data-end-date="{{ $discount->end_date }}">
                <h3>{{ $discount->title }}</h3>
                <p><strong>Discount For: </strong>{{ $discount->item }}</p>
                <p><strong>Discounted: </strong>{{ $discount->discount_percentage }}%</p>
                <div class="discount-details">
                    <p><strong>Description: </strong>{!! $discount->description !!}</p>
                    <p><strong>Valid: </strong>{{ $discount->start_date }} - {{ $discount->end_date }}</p>
                </div>
            </div>
    
            <!-- Modal Structure -->
            <div class="modal fade" id="discountModal-{{ $discount->id }}" tabindex="-1" aria-labelledby="discountModalLabel-{{ $discount->id }}" aria-hidden="true" style="width: 100%;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fs-5" id="discountModalLabel-{{ $discount->id }}">View Discount</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="">Discount For: </label><span> {{ $discount->item }}</span><br>
                            <label for="">Discount Percentage: </label><span> {{ $discount->discount_percentage }}%</span><br>
                            <label for="">Start Date: </label><span> {{ $discount->start_date}}</span><br>
                            <label for="">End Date: </label><span> {{ $discount->end_date}}</span><br>
                            <label for="">Eligibility: </label><span> {{ $discount->eligibility }}</span><br>
                            <label for="">Description: </label><span>{!! $discount->description !!}</span><br>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('discounts.edit', $discount->id) }}" class="btn viewbutton mx-2" style="font-family: 'Rubik', sans-serif;">Edit</a>
                            <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="font-family: 'Rubik', sans-serif;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>No discounts found.</p>
        @endforelse
    </div>
    <br>
    <div class="pagination">
        {{ $discounts->links() }}
    </div>
    </div>
</div> --}}

{{-- <script>
    $(document).ready(function () {
    $('#search').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();

        $('.discount-box').each(function() {
            var card = $(this);
            var title = card.data('title').toLowerCase();
            var description = card.data('description').toLowerCase();

            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.show();
            } else {
                card.hide();
            }
        });
    });

    $('.discount-box').click(function () {
        var target = $(this).data('target');
        $(target).modal('show');
    });
});
</script> --}}
