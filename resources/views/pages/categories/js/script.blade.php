<script>
    function openModal(element) {
        var id = element.getAttribute('data-id');
        var photo = element.getAttribute('data-photo');
        var name = element.getAttribute('data-name');
        const modalId = element.getAttribute('data-bs-target');


        if (modalId === '#deleteCategoryModal') {
            document.getElementById('delete-category-form').action = `/categories/${id}`;
        } else if (modalId === '#editCategoryModal') {
            currentModalType = 'edit';
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_category_name').value = name;
            document.getElementById('edit_category_photo').src = photo;
            document.getElementById('editCategoryForm').action = `/categories/${id}`;
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
</script>
