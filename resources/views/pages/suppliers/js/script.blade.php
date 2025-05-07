<script>
    function openModal(element) {
        var id = element.getAttribute('data-id');
        var name = element.getAttribute('data-name');
        var note = element.getAttribute('data-note');
        // var brand = element.getAttribute('data-brand');
        const modalId = element.getAttribute('data-bs-target');


        if (modalId === '#deleteSupplierModal') {
            document.getElementById('delete-supplier-form').action = `/suppliers/${id}`;
        } else if (modalId === '#editSupplierModal') {
            currentModalType = 'edit';
            document.getElementById('edit_supplier_id').value = id;
            document.getElementById('edit_supplier_name').value = name;
            document.getElementById('edit_supplier_note').value = note;
            document.getElementById('editSupplierForm').action = `/suppliers/${id}`;
        }
    }

</script>
