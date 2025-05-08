<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>photo</th>
                <th>Цена (UZS/USD)</th>
                <th>Статус</th>
                <th>Цена</th>
                <th>Склад</th>
                <th>Штрих-код</th>
                <th>Действие</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->name }}</td>
                    <td>
                        <img src="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                            alt="{{ $p->name }}">
                    </td>
                    <td>{{ number_format($p->price_uzs) }} sum / ${{ $p->price_usd }}</td>
                    <td>
                        @php
                            $color =
                                $p->status === 'normal' ? 'success' : ($p->status === 'low' ? 'warning' : 'danger');

                            $statusRu = match ($p->status) {
                                'normal' => 'В наличии',
                                'low' => 'Мало',
                                'out_of_stock' => 'Нет в наличии',
                                default => $p->status,
                            };
                        @endphp
                        <span class="badge badge-{{ $color }}">
                            {{ $statusRu }}
                        </span>
                    </td>
                    <td>{{ number_format($p->sale_price) }}</td>
                    <td>{{ $p->qty }} {{ $p->unit }}</td>
                    <td>
                        @if ($p->barcode)
                            <p>{{ $p->barcode_value }}</p>
                        @else
                            <p>No barcode</p>
                        @endif
                    </td>
                    <td>
                        <div>
                            <a href="javascript:void(0);" title="Расход товара" data-bs-toggle="modal"
                                data-bs-target="#consumeProductModal" data-id="{{ $p->id }}"
                                data-photo="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                                data-name="{{ $p->name }}" data-sale_price="{{ $p->sale_price }}"
                                data-unit="{{ $p->unit }}" onclick="openModal(this)" class="text-decoration-none">
                                <i class="mdi mdi-eye icon-sm text-success"></i>
                            </a>
                            <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                                data-bs-target="#editProductModal" data-id="{{ $p->id }}"
                                data-name="{{ $p->name }}" data-short_description="{{ $p->short_description }}"
                                data-sale_price="{{ $p->sale_price }}"
                                data-photo="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                                onclick="openModal(this)" class="text-decoration-none">
                                <i class="mdi mdi-pencil icon-sm text-primary"></i>
                            </a>

                            <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                                data-bs-target="#deleteProductModal" data-id="{{ $p->id }}"
                                onclick="openModal(this)" class="text-decoration-none">
                                <i class="mdi mdi-delete icon-sm text-danger"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if ($products->hasPages())
    <div class="mt-3 d-flex justify-content-between align-items-center mb-3">
        <div class="pagination">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        <p class="text-muted">
            Показаны с {{ $products->firstItem() }} по {{ $products->lastItem() }} из
            {{ $products->total() }} результатов
        </p>
    </div>
@endif
