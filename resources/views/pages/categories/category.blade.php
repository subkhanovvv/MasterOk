@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-start">
                <div>
                    <h4 class="card-title card-title-dash">Категории</h4>
                </div>
                <div>
                    <button class="btn btn-primary text-white mb-0 me-0" data-bs-toggle="modal"
                        data-bs-target="#newCategoryModal" type="button"><i class="mdi mdi-plus"></i>Новый</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Название</th>
                            <th>Фото</th>
                            <th>Склад</th>
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
                                    {{ $c->products_count }} товаров
                                </td>
                                <td>
                                    <a href="#" class="text-decoration-none">
                                        <i class="mdi mdi-eye icon-sm text-success"></i>
                                    </a>
                                    <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal" data-id="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-photo="{{ $c->photo ? Storage::url($c->photo) : asset('admin/assets/images/default_product.png') }}"
                                        onclick="openModal(this)">
                                        <i class="mdi mdi-pencil icon-sm text-primary"></i>
                                    </a>
                                    <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                                        data-bs-target="#deleteCategoryModal" data-id="{{ $c->id }}"
                                        onclick="openModal(this)">
                                        <i class="mdi mdi-delete icon-sm text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
    <br>




    @include('pages.categories.modals.delete-category')
    @include('pages.categories.modals.edit-category')
    @include('pages.categories.modals.new-category')
    <script>
        function openModal(element) {
            var id = element.getAttribute('data-id');
            var photo = element.getAttribute('data-photo');
            var name = element.getAttribute('data-name');
            const modalId = element.getAttribute('data-bs-target');


            if (modalId === '#deleteCategoryModal') {
                document.getElementById('delete-category-form').action = `/categories/${id}`;
            } else if (modalId === '#editCategoryModal') {
                currentModalType = 'edit';
                document.getElementById('edit_category_id').value = id;
                document.getElementById('edit_category_name').value = name;
                document.getElementById('edit_category_photo').src = photo;
                document.getElementById('editCategoryForm').action = `/categories/${id}`;
            }
        }
    </script>
@endsection
