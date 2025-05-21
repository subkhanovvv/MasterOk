@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="mb-4">📦 Product Intake</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="payment_type" class="form-label">Payment Type</label>
                    <select class="form-select" id="payment_type" name="payment_type" required>
                        <option value="cash" @selected(old('payment_type') == 'cash')>Cash</option>
                        <option value="card" @selected(old('payment_type') == 'card')>Card</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="type" class="form-label">Transaction Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="intake">Intake</option>
                        <option value="intake_loan">Loan</option>
                        <option value="intake_return">Return</option>
                    </select>
                </div>
            </div>

            <!-- Additional fields that will show/hide based on transaction type -->
            <div id="return-fields" class="row mb-3" style="display: none;">
                <div class="col-md-12">
                    <label for="return_reason" class="form-label">Return Reason</label>
                    <textarea class="form-control" name="return_reason" id="return_reason" rows="2">{{ old('return_reason') }}</textarea>
                </div>
            </div>

            <div id="loan-fields" class="row mb-3" style="display: none;">
                <div class="col-md-6">
                    <label for="loan_amount" class="form-label">Loan Amount (UZS)</label>
                    <input type="number" class="form-control" name="loan_amount" id="loan_amount" step="0.01"
                        value="{{ old('loan_amount') }}">
                </div>
                <div class="col-md-6">
                    <label for="loan_direction" class="form-label">Loan direction</label>
                    <select name="loan_direction" id="loan_direction" class="form-select">
                        <option value="given" @selected(old('loan_direction') == 'given')>Given</option>
                        <option value="taken" @selected(old('loan_direction') == 'taken')>Taken</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" name="due_date" id="due_date" value="{{ old('due_date') }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="note" class="form-label">Note</label>
                <textarea class="form-control" name="note" id="note" rows="2">{{ old('note') }}</textarea>
            </div>

            <!-- Barcode scanner input with button -->
            <div class="mb-4 d-flex gap-2">
                <input type="text" id="barcode" class="form-control" placeholder="Scan or enter barcode..."
                    autocomplete="off" autofocus>
                <button type="button" class="btn btn-success" id="scan-button">
                    <i class="fas fa-barcode"></i> Scan
                </button>
            </div>

            <h4 class="mb-3">🧾 Product List</h4>

            <div id="products-container" class="mb-3">
                <!-- Dynamic rows will be added here -->
            </div>

            <div class="mb-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="add-product">
                    <i class="fas fa-plus"></i> Add Product
                </button>
                <div>
                    <strong>Total UZS:</strong> <span id="total-uzs">0</span> |
                    <strong>Total USD:</strong> <span id="total-usd">0</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-check"></i> Submit Intake
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsFromDb = @json($products->values());
            let rowIndex = 0;
            const audio = new Audio('{{ asset('admin/beep.mp3') }}');
            const barcodeInput = document.getElementById('barcode');
            const scanButton = document.getElementById('scan-button');
            const productsContainer = document.getElementById('products-container');
            const transactionType = document.getElementById('type');
            const returnFields = document.getElementById('return-fields');
            const loanFields = document.getElementById('loan-fields');

            // Initialize with one empty row
            addProductRow();

            // Show/hide additional fields based on transaction type
            transactionType.addEventListener('change', function() {
                const selectedType = this.value;
                returnFields.style.display = selectedType === 'intake_return' ? 'block' : 'none';
                loanFields.style.display = selectedType === 'intake_loan' ? 'block' : 'none';
            });

            // Trigger change event to set initial state
            transactionType.dispatchEvent(new Event('change'));

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
                        <button type="button" class="btn btn-danger remove-product">
                            <i class="fas fa-times"></i>
                        </button>
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
                audio.play().catch(e => console.log('Audio play failed:', e));
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

                    if (selected.value) {
                        row.querySelector('.unit').value = selected.dataset.unit;
                        row.querySelector('.price-uzs').value = selected.dataset.priceUzs;
                        row.querySelector('.price-usd').value = selected.dataset.priceUsd;
                        audio.play().catch(e => console.log('Audio play failed:', e));
                        recalculateTotals();
                    }
                }
            });

            // Handle + / - qty buttons and remove product
            productsContainer.addEventListener('click', e => {
                const row = e.target.closest('.product-row');

                if (e.target.classList.contains('qty-btn') || e.target.closest('.qty-btn')) {
                    const btn = e.target.classList.contains('qty-btn') ? e.target : e.target.closest(
                        '.qty-btn');
                    const qtyInput = row.querySelector('.qty');
                    let qty = parseInt(qtyInput.value) || 0;

                    if (btn.dataset.action === 'increase') {
                        qty++;
                    } else if (btn.dataset.action === 'decrease' && qty > 1) {
                        qty--;
                    }

                    qtyInput.value = qty;
                    recalculateTotals();
                    audio.play().catch(e => console.log('Audio play failed:', e));
                }

                // Remove product row
                if (e.target.classList.contains('remove-product') || e.target.closest('.remove-product')) {
                    const rows = document.querySelectorAll('.product-row');
                    if (rows.length > 1) {
                        row.remove();
                        recalculateTotals();
                        audio.play().catch(e => console.log('Audio play failed:', e));
                    } else {
                        // Reset the single remaining row instead of removing it
                        const select = row.querySelector('.product-select');
                        const qty = row.querySelector('.qty');
                        select.value = '';
                        qty.value = 1;
                        row.querySelector('.unit').value = '';
                        row.querySelector('.price-uzs').value = '';
                        row.querySelector('.price-usd').value = '';
                        recalculateTotals();
                    }
                }
            });

            // Barcode scan functionality
            function handleBarcodeScan() {
                const code = barcodeInput.value.trim();
                if (!code) return;

                // Try to find exact match first
                let product = productsFromDb.find(p => p.barcode_value === code);

                // If no exact match, try to find by product ID as fallback
                if (!product) {
                    product = productsFromDb.find(p => p.id == code);
                }

                barcodeInput.value = '';
                barcodeInput.focus();

                if (!product) {
                    alert(`Product not found for code: ${code}`);
                    return;
                }

                // Check if product already exists in the list
                const existingRow = findProductRow(product.id);
                if (existingRow) {
                    const qtyInput = existingRow.querySelector('.qty');
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                    audio.play().catch(e => console.log('Audio play failed:', e));
                    recalculateTotals();
                } else {
                    addProductRow(product);
                }
            }

            // Helper function to find existing product row
            function findProductRow(productId) {
                const rows = document.querySelectorAll('.product-row');
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

            // Auto-focus barcode input
            barcodeInput.focus();
        });
        document.querySelector('form').addEventListener('submit', function(e) {
            let validProducts = 0;

            document.querySelectorAll('.product-row').forEach(row => {
                const productId = row.querySelector('.product-select')?.value;
                if (productId) {
                    validProducts++;
                }
            });

            if (validProducts === 0) {
                e.preventDefault();
                alert('Please select at least one product before submitting.');
            }
        });
    </script>
@endsection
