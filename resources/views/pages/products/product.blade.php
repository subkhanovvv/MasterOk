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
                                <td>{{ number_format($p->sale_price) }}</td>
                                <td>{{ $p->qty }} {{ $p->unit }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="javascript:void(0);" title="Расход товара" data-bs-toggle="modal"
                                            data-bs-target="#consumeProductModal" data-id="{{ $p->id }}"
                                            data-photo="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                                            data-name="{{ $p->name }}" data-sale_price="{{ $p->sale_price }}"
                                            data-unit="{{ $p->unit }}" onclick="openModal(this)">
                                            <i class="mdi mdi-database-minus icon-sm text-primary"></i>
                                        </a>


                                        <a href="javascript:void(0);" title="Приход товара" data-bs-toggle="modal"
                                            data-bs-target="#intakeProductModal" data-id="{{ $p->id }}"
                                            data-photo="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                                            data-name="{{ $p->name }}" data-sale_price="{{ $p->sale_price }}"
                                            data-unit="{{ $p->unit }}" onclick="openModal(this)">
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

    <script>
        var unitPrice = 0;
        var quantity = 1;

        function openModal(element) {
            var id = element.getAttribute('data-id');
            var photo = element.getAttribute('data-photo');
            var name = element.getAttribute('data-name');
            var salePrice = element.getAttribute('data-sale_price').replace(/\s/g, '');

            unitPrice = parseFloat(salePrice);
            quantity = 1;

            const modalId = element.getAttribute('data-bs-target');

            if (modalId === '#consumeProductModal') {
                document.getElementById('consume_product_id').value = id;
                document.getElementById('consume_product_photo').src = photo;
                document.getElementById('consume_product_name').textContent = name;
                document.getElementById('consume_product_sale_price').textContent = 'Цена за единицу: ' + Number(unitPrice)
                    .toLocaleString() + ' сум';
                document.getElementById('consume_qty').value = quantity;
            } else if (modalId === '#intakeProductModal') {
                document.getElementById('intake_product_id').value = id;
                document.getElementById('intake_product_photo').src = photo;
                document.getElementById('intake_product_name').textContent = name;
                document.getElementById('intake_product_sale_price').textContent = 'Цена за единицу: ' + Number(unitPrice)
                    .toLocaleString() + ' сум';
                document.getElementById('intake_qty').value = quantity;
            }

            updateTotal(modalId); // Pass modalId
            onTransactionTypeChange(modalId === '#consumeProductModal' ? 'consume' : 'intake');
        }


        function increaseQty() {
            var currentQty = parseInt(document.getElementById('qty').value);
            if (!isNaN(currentQty)) {
                currentQty++;
                document.getElementById('qty').value = currentQty;
                updateTotal();
            }
        }

        function decreaseQty() {
            var currentQty = parseInt(document.getElementById('qty').value);
            if (!isNaN(currentQty) && currentQty > 1) {
                currentQty--;
                document.getElementById('qty').value = currentQty;
                updateTotal();
            }
        }

        function updateTotal(modalId) {
    var qtyId = modalId === '#consumeProductModal' ? 'consume_qty' : 'intake_qty';
    var totalPriceId = modalId === '#consumeProductModal' ? 'consume_total_price' : 'intake_total_price';
    var hiddenTotalPriceId = modalId === '#consumeProductModal' ? 'consume_hidden_total_price' : 'intake_hidden_total_price';

    var quantity = parseInt(document.getElementById(qtyId).value);
    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
        document.getElementById(qtyId).value = quantity;
    }

    var total = unitPrice * quantity;

    document.getElementById(totalPriceId).textContent = total.toLocaleString();
    document.getElementById(hiddenTotalPriceId).value = total;
}


        function onTransactionTypeChange(modalType) {
            let typeSelectId = modalType === 'consume' ? 'transaction_type_consume' : 'transaction_type';
            let returnReasonGroup = document.getElementById(modalType === 'consume' ? 'return_reason_group_consume' :
                'return_reason_group');
            let returnReasonInput = document.getElementById(modalType === 'consume' ? 'return_reason_consume' :
                'return_reason');

            let clientPhoneGroup = modalType === 'consume' ? document.getElementById('client_phone_group_consume') : null;
            let clientPhoneInput = modalType === 'consume' ? document.getElementById('client_phone_consume') : null;

            const selectElement = document.getElementById(typeSelectId);
            if (!selectElement) return; // Safe-check: if element not exist

            const type = selectElement.value;

            // Handle client phone (only in consume modal)
            if (modalType === 'consume' && clientPhoneGroup && clientPhoneInput) {
                if (type === 'loan') {
                    clientPhoneGroup.style.display = 'block';
                    setTimeout(() => clientPhoneInput.focus(), 100);
                } else {
                    clientPhoneGroup.style.display = 'none';
                    clientPhoneInput.value = '';
                }
            }

            // Handle return reason
            if (returnReasonGroup && returnReasonInput) {
                if (type === 'return' || type === 'intake_return') {
                    returnReasonGroup.style.display = 'block';
                    setTimeout(() => returnReasonInput.focus(), 100);
                } else {
                    returnReasonGroup.style.display = 'none';
                    returnReasonInput.value = '';
                }
            }
        }
    </script>





    @include('pages.products.modals.new-product')
    @include('pages.products.modals.edit-product')
    @include('pages.products.modals.view-product')
    @include('pages.products.modals.consume-product')
    @include('pages.products.modals.intake-product')
    @include('pages.products.modals.delete-product')
@endsection
