@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="mb-4">📦 Product Intake</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('intake.store') }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select class="form-select" id="supplier_id" name="supplier_id" required>
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="payment_type" class="form-label">Payment Type</label>
                    <select class="form-select" id="payment_type" name="payment_type" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="type" class="form-label">Transaction Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="intake" selected>Intake</option>
                        <option value="intake_loan">Loan</option>
                        <option value="intake_return">Return</option>
                    </select>
                </div>
            </div>

            <!-- Additional fields that will show/hide based on transaction type -->
            <div id="return-fields" class="row mb-3" style="display: none;">
                <div class="col-md-12">
                    <label for="return_reason" class="form-label">Return Reason</label>
                    <textarea class="form-control" name="return_reason" id="return_reason" rows="2"></textarea>
                </div>
            </div>

            <div id="loan-fields" class="row mb-3" style="display: none;">
                <div class="col-md-6">
                    <label for="loan_amount" class="form-label">Loan Amount (UZS)</label>
                    <input type="number" class="form-control" name="loan_amount" id="loan_amount" step="0.01">
                </div>
                 <div class="col-md-6">
                    <label for="loan_direction" class="form-label">Loan direction</label>
                    <select name="loan_direction" id="loan_direction">
                        <option value="given">Given</option>
                        <option value="taken">Taken</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" name="due_date" id="due_date">
                </div>
            </div>

            <div class="mb-3">
                <label for="note" class="form-label">Note</label>
                <textarea class="form-control" name="note" id="note" rows="2"></textarea>
            </div>

            <!-- Barcode scanner input with button -->
            <div class="mb-4 d-flex gap-2">
                <input type="text" id="barcode" class="form-control" placeholder="Scan or enter barcode..."
                    autocomplete="off" autofocus>
                <button type="button" class="btn btn-success" id="scan-button">Scan</button>
            </div>

            <h4 class="mb-3">🧾 Product List</h4>

            <div id="products-container" class="mb-3">
                <div class="product-row row g-2 mb-2">
                    <div class="col-md-4">
                        <select class="form-select product-select" name="products[0][product_id]" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                    data-unit="{{ $product->unit }}" data-price-uzs="{{ $product->price_uzs }}"
                                    data-price-usd="{{ $product->price_usd }}"
                                    data-barcode="{{ $product->barcode_value }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex">
                        <button type="button" class="btn btn-outline-secondary qty-btn" data-action="decrease">-</button>
                        <input type="number" class="form-control qty" name="products[0][qty]" min="1" value="1"
                            required>
                        <button type="button" class="btn btn-outline-secondary qty-btn" data-action="increase">+</button>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control unit" name="products[0][unit]" readonly>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control price-uzs" name="products[0][price_uzs]" step="0.01"
                            required>
                    </div>
                    <div class="col-md-1">
                        <input type="number" class="form-control price-usd" name="products[0][price_usd]" step="0.01"
                            required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-product">×</button>
                    </div>
                </div>
            </div>

            <div class="mb-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="add-product">➕ Add Product</button>
                <div>
                    <strong>Total UZS:</strong> <span id="total-uzs">0</span> |
                    <strong>Total USD:</strong> <span id="total-usd">0</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">✅ Submit Intake</button>
        </form>
    </div>

    <script>
        // Convert the products collection to a proper JavaScript array
        const productsFromDb = @json($products->values());
        let rowIndex = 1;
        const audio = new Audio('{{ asset('admin/beep.mp3') }}');

        const barcodeInput = document.getElementById('barcode');
        const scanButton = document.getElementById('scan-button');
        const productsContainer = document.getElementById('products-container');
        const transactionType = document.getElementById('type');
        const returnFields = document.getElementById('return-fields');
        const loanFields = document.getElementById('loan-fields');

        // Show/hide additional fields based on transaction type
        transactionType.addEventListener('change', function() {
            const selectedType = this.value;
            
            // Hide all first
            returnFields.style.display = 'none';
            loanFields.style.display = 'none';
            
            // Show relevant fields
            if (selectedType === 'intake_return') {
                returnFields.style.display = 'block';
            } else if (selectedType === 'intake_loan') {
                loanFields.style.display = 'block';
            }
        });

        function recalculateTotals() {
            let uzs = 0,
                usd = 0;
            document.querySelectorAll('.product-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const qty = parseFloat(row.querySelector('.qty')?.value || 0);
                    const pUzs = parseFloat(row.querySelector('.price-uzs')?.value || 0);
                    const pUsd = parseFloat(row.querySelector('.price-usd')?.value || 0);
                    uzs += qty * pUzs;
                    usd += qty * pUsd;
                }
            });
            document.getElementById('total-uzs').textContent = uzs.toLocaleString();
            document.getElementById('total-usd').textContent = usd.toLocaleString();
        }

        function addProductRow(product = null) {
            // Create a new row from scratch instead of cloning
            const newRow = document.createElement('div');
            newRow.className = 'product-row row g-2 mb-2';
            newRow.innerHTML = `
            <div class="col-md-4">
                <select class="form-select product-select" name="products[${rowIndex}][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-unit="{{ $product->unit }}"
                                data-price-uzs="{{ $product->price_uzs }}"
                                data-price-usd="{{ $product->price_usd }}"
                                data-barcode="{{ $product->barcode_value }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex">
                <button type="button" class="btn btn-outline-secondary qty-btn" data-action="decrease">-</button>
                <input type="number" class="form-control qty" name="products[${rowIndex}][qty]" min="1" value="1" required>
                <button type="button" class="btn btn-outline-secondary qty-btn" data-action="increase">+</button>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control unit" name="products[${rowIndex}][unit]" readonly>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control price-uzs" name="products[${rowIndex}][price_uzs]" step="0.01" required>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control price-usd" name="products[${rowIndex}][price_usd]" step="0.01" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-product">×</button>
            </div>
        `;

            if (product) {
                const select = newRow.querySelector('.product-select');
                select.value = product.id;
                newRow.querySelector('.unit').value = product.unit;
                newRow.querySelector('.price-uzs').value = product.price_uzs;
                newRow.querySelector('.price-usd').value = product.price_usd;
            }

            productsContainer.appendChild(newRow);
            rowIndex++;
            recalculateTotals();
            audio.play();

            // Hide the default empty row if this is the first product being added
            const defaultRow = document.querySelector('.product-row[style*="display: none"]');
            if (!defaultRow && document.querySelectorAll('.product-row').length > 1) {
                productsContainer.querySelector('.product-row').style.display = 'none';
            }
        }

        // Add product row on button click
        document.getElementById('add-product').addEventListener('click', () => {
            addProductRow();
        });

        // Update fields when product selected from dropdown
        productsContainer.addEventListener('change', e => {
            if (e.target.classList.contains('product-select')) {
                const selected = e.target.options[e.target.selectedIndex];
                const row = e.target.closest('.product-row');

                row.querySelector('.unit').value = selected.dataset.unit;
                row.querySelector('.price-uzs').value = selected.dataset.priceUzs;
                row.querySelector('.price-usd').value = selected.dataset.priceUsd;
                audio.play();
                recalculateTotals();
            }
        });

        // Handle + / - qty buttons
        productsContainer.addEventListener('click', e => {
            if (e.target.classList.contains('qty-btn')) {
                const row = e.target.closest('.product-row');
                const qtyInput = row.querySelector('.qty');
                let qty = parseInt(qtyInput.value) || 0;

                if (e.target.dataset.action === 'increase') {
                    qty++;
                } else if (e.target.dataset.action === 'decrease' && qty > 1) {
                    qty--;
                }

                qtyInput.value = qty;
                recalculateTotals();
                audio.play();
            }

            // Remove product row
            if (e.target.classList.contains('remove-product')) {
                const rows = document.querySelectorAll('.product-row:not([style*="display: none"])');
                if (rows.length > 1) {
                    e.target.closest('.product-row').remove();
                    recalculateTotals();
                    audio.play();
                }
            }
        });

        // Barcode scan functionality
        function handleBarcodeScan() {
            const code = barcodeInput.value.trim();
            if (!code) return;

            // Make sure productsFromDb is an array
            if (!Array.isArray(productsFromDb)) {
                console.error('productsFromDb is not an array:', productsFromDb);
                alert("Error: Product data not loaded properly");
                return;
            }

            const product = productsFromDb.find(p => p.barcode_value === code);
            barcodeInput.value = '';
            barcodeInput.focus();

            if (!product) {
                alert("❌ Product not found for barcode: " + code);
                return;
            }

            // Check if product already exists in the list
            const existingRow = findProductRow(product.id);
            if (existingRow) {
                // Increment quantity if product exists
                const qtyInput = existingRow.querySelector('.qty');
                qtyInput.value = parseInt(qtyInput.value) + 1;
                audio.play();
                recalculateTotals();
            } else {
                // Add new row if product doesn't exist
                addProductRow(product);
            }
        }

        // Helper function to find existing product row
        function findProductRow(productId) {
            const rows = document.querySelectorAll('.product-row:not([style*="display: none"])');
            for (const row of rows) {
                const select = row.querySelector('.product-select');
                if (select && select.value == productId) {
                    return row;
                }
            }
            return null;
        }

        scanButton.addEventListener('click', handleBarcodeScan);

        barcodeInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleBarcodeScan();
            }
        });

        // Auto-focus barcode input on page load
        document.addEventListener('DOMContentLoaded', () => {
            barcodeInput.focus();
        });
    </script>
@endsection