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
                        data-bs-target="#newProductModal" type="button"><i class="mdi mdi-plus"></i>Add new</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Название</th>
                            <th>photo</th>
                            <th>Цена (UZS)</th>
                            <th>Цена (USD)</th>
                            <th>Бренд</th>
                            <th>Статус</th>
                            <th>Цена</th>
                            <th>Склад</th>
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
                                <td>{{ number_format($p->price_uzs) }} uzs</td>
                                <td>$ {{ $p->price_usd }}</td>
                                <td>{{ $p->get_brand->name }}</td>
                                <td>
                                    @php
                                        $color =
                                            $p->status === 'normal'
                                                ? 'success'
                                                : ($p->status === 'low'
                                                    ? 'warning'
                                                    : 'danger');

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
                                <td>{{ $p->sale_price }}</td>
                                <td>{{ $p->qty }} {{ $p->unit }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="javascript:void(0);" title="Расход товара" data-bs-toggle="modal"
                                            data-bs-target="#consumeProductModal">
                                            <i class="mdi mdi-database-minus icon-sm text-primary"></i>
                                        </a>
                                        <a href="javascript:void(0);" title="Приход товара" data-bs-toggle="modal"
                                            data-bs-target="#intakeProductModal">
                                            <i class="mdi mdi-database-plus icon-sm text-success"></i>
                                        </a>
                                        <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                                            data-bs-target="#editProductModal">
                                            <i class="mdi mdi-pencil icon-sm text-primary"></i>
                                        </a>
                                        <a href="javascript:void(0);" title="Удалить" data-bs-toggle="modal"
                                            data-bs-target="#deleteProductModal">
                                            <i class="mdi mdi-delete icon-sm text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <div class="pagination mb-0">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
                <p class="text-muted mb-0">
                    Показаны с {{ $products->firstItem() }} по {{ $products->lastItem() }} из
                    {{ $products->total() }} результатов
                </p>
            </div>
        </div>
    </div>

    @include('pages.products.modals.new-product')
    @include('pages.products.modals.edit-product')
    @include('pages.products.modals.view-product')
    @include('pages.products.modals.consume-product')
    @include('pages.products.modals.intake-product')
    @include('pages.products.modals.delete-product')
@endsection
