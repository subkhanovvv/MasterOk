@extends('layouts.admin')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="card-title card-title-dash">Products</h4>
                </div>
                <div class="d-sm-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div>
                            <input type="text" id="barcodeInput" class="form-control" placeholder="Сканируйте штрихкод..."
                                autofocus />

                        </div>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle text-dark" type="button" id="filterDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-filter-outline"></i> Фильтр
                            </button>
                            <div class="dropdown-menu p-3 shadow" style="min-width:300px;" aria-labelledby="filterDropdown">
                                <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                                    <div class="mb-2">
                                        <input type="text" name="name" class="form-control" placeholder="Название"
                                            value="{{ request('name') }}">
                                    </div>
                                    <div class="mb-2">
                                        <select name="category_id" class="form-select">
                                            <option value="">Все категории</option>
                                            @foreach ($categories as $c)
                                                <option value="{{ $c->id }}"
                                                    {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <select name="brand_id" class="form-select">
                                            <option value="">Все бренды</option>
                                            @foreach ($brands as $b)
                                                <option value="{{ $b->id }}"
                                                    {{ request('brand_id') == $b->id ? 'selected' : '' }}>
                                                    {{ $b->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <select name="status" class="form-select">
                                            <option value="">Все статусы</option>
                                            <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>В
                                                наличии</option>
                                            <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Мало
                                            </option>
                                            <option value="out_of_stock"
                                                {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Нет в наличии
                                            </option>
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-filter"></i> Применить
                                        </button>
                                        <a href="{{ route('products.index') }}"
                                            class="btn btn-sm btn-outline-secondary">Сброс</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProductModal"
                            type="button">
                            <i class="mdi mdi-plus"></i> Add new
                        </button>
                    </div>
                </div>
            </div>

            <!-- Add a loading indicator -->
            <div id="loadingIndicator" class="text-center" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Wrap the table in a div for AJAX updates -->
            <div id="productsTableContainer">
                @include('pages.products.partials.products_table', ['products' => $products])
            </div>
        </div>
    </div>

    @include('pages.products.modals.new-product')
    @include('pages.products.modals.edit-product')
    @include('pages.products.modals.view-product')
    @include('pages.products.modals.consume-product')
    @include('pages.products.modals.intake-product')
    @include('pages.products.modals.delete-product')

    <script>
        // Your existing modal functions remain the same
        var unitPrice = 0;
        var quantity = 1;
        var currentModalType = 'consume';

        function openModal(element) {
            var id = element.getAttribute('data-id');
            var photo = element.getAttribute('data-photo');
            var name = element.getAttribute('data-name');
            var salePrice = element.getAttribute('data-sale_price')?.replace(/\s/g, '') || 0;
            unitPrice = parseFloat(salePrice);
            quantity = 1;
            const modalId = element.getAttribute('data-bs-target');

            if (modalId === '#consumeProductModal') {
                currentModalType = 'consume';
                document.getElementById('consume_product_id').value = id;
                document.getElementById('consume_product_photo').src = photo;
                document.getElementById('consume_product_name').textContent = name;
                document.getElementById('consume_product_sale_price').textContent = 'Цена за единицу: ' + unitPrice
                    .toLocaleString() + ' сум';
                document.getElementById('consume_qty').value = quantity;
            } else if (modalId === '#intakeProductModal') {
                currentModalType = 'intake';
                document.getElementById('intake_product_id').value = id;
                document.getElementById('intake_product_photo').src = photo;
                document.getElementById('intake_product_name').textContent = name;
                document.getElementById('intake_product_sale_price').textContent = 'Цена за единицу: ' + unitPrice
                    .toLocaleString() + ' сум';
                document.getElementById('intake_qty').value = quantity;
            } else if (modalId === '#editProductModal') {
                currentModalType = 'edit';
                document.getElementById('edit_product_id').value = id;
                document.getElementById('edit_product_name').value = name;
                document.getElementById('edit_product_description').value = element.getAttribute(
                    'data-short_description') || '';
                document.getElementById('edit_product_price').value = salePrice;
                document.getElementById('edit_product_photo').src = photo;
                document.getElementById('editProductForm').action = `/products/${id}`;
            } else if (modalId === '#deleteProductModal') {
                document.getElementById('delete-product-form').action = `/products/${id}`;
            }

            updateTotal();
            onTransactionTypeChange();
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
            if (currentModalType === 'edit') return; // Skip total update for edit modal

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
        document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const barcode = this.value.trim();
                if (!barcode) return;
                console.log('Barcode entered:', barcode);

                // Send AJAX request to find product by barcode
                $.get(`/products/by-barcode/${barcode}`, function(product) {
                    if (!product || !product.id) {
                        alert('Товар с таким штрихкодом не найден');
                        return;
                    }

                    // Create a temporary invisible element with data- attributes
                    const temp = document.createElement('div');
                    temp.setAttribute('data-id', product.id);
                    temp.setAttribute('data-name', product.name);
                    temp.setAttribute('data-photo', product.photo_url);
                    temp.setAttribute('data-sale_price', product.sale_price);
                    temp.setAttribute('data-bs-target', '#consumeProductModal');

                    // Call your existing openModal
                    openModal(temp);

                    // Show modal manually (Bootstrap)
                    const modal = new bootstrap.Modal(document.getElementById('consumeProductModal'));
                    modal.show();
                }).fail((jqXHR, textStatus, errorThrown) => {
                    console.error('AJAX error:', textStatus, errorThrown);
                    alert('Ошибка при получении товара');
                });

                this.value = ''; // Clear input
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
