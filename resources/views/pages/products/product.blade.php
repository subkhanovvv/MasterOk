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
        var currentModalType = 'consume'; // Default

        function openModal(element) {
            var id = element.getAttribute('data-id');
            var photo = element.getAttribute('data-photo');
            var name = element.getAttribute('data-name');
            var salePrice = element.getAttribute('data-sale_price').replace(/\s/g, '');

            unitPrice = parseFloat(salePrice);
            quantity = 1;

            const modalId = element.getAttribute('data-bs-target');
            currentModalType = modalId === '#consumeProductModal' ? 'consume' : 'intake';

            if (currentModalType === 'consume') {
                document.getElementById('consume_product_id').value = id;
                document.getElementById('consume_product_photo').src = photo;
                document.getElementById('consume_product_name').textContent = name;
                document.getElementById('consume_product_sale_price').textContent = 'Цена за единицу: ' + unitPrice
                    .toLocaleString() + ' сум';
                document.getElementById('consume_qty').value = quantity;
            } else if (currentModalType === 'intake') {
                document.getElementById('intake_product_id').value = id;
                document.getElementById('intake_product_photo').src = photo;
                document.getElementById('intake_product_name').textContent = name;
                document.getElementById('intake_product_sale_price').textContent = 'Цена за единицу: ' + unitPrice
                    .toLocaleString() + ' сум';
                document.getElementById('intake_qty').value = quantity;
            }

            updateTotal();
            onTransactionTypeChange(); // handle default fields
        }

        function increaseQty() {
            var qtyInputId = currentModalType === 'consume' ? 'consume_qty' : 'intake_qty';
            var qtyInput = document.getElementById(qtyInputId);

            var currentQty = parseInt(qtyInput.value);
            if (!isNaN(currentQty)) {
                qtyInput.value = currentQty + 1;
                updateTotal();
            }
        }

        function decreaseQty() {
            var qtyInputId = currentModalType === 'consume' ? 'consume_qty' : 'intake_qty';
            var qtyInput = document.getElementById(qtyInputId);

            var currentQty = parseInt(qtyInput.value);
            if (!isNaN(currentQty) && currentQty > 1) {
                qtyInput.value = currentQty - 1;
                updateTotal();
            }
        }

        function updateTotal() {
            var qtyInputId = currentModalType === 'consume' ? 'consume_qty' : 'intake_qty';
            var totalPriceId = currentModalType === 'consume' ? 'consume_total_price' : 'intake_total_price';
            var hiddenTotalPriceId = currentModalType === 'consume' ? 'consume_hidden_total_price' :
                'intake_hidden_total_price';

            var quantity = parseInt(document.getElementById(qtyInputId).value);
            if (isNaN(quantity) || quantity < 1) {
                quantity = 1;
                document.getElementById(qtyInputId).value = quantity;
            }

            var total = unitPrice * quantity;

            document.getElementById(totalPriceId).textContent = total.toLocaleString();
            document.getElementById(hiddenTotalPriceId).value = total;
        }

        function onTransactionTypeChange() {
            var typeSelectId = currentModalType === 'consume' ? 'consume_transaction_type' : 'intake_transaction_type';
            var selectElement = document.getElementById(typeSelectId);

            if (!selectElement) return;

            var type = selectElement.value;

            if (currentModalType === 'consume') {
                var clientPhoneGroup = document.getElementById('consume_client_phone_group');
                var clientPhoneInput = document.getElementById('consume_client_phone');
                var returnReasonGroup = document.getElementById('consume_return_reason_group');
                var returnReasonInput = document.getElementById('consume_return_reason');

                if (type === 'loan') {
                    clientPhoneGroup.style.display = 'block';
                    setTimeout(() => clientPhoneInput.focus(), 100);
                } else {
                    clientPhoneGroup.style.display = 'none';
                    clientPhoneInput.value = '';
                }

                if (type === 'return') {
                    returnReasonGroup.style.display = 'block';
                    setTimeout(() => returnReasonInput.focus(), 100);
                } else {
                    returnReasonGroup.style.display = 'none';
                    returnReasonInput.value = '';
                }

            } else if (currentModalType === 'intake') {
                var returnReasonGroup = document.getElementById('intake_return_reason_group');
                var returnReasonInput = document.getElementById('intake_return_reason');

                if (type === 'intake_return') {
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
