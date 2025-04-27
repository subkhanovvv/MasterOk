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
                                            data-bs-target="#intakeProductModal" >
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
            var salePrice = element.getAttribute('data-sale_price').replace(/\s/g, ''); // Remove spaces

            unitPrice = parseFloat(salePrice);
            quantity = 1;

            document.getElementById('product_id').value = id;
            document.getElementById('product_photo').src = photo;
            document.getElementById('product_name').textContent = name;
            document.getElementById('product_sale_price').textContent = 'Цена за единицу: ' + Number(unitPrice)
                .toLocaleString() + ' сум';
            document.getElementById('qty').value = quantity;

            updateTotal();
            onTransactionTypeChange(); // reset fields
        }

        function increaseQty() {
            var currentQty = parseInt(document.getElementById('qty').value);
            if (!isNaN(currentQty)) {
                currentQty++; // Increase by 1
                document.getElementById('qty').value = currentQty; // Update input value
                updateTotal(); // Update total price
            }
        }

        // Function to decrease the quantity by 1
        function decreaseQty() {
            var currentQty = parseInt(document.getElementById('qty').value);
            if (!isNaN(currentQty) && currentQty > 1) {
                currentQty--; // Decrease by 1
                document.getElementById('qty').value = currentQty; // Update input value
                updateTotal(); // Update total price
            }
        }

        function updateTotal() {
            // Get the quantity from the input field
            var quantity = parseInt(document.getElementById('qty').value);

            // Validate the quantity to be a positive integer
            if (isNaN(quantity) || quantity < 1) {
                quantity = 1; // Default to 1 if invalid
                document.getElementById('qty').value = quantity;
            }

            // Calculate the total price
            var total = unitPrice * quantity;

            // Update the total price display
            document.getElementById('total_price').textContent = total.toLocaleString();

            // Update the hidden input field to send total_price with the form
            document.getElementById('hidden_total_price').value = total;
        }


        function onTransactionTypeChange() {
            const type = document.getElementById('transaction_type').value;
            const clientPhoneGroup = document.getElementById('client_phone_group');
            const returnReasonGroup = document.getElementById('return_reason_group');
            const clientPhoneInput = document.getElementById('client_phone');
            const returnReasonInput = document.getElementById('return_reason');

            if (type === 'loan') {
                clientPhoneGroup.style.display = 'block';
                setTimeout(() => {
                    clientPhoneInput.focus(); // auto focus phone
                }, 100);
            } else {
                clientPhoneGroup.style.display = 'none';
                clientPhoneInput.value = '';
            }

            if (type === 'return') {
                returnReasonGroup.style.display = 'block';
                setTimeout(() => {
                    returnReasonInput.focus(); // auto focus reason
                }, 100);
            } else {
                returnReasonGroup.style.display = 'none';
                returnReasonInput.value = '';
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
