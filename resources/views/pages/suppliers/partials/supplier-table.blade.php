<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>info</th>
                <th>brand</th>
                <th>Действие</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $s)
                <tr>
                    <td>{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->note }}</td>
                    <td>
                        <a href="javascript:void(0);" title="Просмотреть" data-bs-toggle="modal"
                            data-bs-target="#viewBrandModal" data-brand-id="{{ $s->brand->id }}"
                            data-brand-name="{{ $s->brand->name }}" data-brand-description="{{ $s->brand->description }}"
                            data-brand-phone="{{ $s->brand->phone }}"
                            data-brand-intake="@if ($s->brand->last_intake) {{ $s->brand->last_intake->format('d.m.Y H:i') }}@else — @endif"
                            data-brand-product="{{ $s->brand->products_count }}"
                            data-brand-photo="{{ $s->brand->photo ? Storage::url($s->brand->photo) : asset('admin/assets/images/default_product.png') }}"
                            onclick="openModal(this)" class="text-decoration-none text-dark">
                            {{ $s->brand->name }}
                        </a>
                    </td>

                    <td>
                        <a href="javascript:void(0);" title="Просмотреть" data-bs-toggle="modal"
                            data-bs-target="#viewSupplierModal" data-id="{{ $s->id }}"
                            data-brand-photo="{{ $s->brand->photo ? Storage::url($s->brand->photo) : asset('admin/assets/images/default_product.png') }}"
                            data-brand-phone="{{ $s->brand->phone }}" data-brand-name="{{ $s->brand->name }}"
                            data-name="{{ $s->name }}" data-note="{{ $s->note }}" onclick="openModal(this)"
                            class="text-decoration-none">
                            <i class="mdi mdi-eye icon-sm text-success"></i>
                        </a>
                        <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                            data-bs-target="#editSupplierModal" data-id="{{ $s->id }}"
                            data-name="{{ $s->name }}" data-photo="{{ $s->note }}" onclick="openModal(this)"
                            class="text-decoration-none">
                            <i class="mdi mdi-pencil icon-sm text-primary"></i>
                        </a>
                        <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                            data-bs-target="#deleteSupplierModal" data-id="{{ $s->id }}"
                            onclick="openModal(this)" class="text-decoration-none">
                            <i class="mdi mdi-delete icon-sm text-danger"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Нет suppliers</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
