<div class="modal fade" id="editCategoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Редактировать категория</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm" method="POST" action="#" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_category_id">

                    <div class="form-group">
                        <label class="form-label">Название категория</label>
                        <input type="text" class="form-control" id="edit_category_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="photo">Загрузить изображения</label>
                        <div class="row g-4">
                            <div class="col-12 col-md-8">
                                <input class="form-control form-control-sm mb-3" type="file" class="form-control"
                                    name="photo" onchange="updatePreviewImage(event)">
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="preview" id="imagePreview">
                                    <img id="edit_category_photo" src="" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded text-white">Сохранить</button>
                </form>
            </div>
        </div>
    </div>
</div>
