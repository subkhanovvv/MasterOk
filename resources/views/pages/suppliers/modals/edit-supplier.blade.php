<div class="modal fade" id="editSupplierModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Редактировать категория</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editSupplierForm" method="POST" action="#" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_supplier_id">

                    <div class="form-group">
                        <label class="form-label">Название s</label>
                        <input type="text" class="form-control" id="edit_supplier_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">note s</label>
                        <input type="text" class="form-control" id="edit_supplier_note" name="name" required>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded text-white">Сохранить</button>
                </form>
            </div>
        </div>
    </div>
</div>
