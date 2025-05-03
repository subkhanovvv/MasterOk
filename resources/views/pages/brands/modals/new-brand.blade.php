<div class="modal fade" id="newBrandModal" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Новый товар</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('brands.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="categoryName">Название товара</label>
                        <input type="text" class="form-control" placeholder="Название товара" name="name" required
                            id="categoryName">
                    </div>
                    <div class="form-group">
                        <label for="categoryPhone">Телефон </label>
                        <input type="text" class="form-control" placeholder="Телефон " name="phone" required
                            id="categoryPhone">
                    </div>
                    <div class="form-group">
                        <label for="categoryDescription">Описание </label>
                        <input type="text" class="form-control" placeholder="Описание товара" name="description"
                            required id="categoryDescription">
                    </div>
                    <div class="form-group">
                        <label for="photo">Загрузить изображения</label>
                        <div class=" row g-4">
                            <div class="col-12 col-md-6">
                                <input class="form-control form-control-sm mb-3" type="file" name="photo"
                                    id="photo" onchange="previewImage(event)">
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="preview mb-3" id="imagePreview" style="display: none;">
                                    <img id="preview" src="" alt="Image Preview" class="img-thumbnail"
                                        style="max-width: 50%;">
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-lg text-white">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    