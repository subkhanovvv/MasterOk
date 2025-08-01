<div class="modal fade" id="viewSupplierModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Просмотр поставщика</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">

                    <div class="col-md-6 text-center mb-3">

                        <div class="form-group">
                            <img id="view_supplier_photo" src="{{ asset('admin/assets/images/driver.png') }}"
                                class="rounded-circle border" alt="Изображение поставщика"
                                style="width: 150px; height: 150px; object-fit: cover;">
                        </div>

                        <div class="form-group">
                            <h5 id="view_supplier_name"></h5>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Бренд:</label>
                            <p id="view_supplier_brand"></p>
                        </div>

                        <div class="form-group">
                            <label>Телефон бренда:</label>
                            <p id="view_supplier_phone"></p>
                        </div>

                        <div class="form-group">
                            <label for="view_supplier_description">Описание:</label>
                            <p id="view_supplier_note"></p>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
