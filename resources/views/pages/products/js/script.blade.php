<script>
    function openModal(element) {
        var id = element.getAttribute('data-id');
        var photo = element.getAttribute('data-photo');
        var description = element.getAttribute('data-description');
        var name = element.getAttribute('data-name');
        var status = element.getAttribute('data-status');
        var barcode = element.getAttribute('data-barcode');

        var uzsPrice = element.getAttribute('data-uzs-price');
        var usdPrice = element.getAttribute('data-usd-price');
        var salePrice = element.getAttribute('data-sale-price');

        var brand = element.getAttribute('data-brand');
        var category = element.getAttribute('data-category');

        var qty = element.getAttribute('data-qty');
        var unit = element.getAttribute('data-unit');
        var stock_unit = element.getAttribute('data-stock-unit');
        var units_per_stock = element.getAttribute('data-units-per-stock');

        var updated_at = element.getAttribute('data-updated-at');
        var created_at = element.getAttribute('data-created-at');


        const modalId = element.getAttribute('data-bs-target');
        if (modalId === '#viewProductModal') {

            document.getElementById('product_id').value = id;
            document.getElementById('product_photo').src = photo;
            document.getElementById('product_barcode').src = barcode;
            document.getElementById('product_name').textContent = name;
            document.getElementById('product_uzs_price').textContent = uzsPrice;
            document.getElementById('product_usd_price').textContent = usdPrice;
            document.getElementById('product_sale_price').textContent = salePrice
            document.getElementById('product_description').textContent = description;
            document.getElementById('product_qty').textContent = qty;
            document.getElementById('product_updated_at').textContent = updated_at;
            document.getElementById('product_created_at').textContent = created_at;
            document.getElementById('product_brand').textContent = brand;
            document.getElementById('product_category').textContent = category;
            document.getElementById('product_unit').textContent = unit;
            document.getElementById('product_stock_unit').textContent = stock_unit;
            document.getElementById('product_units_per_stock').textContent = units_per_stock;
            document.getElementById('product_status').textContent = status;

        } else if (modalId === '#editProductModal') {
            document.getElementById('editProductForm').action = `/products/${product.id}`;

            // Set basic info
            document.getElementById('editName').value = product.name || '';
            document.getElementById('editShortDescription').value = product.short_description || '';

            // Set prices
            document.getElementById('editPriceUsd').value = product.price_usd || '';
            document.getElementById('editPriceUzs').value = product.price_uzs || '';
            document.getElementById('editSalePrice').value = product.sale_price || '';

            // Set selects
            document.getElementById('editBrand').value = product.brand_id || '';
            document.getElementById('editCategory').value = product.category_id || '';
            if (product.unit) document.getElementById('editUnit').value = product.unit;
            if (product.stock_unit) document.getElementById('editStockUnit').value = product.stock_unit;

            // Set units
            document.getElementById('editUnitsPerStock').value = product.units_per_stock || '';

            // Set image preview
            const preview = document.getElementById('editPreview');
            const placeholder = document.getElementById('editImagePlaceholder');
            if (product.photo) {
                preview.src = `/storage/${product.photo}`;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                preview.src = '';
                preview.style.display = 'none';
                placeholder.style.display = 'flex';
            }

            // Set barcode if exists
            if (product.barcode) {
                document.getElementById('editBarcode').src = product.barcode;
                document.getElementById('editBarcode').style.display = 'block';
            } else {
                document.getElementById('editBarcode').style.display = 'none';
            }

            // Set currency rate if exists
            if (usdToUzsRate) {
                document.getElementById('editUsdUzsRate').textContent = usdToUzsRate.toFixed(2);
            }
        }

        // Initialize currency conversion for edit modal
        document.addEventListener('DOMContentLoaded', () => {
            const editUsdInput = document.querySelector('#editPriceUsd');
            const editUzsInput = document.querySelector('#editPriceUzs');

            if (editUsdInput && editUzsInput) {
                editUsdInput.addEventListener('input', () => {
                    if (usdToUzsRate) {
                        const usdValue = parseFloat(editUsdInput.value);
                        if (!isNaN(usdValue)) {
                            const uzsValue = usdValue * usdToUzsRate;
                            editUzsInput.value = uzsValue.toFixed(2);
                        } else {
                            editUzsInput.value = '';
                        }
                    }
                });
            }}
        });
        // // Global variables
        // var unitPrice = 0;
        // var quantity = 1;
        // var currentModalType = 'consume';
        // var productCounter = 1; // Counter for additional products
        // var selectedProducts = []; // Array to store selected products

        // // Add product to the form
        // function addProductToForm(product) {
        //     const productIndex = productCounter++;

        //     // Add to selected products array
        //     selectedProducts.push({
        //         id: product.id,
        //         name: product.name,
        //         price: parseFloat(product.sale_price.replace(/\s/g, '')),
        //         quantity: 1
        //     });

        //     // Create form inputs for the new product
        //     const form = document.getElementById('consumeForm');

        //     // Create hidden inputs for the product
        //     const productIdInput = document.createElement('input');
        //     productIdInput.type = 'hidden';
        //     productIdInput.name = `products[${productIndex}][product_id]`;
        //     productIdInput.value = product.id;
        //     form.appendChild(productIdInput);

        //     const quantityInput = document.createElement('input');
        //     quantityInput.type = 'hidden';
        //     quantityInput.name = `products[${productIndex}][quantity]`;
        //     quantityInput.value = 1;
        //     form.appendChild(quantityInput);

        //     const priceInput = document.createElement('input');
        //     priceInput.type = 'hidden';
        //     priceInput.name = `products[${productIndex}][total_price]`;
        //     priceInput.value = product.sale_price.replace(/\s/g, '');
        //     form.appendChild(priceInput);

        //     // Create a visible product card in the selected products list
        //     const productCard = document.createElement('div');
        //     productCard.className = 'card mb-2';
        //     productCard.innerHTML = `
        // <div class="card-body p-2">
        //     <div class="d-flex justify-content-between align-items-center">
        //         <div class="d-flex align-items-center">
        //             <img src="${product.photo_url}" alt="${product.name}" style="width: 50px; height: 50px;" class="rounded me-2">
        //                 <div>
        //                     <h6 class="mb-0">${product.name}</h6>
        //                     <small class="text-muted">${product.sale_price} сум × 1</small>
        //                 </div>
        //         </div>
        //         <div class="d-flex align-items-center">
        //             <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="adjustProductQuantity(${product.id}, -1)">
        //                 <i class="mdi mdi-minus"></i>
        //             </button>
        //             <span id="productQty_${product.id}">1</span>
        //             <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="adjustProductQuantity(${product.id}, 1)">
        //                 <i class="mdi mdi-plus"></i>
        //             </button>
        //             <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeProduct(${product.id})">
        //                 <i class="mdi mdi-delete"></i>
        //             </button>
        //         </div>
        //     </div>
        // </div>
        // `;

        //     document.getElementById('selectedProductsList').appendChild(productCard);

        //     // Clear search input
        //     document.getElementById('addProductInput').value = '';

        //     // Update grand total
        //     updateGrandTotal();
        // }

        // // Adjust product quantity
        // function adjustProductQuantity(productId, change) {
        //     const product = selectedProducts.find(p => p.id === productId);
        //     if (!product) return;

        //     const newQuantity = product.quantity + change;
        //     if (newQuantity < 1) return;

        //     product.quantity = newQuantity;
        //     document.getElementById(`productQty_${productId}`).textContent = newQuantity;

        //     // Update the hidden input value
        //     const inputs = document.querySelectorAll(`input[name^="products"][name$="product_id]`);
        //     for (let input of inputs) {
        //         if (parseInt(input.value) === productId) {
        //             const index = input.name.match(/\[(\d+)\]/)[1];
        //             document.querySelector(`input[name="products[${index}][quantity]"]`).value = newQuantity;
        //             document.querySelector(`input[name="products[${index}][total_price]"]`).value = (product.price *
        //                 newQuantity).toFixed(2);
        //             break;
        //         }
        //     }

        //     updateGrandTotal();
        // }

        // // Remove product
        // function removeProduct(productId) {
        //     selectedProducts = selectedProducts.filter(p => p.id !== productId);

        //     // Remove the product card
        //     const cards = document.querySelectorAll('#selectedProductsList .card');
        //     for (let card of cards) {
        //         if (card.querySelector('button[onclick*="' + productId + '"]')) {
        //             card.remove();
        //             break;
        //         }
        //     }

        //     // Remove the hidden inputs
        //     const inputs = document.querySelectorAll(`input[name^="products"][name$="product_id]`);
        //     for (let input of inputs) {
        //         if (parseInt(input.value) === productId) {
        //             const index = input.name.match(/\[(\d+)\]/)[1];
        //             document.querySelector(`input[name="products[${index}][quantity]"]`).remove();
        //             document.querySelector(`input[name="products[${index}][total_price]"]`).remove();
        //             input.remove();
        //             break;
        //         }
        //     }

        //     updateGrandTotal();
        // }

        // // Update grand total
        // function updateGrandTotal() {
        //     let grandTotal = 0;

        //     // Calculate total from the main product
        //     const mainProductQty = parseInt(document.getElementById('consume_qty').value) || 0;
        //     grandTotal += unitPrice * mainProductQty;

        //     // Calculate total from additional products
        //     for (let product of selectedProducts) {
        //         grandTotal += product.price * product.quantity;
        //     }

        //     document.getElementById('consume_grand_total').textContent = grandTotal.toLocaleString();
        // }

        // function increaseQty() {
        //     var qtyInputId = currentModalType === 'consume' ? 'consume_qty' : 'intake_qty';
        //     var qtyInput = document.getElementById(qtyInputId);
        //     var currentQty = parseInt(qtyInput.value);
        //     if (!isNaN(currentQty)) {
        //         qtyInput.value = currentQty + 1;
        //         updateTotal();
        //         updateGrandTotal();
        //     }
        // }

        // function decreaseQty() {
        //     var qtyInputId = currentModalType === 'consume' ? 'consume_qty' : 'intake_qty';
        //     var qtyInput = document.getElementById(qtyInputId);
        //     var currentQty = parseInt(qtyInput.value);
        //     if (!isNaN(currentQty) && currentQty > 1) {
        //         qtyInput.value = currentQty - 1;
        //         updateTotal();
        //         updateGrandTotal();
        //     }
        // }

        // function updateTotal() {
        //     if (currentModalType === 'edit') return; // Skip total update for edit modal

        //     var qtyInputId = currentModalType === 'consume' ? 'consume_qty' : 'intake_qty';
        //     var totalPriceId = currentModalType === 'consume' ? 'consume_total_price' : 'intake_total_price';
        //     var hiddenTotalPriceId = currentModalType === 'consume' ? 'consume_hidden_total_price' :
        //         'intake_hidden_total_price';
        //     var quantity = parseInt(document.getElementById(qtyInputId).value);

        //     if (isNaN(quantity) || quantity < 1) {
        //         quantity = 1;
        //         document.getElementById(qtyInputId).value = quantity;
        //     }

        //     var total = unitPrice * quantity;
        //     document.getElementById(totalPriceId).textContent = total.toLocaleString();
        //     document.getElementById(hiddenTotalPriceId).value = total;
        // }

        // function onTransactionTypeChange() {
        //     var typeSelectId = currentModalType === 'consume' ? 'consume_transaction_type' : 'intake_transaction_type';
        //     var selectElement = document.getElementById(typeSelectId);
        //     if (!selectElement) return;

        //     var type = selectElement.value;

        //     if (currentModalType === 'consume') {
        //         var clientPhoneGroup = document.getElementById('consume_client_phone_group');
        //         var clientNameGroup = document.getElementById('consume_client_name_group');
        //         var clientPhoneInput = document.getElementById('consume_client_phone');
        //         var clientNameInput = document.getElementById('consume_client_name');
        //         var returnReasonGroup = document.getElementById('consume_return_reason_group');
        //         var returnReasonInput = document.getElementById('consume_return_reason');

        //         if (type === 'loan') {
        //             clientPhoneGroup.style.display = 'block';
        //             clientNameGroup.style.display = 'block';
        //             setTimeout(() => clientPhoneInput.focus(), 100);
        //             setTimeout(() => clientNameInput.focus(), 100);
        //         } else {
        //             clientPhoneGroup.style.display = 'none';
        //             clientPhoneInput.value = '';
        //             clientNameGroup.style.display = 'none';
        //             clientNameInput.value = '';
        //         }

        //         if (type === 'return') {
        //             returnReasonGroup.style.display = 'block';
        //             setTimeout(() => returnReasonInput.focus(), 100);
        //         } else {
        //             returnReasonGroup.style.display = 'none';
        //             returnReasonInput.value = '';
        //         }

        //     } else if (currentModalType === 'intake') {
        //         var returnReasonGroup = document.getElementById('intake_return_reason_group');
        //         var returnReasonInput = document.getElementById('intake_return_reason');

        //         if (type === 'intake_return') {
        //             returnReasonGroup.style.display = 'block';
        //             setTimeout(() => returnReasonInput.focus(), 100);
        //         } else {
        //             returnReasonGroup.style.display = 'none';
        //             returnReasonInput.value = '';
        //         }
        //     }
        // }
</script>
