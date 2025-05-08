<script>
    function openModal(element) {
        var id = element.getAttribute('data-id');
        var photo = element.getAttribute('data-photo');
        var phone = element.getAttribute('data-phone');
        var name = element.getAttribute('data-name');
        var product = element.getAttribute('data-product');
        var intake = element.getAttribute('data-intake');
        var supplier = element.getAttribute('data-supplier');
        const modalId = element.getAttribute('data-bs-target');


        if (modalId === '#deleteBrandModal') {
            document.getElementById('delete-brand-form').action = `/brands/${id}`;
        } else if (modalId === '#editBrandModal') {
            currentModalType = 'edit';
            document.getElementById('edit_brand_id').value = id;
            document.getElementById('edit_brand_name').value = name;
            document.getElementById('edit_brand_description').value = element.getAttribute('data-description') || '';
            document.getElementById('edit_brand_photo').src = photo;
            document.getElementById('edit_brand_phone').value = phone;
            document.getElementById('editBrandForm').action = `/brands/${id}`;
        } else if (modalId === '#viewBrandModal') {
            currentModalType = 'view';
            document.getElementById('view_brand_name').textContent = name;
            document.getElementById('view_brand_description').textContent = element.getAttribute('data-description') || '';
            document.getElementById('view_brand_phone').textContent = phone;
            document.getElementById('view_brand_photo').src = photo;
            document.getElementById('view_brand_intake').textContent = intake;
            document.getElementById('view_brand_product').textContent = product;
            document.getElementById('view_brand_supplier').textContent = supplier;
        }

    }

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }

    function updatePreviewImage(event) {
        const input = event.target;
        const previewImg = document.getElementById('edit_brand_photo');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => previewImg.src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    }

    function applyPhoneMaskOnModal(modalId, inputId) {
        const modal = document.getElementById(modalId);

        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                const phoneInput = document.getElementById(inputId);
                if (phoneInput && !phoneInput.classList.contains('masked')) {
                    maskUzPhoneInput(phoneInput);
                    phoneInput.classList.add('masked');
                }
            });
        }
    }
    applyPhoneMaskOnModal('newBrandModal', 'brandPhone');
    applyPhoneMaskOnModal('editBrandModal', 'edit_brand_phone');
</script>
