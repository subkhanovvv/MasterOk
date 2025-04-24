@extends('layouts.admin')

@section('content')
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title card-title-dash">Products</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-lg text-white mb-0 me-0" data-bs-toggle="modal"
                            data-bs-target="#newCategoryModal" type="button"><i class="mdi mdi-plus"></i>Add new</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>photo</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $c)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $c->name }}</td>
                                    <td>
                                        <img src="{{ $c->photo ? Storage::url($c->photo) : asset('admin/assets/images/default_product.png') }}"
                                            alt="{{ $c->name }}" class="image">
                                    </td>

                                    <td>
                                        <div class="list-icon-function d-flex justify-content-center gap-2">
                                            <a data-bs-toggle="modal" data-bs-target="#viewProductModal"
                                                data-product="{{ $c->toJson() }}" onclick="viewProductModal(this)"
                                                href="javascript:void(0)">
                                                <i class="mdi mdi-eye icon-sm text-warning"></i>
                                            </a>
                                            <a href="">
                                                <i class="mdi mdi-pencil icon-sm"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="deleteProduct" data-bs-toggle="modal"
                                                data-bs-target="#deleteProductModal" data-id="{{ $c->id }}"
                                                data-token="{{ csrf_token() }}">
                                                <i class="mdi mdi-delete icon-sm text-danger"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table><br>
                </div>
                @if ($categories->count())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="text-muted mb-0">
                            Показаны с {{ $categories->firstItem() }} по {{ $categories->lastItem() }} из
                            {{ $categories->total() }} результатов
                        </p>

                        <div class="pagination mb-0">
                            {{ $categories->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    {{-- </div> --}}
    <br>

    <div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить этот товар?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-danger text-white" id="confirmDeleteBtn">Удалить</button>
                </div>
            </div>
        </div>
    </div>
    @include('pages.categories.modals.new-category')
    @include('pages.categories.modals.edit-category')
    {{-- @include('pages.categories.modals.view-product') --}}
    {{-- @include('pages.products.modals.delete-product') --}}
    <script>
        // When the delete button is clicked
        $(".deleteProduct").click(function() {
            // Capture the product ID and CSRF token from the clicked link
            var productId = $(this).data("id");
            var token = $(this).data("token");

            // Set up the confirmation button click to actually delete the product
            $("#confirmDeleteBtn").off("click").on("click", function() {
                // Send the AJAX DELETE request to delete the product
                $.ajax({
                    url: "/destroy-product/" + productId, // Use the correct endpoint
                    type: 'DELETE',
                    dataType: "JSON",
                    data: {
                        "_token": token, // Send the CSRF token
                    },
                    success: function(response) {
                        // Close the modal
                        $("#deleteProductModal").modal('hide');

                        // Optionally, show a success message
                        alert(response.message || "Товар успешно удалён!");

                        // Remove the deleted row from the table (assuming you have a table with the corresponding ID)
                        $("button[data-id='" + productId + "']").closest("tr").remove();
                    },
                    error: function(xhr, status, error) {
                        alert("Ошибка при удалении товара");
                    }
                });
            });
        });
    </script>
@endsection
