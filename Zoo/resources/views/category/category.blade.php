
<div class="democategorylist" style="justify-items: center">
    <ul class="list-group">
        @if (count($demoCategories) > 0)
            @foreach ($demoCategories as $category)
                <li class="list-group-item">
                    <span>{{ $category->demoCategoryName }}</span>
                    {{-- <button type="button" onclick="deleteCategory('{{ $category->id }}')" style="background-color: red; border: none; border-radius: 10px; padding: 5px; padding: 2px 10px" class="float-end"><i class="fa-solid fa-xmark" style="color: white"></i></button> --}}
                    <button type="button" style="background-color: red; border: none; border-radius: 10px; padding: 5px; padding: 2px 10px" data-id="{{ $category->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal" class="float-end" onclick="confirmDelete('{{ $category->id }}')"><i class="fa-solid fa-xmark" style="color: white"></i></button>

                    <!-- Move the form inside the loop -->
                    <form id="delete-form-{{ $category->id }}"
                        action="{{ action('App\Http\Controllers\DemoCategoryController@destroy', $category->id) }}" method="post">
                        @csrf
                        @method('DELETE')
                    </form>
                </li>
            @endforeach
        @else
            <p>No category found</p>
        @endif
    </ul>
</div>

    <!-- Form to add a new category -->
    <form method="post" action="{{ route('categories.store') }}">
        @csrf
        <div class="input-group mb-2 mt-3">
            <input type="text" name="category_name" class="form-control" placeholder="New Category">
            <button class="btn viewbutton" type="submit" id="button-addon2" style="font-family: 'Rubik', sans-serif;">Add</button>
          </div>
    </form>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true" style="width: 100%;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="confirmationModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this category?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDelete" style="font-family: 'Rubik', sans-serif;color:white;" onclick="deleteCategory()">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="categoryIdInput" value="">

    <script>
        function confirmDelete(categoryId) {
            $('#categoryIdInput').val(categoryId); // Set category ID in hidden input
            $('#confirmationModal').modal('show'); // Show the modal
        }
    
        function deleteCategory() {
            var categoryId = $('#categoryIdInput').val(); // Get category ID from hidden input
            var form = $('#delete-form-' + categoryId); // Find the form with the corresponding ID
            form.submit(); // Submit the form
        }
    </script>