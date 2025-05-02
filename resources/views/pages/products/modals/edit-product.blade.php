<div class="modal fade" id="editProductModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Редактировать товар</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" method="POST" action="#" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_product_id">

                    <div class="mb-3">
                        <label class="form-label">Название товара</label>
                        <input type="text" class="form-control" id="edit_product_name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание товара</label>
                        <textarea class="form-control" id="edit_product_description" name="short_description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Цена товара</label>
                        <input type="number" class="form-control" id="edit_product_price" name="sale_price" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Текущее изображение</label><br>
                        <img id="edit_product_photo" src="" alt="Фото товара" width="100">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Новое изображение</label>
                        <input type="file" class="form-control" name="photo">
                    </div>

                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </div>
        </div>
    </div>
</div>
