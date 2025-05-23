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
            returnFields.style.display = selectedType === 'return' ? 'block' : 'none';
            loanFields.style.display = selectedType === 'loan' ? 'block' : 'none';

            if (selectedType !== 'loan') {
                // Clear loan-related fields
                document.getElementById('loan_amount').value = '';
                document.getElementById('loan_direction').value = '';
                document.getElementById('loan_due_to').value = '';
                document.getElementById('client_name').value = '';
                document.getElementById('client_phone').value = '';
            }

            if (selectedType !== 'return') {
                document.getElementById('return_reason').value = '';
            }
        });


        // Trigger change event to set initial state
        transactionType.dispatchEvent(new Event('change'));

        function recalculateTotals() {
            let total = 0;

            document.querySelectorAll('.product-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const qty = parseFloat(row.querySelector('.qty')?.value || 0);
                    const price = parseFloat(row.querySelector('.price')?.value || 0);
                    total += qty * price;
                }
            });

            // Update display
            document.getElementById('total-uzs').textContent = total.toLocaleString();

            // Update hidden field for form submission
            document.getElementById('total-price-hidden').value = total;

            return total;
        }

        function addProductRow(product = null) {
            const newRow = document.createElement('div');
            newRow.className = 'product-row row g-2 mb-2';
            newRow.innerHTML = `
                <div class="col-md-5">
                    <select class="form-select product-select" name="products[${rowIndex}][product_id]" required>
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-unit="{{ $product->unit }}"
                                data-price="{{ $product->sale_price }}"
                                data-barcode="{{ $product->barcode_value }}">
                                {{ $product->name }} ({{ $product->sale_price }} UZS)
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
                    <input type="number" class="form-control price" name="products[${rowIndex}][price]" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-product">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            `;

            if (product) {
                const select = newRow.querySelector('.product-select');
                select.value = product.id;
                newRow.querySelector('.unit').value = product.unit;
                newRow.querySelector('.price').value = product.sale_price;
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
                    row.querySelector('.price').value = selected.dataset.price;
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
                    row.querySelector('.price').value = '';
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
        // Recalculate totals before submission
        const total = recalculateTotals();

        // Validate at least one product and positive total
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
            return;
        }

        if (total <= 0) {
            e.preventDefault();
            alert('Total amount must be greater than zero.');
            return;
        }
    });
</script>
