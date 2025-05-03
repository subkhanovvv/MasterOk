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
            @forelse ($categories as $c)
                <tr>
                    <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                    <td>{{ $c->name }}</td>
                    <td>
                        <img
                            src="{{ $c->photo ? Storage::url($c->photo) : asset('admin/assets/images/default_product.png') }}">
                    </td>
                    <td>
                        <a href="{{ route('products.index', array_merge(request()->except('page'), ['category_id' => $c->id])) }}"
                            class="text-decoration-none text-dark" title="Товары в категории">
                            {{ $c->products_count }} товаров</a>
                    </td>
                    <td>
                        <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                            data-bs-target="#editCategoryModal" data-id="{{ $c->id }}"
                            data-name="{{ $c->name }}"
                            data-photo="{{ $c->photo ? Storage::url($c->photo) : asset('admin/assets/images/default_product.png') }}"
                            onclick="openModal(this)" class="text-decoration-none">
                            <i class="mdi mdi-pencil icon-sm text-primary"></i>
                        </a>
                        <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                            data-bs-target="#deleteCategoryModal" data-id="{{ $c->id }}"
                            onclick="openModal(this)" class="text-decoration-none">
                            <i class="mdi mdi-delete icon-sm text-danger"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Нет категории</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
