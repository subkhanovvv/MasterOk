<div class="modal fade" id="editProfileModal" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Новый товар</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="#" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="categoryName">Название товара</label>
                        <input type="text" class="form-control" placeholder="Название товара" name="name" required
                            id="categoryName">
                    </div>
                    <div class="form-group">
                        <label for="categoryPhone">Телефон </label>
                        <input type="text" class="form-control" placeholder="Телефон " name="phone" required
                            id="categoryPhone"value="+998">
                    </div>
                    <div class="form-group">
                        <label for="categoryDescription">Описание </label>
                        <input type="text" class="form-control" placeholder="Описание товара" name="description"
                            required id="categoryDescription">
                    </div>
                 
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-lg text-white">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
