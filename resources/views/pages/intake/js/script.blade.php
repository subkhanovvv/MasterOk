<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productsFromDb = @json($products->values());
        let rowIndex = 0;
        // const audio = new Audio('{{ asset('admin/beep.mp3') }}');
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

            if (selectedType !== 'intake_loan') {
                // Clear loan-related fields
                document.getElementById('loan_amount').value = '';
                document.getElementById('loan_direction').value = '';
                document.getElementById('loan_due_to').value = '';
                document.getElementById('client_name').value = '';
                document.getElementById('client_phone').value = '';
            }

            if (selectedType !== 'intake_return') {
                document.getElementById('return_reason').value = '';
            }
        });

        // Trigger change event to set initial state
        transactionType.dispatchEvent(new Event('change'));

        function recalculateTotals() {
            let totalUzs = 0;

            document.querySelectorAll('.product-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const qty = parseFloat(row.querySelector('.qty')?.value || 0);
                    const priceUzs = parseFloat(row.querySelector('.price-uzs')?.value || 0);
                    totalUzs += qty * priceUzs;
                }
            });

            // Update display
            document.getElementById('total-uzs').textContent = totalUzs.toLocaleString();

            // Update hidden fields for form submission
            document.getElementById('total-price-hidden').value = totalUzs;

            return {
                totalUzs,
            };
        }

        function addProductRow(product = null) {
            const newRow = document.createElement('tr');

            newRow.className = 'product-row';
            newRow.innerHTML = `
            
                    <td>
                        <select class="form-select form-select-sm product-select" name="products[${rowIndex}][product_id]" required>
                            <option value="">Выберите продукт</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price-uzs="{{ $product->price_uzs }}"
                                    data-unit="{{ $product->unit }}"
                                    data-barcode="{{ $product->barcode_value }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control qty" name="products[${rowIndex}][qty]" min="1" value="1" required>
                      </td>
                    <td>
                   
                        <input type="text" class="form-control unit bg-white border-0" name="products[${rowIndex}][unit]" disabled>
                   </td>
                    <td>
                        <input type="number" class="form-control price-uzs bg-white border-0" disabled>
                   
                    </td>
                    <td class="text-center">
                         <button type="button" class="border-0 bg-white qty-btn" data-action="increase">
                            <i class="mdi mdi-plus-circle-outline icon-sm text-success"></i>
                            </button>
                    
                        <button type="button" class="border-0 bg-white qty-btn" data-action="decrease">
                            <i class="mdi mdi-minus-circle-outline icon-sm text-warning"></i>    
                        </button>
                          <button type="button" class="border-0 bg-white remove-product" id="clear-all">
                            <i class="mdi mdi-delete icon-sm text-danger"></i>
                    </button>
                    </td>
                `;

            if (product) {
                const select = newRow.querySelector('.product-select');
                select.value = product.id;
                newRow.querySelector('.price-uzs').value = product.price_uzs;
                newRow.querySelector('.unit').value = product.unit;
            }

            productsContainer.appendChild(newRow);
            rowIndex++;
            recalculateTotals();

        }

        // Enhanced Search Functionality
        const productSearch = document.getElementById('product_search');
        const searchResults = document.getElementById('search-results');

        function highlightMatch(text, searchTerm) {
            if (!searchTerm) return text;
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            return text.replace(regex, '<span class="text-primary fw-bold">$1</span>');
        }

        function performSearch() {
            const searchTerm = productSearch.value.trim().toLowerCase();
            searchResults.innerHTML = '';

            if (searchTerm === '') {
                searchResults.style.display = 'none';
                return;
            }

            // Get all brands and categories for display
            const brands = @json($brands ?? []);
            const categories = @json($categories ?? []);

            const filteredProducts = productsFromDb.filter(product => {
                return (
                    product.name.toLowerCase().includes(searchTerm) ||
                    (product.barcode_value && product.barcode_value.toString().includes(
                    searchTerm)) ||
                    (product.brand_id && brands[product.brand_id]?.name.toLowerCase().includes(
                        searchTerm)) ||
                    (product.category_id && categories[product.category_id]?.name.toLowerCase()
                        .includes(searchTerm))
                );
            });

            if (filteredProducts.length === 0) {
                searchResults.innerHTML = '<div class="p-2 text-muted">Товары не найдены</div>';
                searchResults.style.display = 'block';
                return;
            }

            filteredProducts.forEach(product => {
                const resultItem = document.createElement('div');
                resultItem.className = 'p-2 border-bottom cursor-pointer search-result-item';

                // Get brand and category names
                const brandName = product.brand_id ? brands[product.brand_id]?.name : 'Без бренда';
                const categoryName = product.category_id ? categories[product.category_id]?.name :
                    'Без категории';

                resultItem.innerHTML = `
            <div class="d-flex justify-content-between">
                <span>${highlightMatch(product.name, searchTerm)}</span>
                <small class="text-muted">${product.price_uzs} сум </small>
            </div>
            <div class="d-flex justify-content-between small">
                <span class="text-muted">${highlightMatch(brandName, searchTerm)} • ${highlightMatch(categoryName, searchTerm)}</span>
                <small class="text-muted">${product.barcode_value || 'Нет штрихкода'}</small>
            </div>
        `;
                resultItem.dataset.productId = product.id;
                searchResults.appendChild(resultItem);
            });

            searchResults.style.display = 'block';
        }

        // Immediate search on input
        productSearch.addEventListener('input', performSearch);

        // Handle click on search result (same as before)
        searchResults.addEventListener('click', (e) => {
            const resultItem = e.target.closest('.search-result-item');
            if (!resultItem) return;

            const productId = parseInt(resultItem.dataset.productId);
            const product = productsFromDb.find(p => p.id === productId);

            if (product) {
                const existingRow = findProductRow(product.id);
                if (existingRow) {
                    const qtyInput = existingRow.querySelector('.qty');
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                    recalculateTotals();
                } else {
                    // Remove default empty row if it exists
                    const rows = document.querySelectorAll('.product-row');
                    if (rows.length === 1) {
                        const select = rows[0].querySelector('.product-select');
                        if (!select.value) {
                            rows[0].remove();
                        }
                    }
                    addProductRow(product);
                }
            }

            // Clear search and hide results
            productSearch.value = '';
            searchResults.style.display = 'none';
            barcodeInput.focus();
        });

        // Hide search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!productSearch.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
        // Update totals when quantity is manually changed
        productsContainer.addEventListener('input', e => {
            if (e.target.classList.contains('qty')) {
                recalculateTotals();
            }
        });

        document.getElementById('add-product').addEventListener('click', () => {
            addProductRow();
        });

        document.getElementById('clear-all').addEventListener('click', () => {
            productsContainer.innerHTML = '';
            rowIndex = 0;
            addProductRow();
            recalculateTotals();
        });

        // Update fields when product selected from dropdown
        productsContainer.addEventListener('change', e => {
            if (e.target.classList.contains('product-select')) {
                const selected = e.target.options[e.target.selectedIndex];
                const row = e.target.closest('.product-row');

                if (selected.value) {
                    row.querySelector('.unit').value = selected.dataset.unit;
                    row.querySelector('.price-uzs').value = selected.dataset.priceUzs;
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

            }

            // Remove product row
            if (e.target.classList.contains('remove-product') || e.target.closest('.remove-product')) {
                const rows = document.querySelectorAll('.product-row');
                if (rows.length > 1) {
                    row.remove();
                    recalculateTotals();
                } else {
                    // Reset the single remaining row instead of removing it
                    const select = row.querySelector('.product-select');
                    const qty = row.querySelector('.qty');
                    select.value = '';
                    qty.value = 1;
                    row.querySelector('.unit').value = '';
                    row.querySelector('.price-uzs').value = '';
                    recalculateTotals();
                }
            }
        });

        // Barcode scan functionality
        function handleBarcodeScan() {
            const code = barcodeInput.value.trim();
            if (!code) return;

            let product = productsFromDb.find(p => p.barcode_value === code);
            if (!product) {
                product = productsFromDb.find(p => p.id == code);
            }

            barcodeInput.value = '';
            barcodeInput.focus();

            if (!product) {
                alert(`Product not found for code: ${code}`);
                return;
            }

            const existingRow = findProductRow(product.id);
            if (existingRow) {
                const qtyInput = existingRow.querySelector('.qty');
                qtyInput.value = parseInt(qtyInput.value) + 1;
                recalculateTotals();
            } else {
                // Remove default empty row if it exists
                const rows = document.querySelectorAll('.product-row');
                if (rows.length === 1) {
                    const select = rows[0].querySelector('.product-select');
                    if (!select.value) {
                        rows[0].remove(); // remove empty row
                    }
                }

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
        const totals = recalculateTotals();

        // Validate at least one product and positive totals
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

        if (totals.totalUzs <= 0) {
            e.preventDefault();
            alert('Total amount must be greater than zero in at least one currency.');
            return;
        }
    });
</script>
