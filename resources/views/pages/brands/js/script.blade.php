<script>
    const clientPhoneInput = document.getElementById('categoryPhone');
    maskUzPhoneInput(clientPhoneInput);

    function openModal(element) {
        var id = element.getAttribute('data-id');
        var photo = element.getAttribute('data-photo');
        var name = element.getAttribute('data-name');
        const modalId = element.getAttribute('data-bs-target');


        if (modalId === '#deleteBrandModal') {
            document.getElementById('delete-brand-form').action = `/brands/${id}`;
        } else if (modalId === '#editBrandModal') {
            currentModalType = 'edit';
            document.getElementById('edit_brand_id').value = id;
            document.getElementById('edit_brand_name').value = name;
            document.getElementById('edit_brand_description').value = element.getAttribute(
                'data-description') || '';
            document.getElementById('edit_brand_price').value = salePrice;
            document.getElementById('edit_brand_photo').src = photo;
            document.getElementById('editBrandForm').action = `/brands/${id}`;
        }
    }
</script>
