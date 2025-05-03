<script>
    function openModal(element) {
        var id = element.getAttribute('data-id');
        var photo = element.getAttribute('data-photo');
        var phone = element.getAttribute('data-phone');
        var name = element.getAttribute('data-name');
        const modalId = element.getAttribute('data-bs-target');


        if (modalId === '#deleteBrandModal') {
            document.getElementById('delete-brand-form').action = `/brands/${id}`;
        } else if (modalId === '#editBrandModal') {
            currentModalType = 'edit';
            document.getElementById('edit_brand_id').value = id;
            document.getElementById('edit_brand_name').value = name;
            document.getElementById('edit_brand_description').value = element.getAttribute('data-description') || '';
            document.getElementById('edit_brand_photo').src = photo;
            document.getElementById('editBrandForm').action = `/brands/${id}`;
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
    applyPhoneMaskOnModal('newBrandModal', 'categoryPhone');
    applyPhoneMaskOnModal('editBrandModal', 'edit_brand_phone');
</script>
