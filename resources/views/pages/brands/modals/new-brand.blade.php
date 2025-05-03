<div class="modal fade" id="newBrandModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Новый бренд</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('brands.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="brandName">Название бренда</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Название бренда"
                            name="name" required id="brandName">
                    </div>
                    <div class="form-group">
                        <label for="brandPhone">Телефон</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Телефон " name="phone"
                            required id="brandPhone">
                    </div>
                    <div class="form-group">
                        <label for="brandDescription">Описание </label>
                        <input type="text" class="form-control form-control-sm" placeholder="Описание бренда"
                            name="description" required id="brandDescription">
                    </div>
                    <div class="form-group">
                        <label for="photo">Загрузить изображения</label>
                        <div class="row g-4">
                            <div class="col-12 col-md-8">
                                <input class="form-control form-control-sm mb-3" type="file" class="form-control"
                                    name="photo" onchange="previewImage(event)">
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="preview" id="imagePreview">
                                    <img id="preview" src="" class="img-thumbnail">
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
