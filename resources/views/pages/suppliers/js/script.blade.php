<script>
    function openModal(element) {
        var id = element.getAttribute('data-id');
        var name = element.getAttribute('data-name');
        var note = element.getAttribute('data-note');

        var brand_id = element.getAttribute('data-brand-id');
        var brand_photo = element.getAttribute('data-brand-photo');
        var brand_phone = element.getAttribute('data-brand-phone');
        var brand_name = element.getAttribute('data-brand-name');
        var brand_product = element.getAttribute('data-brand-product');
        var brand_supplier = element.getAttribute('data-brand-supplier');
        var brand_intake = element.getAttribute('data-brand-intake');

        const modalId = element.getAttribute('data-bs-target');


        if (modalId === '#deleteSupplierModal') {
            document.getElementById('delete-supplier-form').action = `/suppliers/${id}`;
        } else if (modalId === '#editSupplierModal') {
            currentModalType = 'edit';
            document.getElementById('edit_supplier_id').value = id;
            document.getElementById('edit_supplier_name').value = name;
            document.getElementById('edit_supplier_note').value = note;
            document.getElementById('editSupplierForm').action = `/suppliers/${id}`;
        } else if (modalId === '#viewBrandModal') {
            currentModalType = 'view';
            document.getElementById('view_brand_name').textContent = brand_name;
            document.getElementById('view_brand_description').textContent = element.getAttribute(
                'data-brand-description') || '';
            document.getElementById('view_brand_phone').textContent = brand_phone;
            document.getElementById('view_brand_photo').src = brand_photo;
            document.getElementById('view_brand_intake').textContent = brand_intake;
            document.getElementById('view_brand_product').textContent = brand_product;
            document.getElementById('view_brand_supplier').textContent = brand_supplier;
        } else if (modalId === '#viewSupplierModal') {
            currentModalType = 'view';
            document.getElementById('view_supplier_name').textContent = name;
            document.getElementById('view_supplier_phone').textContent = brand_phone;
            document.getElementById('view_supplier_note').textContent = note;
            document.getElementById('view_supplier_brand').textContent = brand_name;
        }
    }
</script>
