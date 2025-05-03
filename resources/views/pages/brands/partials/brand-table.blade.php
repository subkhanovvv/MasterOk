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
            @forelse ($brands as $brand)
                <tr>
                    <td>{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>
                        <img
                            src="{{ $brand->photo ? Storage::url($brand->photo) : asset('admin/assets/images/default_product.png') }}">
                    </td>
                    <td>
                        <a href="{{ route('products.index', array_merge(request()->except('page'), ['brand_id' => $brand->id])) }}"
                            class="text-decoration-none text-dark" title="Товары в категории">
                            {{ $brand->products_count }} товаров</a>
                    </td>
                    <td>
                        <a href="javascript:void(0);" title="Просмотреть" data-bs-toggle="modal"
                            data-bs-target="#viewBrandModal" data-id="{{ $brand->id }}"
                            data-name="{{ $brand->name }}" data-description="{{ $brand->description }}"
                            data-phone="{{ $brand->phone }}"
                            data-intake="{{ $brand->last_intake ? $brand->last_intake->format('d.m.Y H:i') : '-' }}"
                            data-product="{{ $brand->products_count }}"
                            data-photo="{{ $brand->photo ? Storage::url($brand->photo) : asset('admin/assets/images/default_product.png') }}"
                            onclick="openModal(this)" class="text-decoration-none">
                            <i class="mdi mdi-eye icon-sm text-success"></i>
                        </a>

                        <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                            data-bs-target="#editBrandModal" data-id="{{ $brand->id }}"
                            data-name="{{ $brand->name }}" data-description="{{ $brand->description }}"
                            data-phone="{{ $brand->phone }}"
                            data-photo="{{ $brand->photo ? Storage::url($brand->photo) : asset('admin/assets/images/default_product.png') }}"
                            onclick="openModal(this)" class="text-decoration-none">
                            <i class="mdi mdi-pencil icon-sm text-primary"></i>
                        </a>
                        <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                            data-bs-target="#deleteBrandModal" data-id="{{ $brand->id }}" onclick="openModal(this)"
                            class="text-decoration-none">
                            <i class="mdi mdi-delete icon-sm text-danger"></i>
                        </a>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        Нет брендов
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
