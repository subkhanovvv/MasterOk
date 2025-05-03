<div class="modal fade" id="newCategoryModal" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Новый категория</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="categoryName">Название категория</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Название категория"
                            name="name" required id="categoryName">
                    </div>
                    <div class="form-group">
                        <label for="photo">Загрузить изображения</label>
                        <div class="row g-4">
                            <div class="col-12 col-md-8">
                                <input class="form-control form-control-sm mb-3" type="file" name="photo"
                                    id="photo" accept="image/*" onchange="previewImage(event)">
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="preview" id="imagePreview" style="display: none;">
                                    <img id="preview" src="" alt="Image Preview" class="img-thumbnail"
                                        >
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
</div>
