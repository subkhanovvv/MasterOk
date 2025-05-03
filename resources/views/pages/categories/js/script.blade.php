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
</script>
