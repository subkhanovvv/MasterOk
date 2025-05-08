<script>
    function openModal(element) {
        const id = element.getAttribute('data-id');
        const name = element.getAttribute('data-name');
        const note = element.getAttribute('data-note');
        const phone = element.getAttribute('data-phone');
        const photo = element.getAttribute('data-photo');
        const brandName = name;
        const productCount = element.getAttribute('data-product');
        const intake = element.getAttribute('data-intake');
        const description = element.getAttribute('data-description') || '';
        const modalId = element.getAttribute('data-bs-target');

        if (modalId === '#deleteSupplierModal') {
            const form = document.getElementById('delete-supplier-form');
            if (form) form.action = `/suppliers/${id}`;
        } else if (modalId === '#editSupplierModal') {
            const form = document.getElementById('editSupplierForm');
            if (form) {
                form.action = `/suppliers/${id}`;
                document.getElementById('edit_supplier_id').value = id;
                document.getElementById('edit_supplier_name').value = name;
                document.getElementById('edit_supplier_note').value = note;
            }
        } else if (modalId === '#viewBrandModal') {
            document.getElementById('view_brand_name').textContent = brandName;
            document.getElementById('view_brand_description').textContent = description;
            document.getElementById('view_brand_phone').textContent = phone;
            document.getElementById('view_brand_photo').src = photo;
            document.getElementById('view_brand_intake').textContent = intake;
            document.getElementById('view_brand_product').textContent = productCount;
        }
    }
</script>
