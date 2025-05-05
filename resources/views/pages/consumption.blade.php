<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Consumption</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- <style>
        .consumption-card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        .product-select {
            width: 100%;
        }
        .qty-input {
            width: 90px;
        }
        #selectedProductsTable tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        .remove-product {
            transition: all 0.2s;
        }
        .remove-product:hover {
            transform: scale(1.1);
        }
    </style> --}}
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="consumption-card card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-cart-arrow-down me-2"></i> Product Consumption</h4>
                    </div>
                    <div class="card-body">
                        <form id="consumptionForm">
                            <!-- Product Selection Section -->
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-plus-circle me-2 text-success"></i>Add Products</h5>
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Product</label>
                                        <select class="form-select product-select" id="productSelect">
                                            <option value="">-- Select Product --</option>
                                            <option value="1" data-price="12.99" data-max="50">Premium Coffee (50 available)</option>
                                            <option value="2" data-price="8.50" data-max="30">Green Tea (30 available)</option>
                                            <option value="3" data-price="15.75" data-max="20">Organic Honey (20 available)</option>
                                            <option value="4" data-price="5.25" data-max="100">Sugar Packets (100 available)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control qty-input" id="productQuantity" min="1" value="1">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-success w-100" id="addProductBtn">
                                            <i class="fas fa-plus me-1"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Products Table -->
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-list me-2 text-primary"></i>Selected Products</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="selectedProductsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="40%">Product</th>
                                                <th width="15%">Price</th>
                                                <th width="15%">Quantity</th>
                                                <th width="20%">Total</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Products will be added here dynamically -->
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3" class="text-end">Grand Total:</th>
                                                <th id="grandTotal">$0.00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-edit me-2 text-info"></i>Additional Information</h5>
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Leave notes here" id="consumptionNotes" style="height: 100px"></textarea>
                                    <label for="consumptionNotes">Consumption notes (optional)</label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="button" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary" id="consumeBtn">
                                    <i class="fas fa-check-circle me-1"></i> Consume Products
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const productSelect = document.getElementById('productSelect');
            const productQuantity = document.getElementById('productQuantity');
            const addProductBtn = document.getElementById('addProductBtn');
            const selectedProductsTable = document.getElementById('selectedProductsTable').getElementsByTagName('tbody')[0];
            const consumptionForm = document.getElementById('consumptionForm');
            const grandTotalElement = document.getElementById('grandTotal');
            
            // Add product to table
            addProductBtn.addEventListener('click', function() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const productId = productSelect.value;
                const productName = selectedOption.text.split('(')[0].trim();
                const productPrice = parseFloat(selectedOption.getAttribute('data-price'));
                const maxQuantity = parseInt(selectedOption.getAttribute('data-max'));
                let quantity = parseInt(productQuantity.value) || 1;
                
                if (!productId) {
                    showAlert('Please select a product', 'warning');
                    return;
                }
                
                // Validate quantity
                if (quantity < 1) {
                    showAlert('Quantity must be at least 1', 'warning');
                    return;
                }
                
                if (quantity > maxQuantity) {
                    showAlert(`Only ${maxQuantity} items available`, 'warning');
                    return;
                }
                
                // Check if product already added
                const existingRow = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (existingRow) {
                    const existingQtyInput = existingRow.querySelector('.qty-input');
                    const existingQty = parseInt(existingQtyInput.value);
                    const newQty = existingQty + quantity;
                    
                    if (newQty > maxQuantity) {
                        showAlert(`Total quantity cannot exceed available stock (${maxQuantity})`, 'warning');
                        return;
                    }
                    
                    existingQtyInput.value = newQty;
                    updateRowTotal(existingRow);
                    updateGrandTotal();
                    return;
                }
                
                // Create new row
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-product-id', productId);
                
                newRow.innerHTML = `
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-light rounded p-2 me-2">
                                <i class="fas fa-box text-muted"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">${productName}</h6>
                                <small class="text-muted">SKU: PROD-${productId.toString().padStart(3, '0')}</small>
                                <input type="hidden" name="products[][id]" value="${productId}">
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">$${productPrice.toFixed(2)}</td>
                    <td class="align-middle">
                        <input type="number" name="products[][quantity]" 
                               class="form-control qty-input" 
                               min="1" max="${maxQuantity}" 
                               value="${quantity}" 
                               data-price="${productPrice}">
                    </td>
                    <td class="align-middle total">$${(quantity * productPrice).toFixed(2)}</td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-product">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;
                
                selectedProductsTable.appendChild(newRow);
                updateGrandTotal();
                
                // Reset selection
                productSelect.selectedIndex = 0;
                productQuantity.value = 1;
                
                // Show success message
                showAlert(`${productName} added to consumption list`, 'success');
            });
            
            // Remove product from table
            selectedProductsTable.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-product') || e.target.closest('.remove-product')) {
                    const row = e.target.closest('tr');
                    const productName = row.querySelector('h6').textContent;
                    row.remove();
                    updateGrandTotal();
                    showAlert(`${productName} removed from list`, 'info');
                }
            });
            
            // Update total when quantity changes
            selectedProductsTable.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input')) {
                    const row = e.target.closest('tr');
                    const maxQty = parseInt(e.target.getAttribute('max'));
                    const newQty = parseInt(e.target.value) || 0;
                    
                    if (newQty > maxQty) {
                        e.target.value = maxQty;
                        showAlert(`Cannot exceed maximum available quantity (${maxQty})`, 'warning');
                    }
                    
                    updateRowTotal(row);
                    updateGrandTotal();
                }
            });
            
            // Form submission
            consumptionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (selectedProductsTable.rows.length === 0) {
                    showAlert('Please add at least one product', 'warning');
                    return;
                }
                
                // Here you would normally submit to backend
                const formData = getFormData();
                console.log('Form data:', formData);
                
                // Show success message
                showAlert('Products consumed successfully!', 'success', 3000);
                
                // Reset form (in a real app, you might redirect instead)
                selectedProductsTable.innerHTML = '';
                updateGrandTotal();
                consumptionForm.reset();
            });
            
            // Helper function to update row total
            function updateRowTotal(row) {
                const quantity = parseInt(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.qty-input').getAttribute('data-price'));
                const totalCell = row.querySelector('.total');
                
                totalCell.textContent = `$${(quantity * price).toFixed(2)}`;
            }
            
            // Helper function to update grand total
            function updateGrandTotal() {
                let grandTotal = 0;
                const rows = selectedProductsTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const totalText = row.querySelector('.total').textContent;
                    grandTotal += parseFloat(totalText.replace('$', ''));
                });
                
                grandTotalElement.textContent = `$${grandTotal.toFixed(2)}`;
            }
            
            // Helper function to get form data
            function getFormData() {
                const notes = document.getElementById('consumptionNotes').value;
                const products = [];
                const rows = selectedProductsTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const productId = row.getAttribute('data-product-id');
                    const productName = row.querySelector('h6').textContent;
                    const quantity = parseInt(row.querySelector('.qty-input').value);
                    const price = parseFloat(row.querySelector('.qty-input').getAttribute('data-price'));
                    
                    products.push({
                        id: productId,
                        name: productName,
                        quantity: quantity,
                        price: price,
                        total: quantity * price
                    });
                });
                
                return {
                    notes: notes,
                    products: products,
                    grandTotal: parseFloat(grandTotalElement.textContent.replace('$', ''))
                };
            }
            
            // Helper function to show alerts
            function showAlert(message, type, duration = 2000) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
                alertDiv.style.zIndex = '1060';
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                document.body.appendChild(alertDiv);
                
                // Auto dismiss after duration
                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 150);
                }, duration);
            }
        });
    </script>
</body>
</html> /selection <del>

</del>

