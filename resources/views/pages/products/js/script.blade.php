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
            document.getElementById('product_status').textContent = status;

        } else if (modalId === '#editProductModal') {
            document.getElementById('edit_product_id').value = element.getAttribute('data-id');
            document.getElementById('editProductForm').action = `/products/${element.getAttribute('data-id')}`;
            document.getElementById('photopreview').src = element.getAttribute('data-photo');
            document.getElementById('edit_product_name').value = element.getAttribute('data-name');
            document.getElementById('editShortDescription').value = element.getAttribute('data-description') || '';
            // document.getElementById('editBrand').value = element.getAttribute('data-brand');
            // document.getElementById('editCategory').value = element.getAttribute('data-category');
            // document.getElementById('editPriceUsd').value = element.getAttribute('data-usd-price');
            document.getElementById('editPriceUzs').value = element.getAttribute('data-uzs-price');
            document.getElementById('editSalePrice').value = element.getAttribute('data-sale-price');
            document.getElementById('editUnit').value = element.getAttribute('data-unit');
            // document.getElementById('editBarcode').src = element.getAttribute('data-barcode') || '';
            document.getElementById('editBarcode').src = element.getAttribute('data-barcode') || '';
        } else if(modalId === "#deleteProductModal"){
            document.getElementById('delete-product-form').action = `/products/${id}`;
        }

    }
</script>
