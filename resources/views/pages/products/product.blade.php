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
                            <form action="{{ route('products.index') }}" method="GET">
                                @csrf
                                <input type="text" class="form-control rounded" name="search" id="searchInput"
                                       placeholder="Поиск..."  value="{{ request('search') }}" autofocus
                                       style="height:45px; width:300px ; border:2px solid black" />
                            </form>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle text-dark" type="button" id="filterDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-filter-outline"></i> Фильтр
                            </button>
                            <div class="dropdown-menu p-3 shadow" style="min-width:300px;" aria-labelledby="filterDropdown">
                                <form method="GET" action="{{ route('products.index') }}" id="filterForm">
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
                                            <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>В наличии</option>
                                            <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Мало</option>
                                            <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Нет в наличии</option>
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-filter"></i> Применить
                                        </button>
                                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Сброс</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProductModal" type="button">
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
    @include('pages.products.modals.intake-product')
    @include('pages.products.modals.delete-product')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('searchInput');
            if (input) {
                // Focus the input
                input.focus();
                
                // Move cursor to the end
                const length = input.value.length;
                input.setSelectionRange(length, length);
            }
        });
    </script>

    <script>
        // Global variables
        var unitPrice = 0;
        var quantity = 1;
        var currentModalType = 'consume';
        var productCounter = 1; // Counter for additional products
        var selectedProducts = []; // Array to store selected products

        // Payment type change handler
        document.getElementById('consume_payment_type').addEventListener('change', function() {
            const mixedPaymentContainer = document.getElementById('mixedPaymentContainer');
            if (this.value === 'mixed') {
                mixedPaymentContainer.style.display = 'block';
            } else {
                mixedPaymentContainer.style.display = 'none';
            }
        });

        // Add product button click handler
        document.getElementById('addProductBtn').addEventListener('click', function() {
            const searchTerm = document.getElementById('addProductInput').value.trim();
            if (!searchTerm) return;

            // Here you would typically make an AJAX call to search for products
            // For this example, we'll simulate finding a product
            simulateProductSearch(searchTerm);
        });

        // Simulate product search (replace with actual AJAX call)
        function simulateProductSearch(searchTerm) {
            // This is a simulation - replace with actual API call
            console.log('Searching for product:', searchTerm);

            // Mock product data - in real app, this would come from your backend
            const mockProduct = {
                id: 100 + productCounter,
                name: 'Товар ' + searchTerm,
                photo_url: 'https://via.placeholder.com/250',
                sale_price: (1000 * productCounter).toLocaleString()
            };

            addProductToForm(mockProduct);
        }

        // Add product to the form
        function addProductToForm(product) {
            const productIndex = productCounter++;

            // Add to selected products array
            selectedProducts.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.sale_price.replace(/\s/g, '')),
                quantity: 1
            });

            // Create form inputs for the new product
            const form = document.getElementById('consumeForm');

            // Create hidden inputs for the product
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.name = `products[${productIndex}][product_id]`;
            productIdInput.value = product.id;
            form.appendChild(productIdInput);

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `products[${productIndex}][quantity]`;
            quantityInput.value = 1;
            form.appendChild(quantityInput);

            const priceInput = document.createElement('input');
            priceInput.type = 'hidden';
            priceInput.name = `products[${productIndex}][total_price]`;
            priceInput.value = product.sale_price.replace(/\s/g, '');
            form.appendChild(priceInput);

            // Create a visible product card in the selected products list
            const productCard = document.createElement('div');
            productCard.className = 'card mb-2';
            productCard.innerHTML = `
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="${product.photo_url}" alt="${product.name}" style="width: 50px; height: 50px;" class="rounded me-2">
                            <div>
                                <h6 class="mb-0">${product.name}</h6>
                                <small class="text-muted">${product.sale_price} сум × 1</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="adjustProductQuantity(${product.id}, -1)">
                                <i class="mdi mdi-minus"></i>
                            </button>
                            <span id="productQty_${product.id}">1</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="adjustProductQuantity(${product.id}, 1)">
                                <i class="mdi mdi-plus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeProduct(${product.id})">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('selectedProductsList').appendChild(productCard);

            // Clear search input
            document.getElementById('addProductInput').value = '';

            // Update grand total
            updateGrandTotal();
        }

        // Adjust product quantity
        function adjustProductQuantity(productId, change) {
            const product = selectedProducts.find(p => p.id === productId);
            if (!product) return;

            const newQuantity = product.quantity + change;
            if (newQuantity < 1) return;

            product.quantity = newQuantity;
            document.getElementById(`productQty_${productId}`).textContent = newQuantity;

            // Update the hidden input value
            const inputs = document.querySelectorAll(`input[name^="products"][name$="product_id]`);
            for (let input of inputs) {
                if (parseInt(input.value) === productId) {
                    const index = input.name.match(/\[(\d+)\]/)[1];
                    document.querySelector(`input[name="products[${index}][quantity]"]`).value = newQuantity;
                    document.querySelector(`input[name="products[${index}][total_price]"]`).value = (product.price *
                        newQuantity).toFixed(2);
                    break;
                }
            }

            updateGrandTotal();
        }

        // Remove product
        function removeProduct(productId) {
            selectedProducts = selectedProducts.filter(p => p.id !== productId);

            // Remove the product card
            const cards = document.querySelectorAll('#selectedProductsList .card');
            for (let card of cards) {
                if (card.querySelector('button[onclick*="' + productId + '"]')) {
                    card.remove();
                    break;
                }
            }

            // Remove the hidden inputs
            const inputs = document.querySelectorAll(`input[name^="products"][name$="product_id]`);
            for (let input of inputs) {
                if (parseInt(input.value) === productId) {
                    const index = input.name.match(/\[(\d+)\]/)[1];
                    document.querySelector(`input[name="products[${index}][quantity]"]`).remove();
                    document.querySelector(`input[name="products[${index}][total_price]"]`).remove();
                    input.remove();
                    break;
                }
            }

            updateGrandTotal();
        }

        // Update grand total
        function updateGrandTotal() {
            let grandTotal = 0;

            // Calculate total from the main product
            const mainProductQty = parseInt(document.getElementById('consume_qty').value) || 0;
            grandTotal += unitPrice * mainProductQty;

            // Calculate total from additional products
            for (let product of selectedProducts) {
                grandTotal += product.price * product.quantity;
            }

            document.getElementById('consume_grand_total').textContent = grandTotal.toLocaleString();
        }

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

                // Clear previously selected products
                selectedProducts = [];
                productCounter = 1;
                document.getElementById('selectedProductsList').innerHTML = '';
                document.getElementById('addProductInput').value = '';
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
            updateGrandTotal();
            onTransactionTypeChange();
        }

        function increaseQty() {
            var qtyInputId = currentModalType === 'consume' ? 'consume_qty' : 'intake_qty';
            var qtyInput = document.getElementById(qtyInputId);
            var currentQty = parseInt(qtyInput.value);
            if (!isNaN(currentQty)) {
                qtyInput.value = currentQty + 1;
                updateTotal();
                updateGrandTotal();
            }
        }

        function decreaseQty() {
            var qtyInputId = currentModalType === 'consume' ? 'consume_qty' : 'intake_qty';
            var qtyInput = document.getElementById(qtyInputId);
            var currentQty = parseInt(qtyInput.value);
            if (!isNaN(currentQty) && currentQty > 1) {
                qtyInput.value = currentQty - 1;
                updateTotal();
                updateGrandTotal();
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
                var clientNameGroup = document.getElementById('consume_client_name_group');
                var clientPhoneInput = document.getElementById('consume_client_phone');
                var clientNameInput = document.getElementById('consume_client_name');
                var returnReasonGroup = document.getElementById('consume_return_reason_group');
                var returnReasonInput = document.getElementById('consume_return_reason');

                if (type === 'loan') {
                    clientPhoneGroup.style.display = 'block';
                    clientNameGroup.style.display = 'block';
                    setTimeout(() => clientPhoneInput.focus(), 100);
                    setTimeout(() => clientNameInput.focus(), 100);
                } else {
                    clientPhoneGroup.style.display = 'none';
                    clientPhoneInput.value = '';
                    clientNameGroup.style.display = 'none';
                    clientNameInput.value = '';
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

        // // Barcode scanner functionality
        // document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
        //     if (e.key === 'Enter') {
        //         e.preventDefault();
        //         const barcode = this.value.trim();
        //         if (!barcode) return;
        //         console.log('Barcode entered:', barcode);

        //         // Send AJAX request to find product by barcode
        //         $.get(`/products/by-barcode/${barcode}`, function(product) {
        //             if (!product || !product.id) {
        //                 alert('Товар с таким штрихкодом не найден');
        //                 return;
        //             }

        //             // Create a temporary invisible element with data- attributes
        //             const temp = document.createElement('div');
        //             temp.setAttribute('data-id', product.id);
        //             temp.setAttribute('data-name', product.name);
        //             temp.setAttribute('data-photo', product.photo_url);
        //             temp.setAttribute('data-sale_price', product.sale_price);
        //             temp.setAttribute('data-bs-target', '#consumeProductModal');

        //             // Call your existing openModal
        //             openModal(temp);

        //             // Show modal manually (Bootstrap)
        //             const modal = new bootstrap.Modal(document.getElementById('consumeProductModal'));
        //             modal.show();
        //         }).fail((jqXHR, textStatus, errorThrown) => {
        //             console.error('AJAX error:', textStatus, errorThrown);
        //             alert('Ошибка при получении товара');
        //         });

        //         this.value = ''; // Clear input
        //     }
        // });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
