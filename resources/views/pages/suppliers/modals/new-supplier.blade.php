<div class="modal fade" id="newSupplierModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Новый поставщик</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('suppliers.store') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="supplierName">Название поставщика</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Введите название поставщика"
                            name="name" required id="supplierName">
                    </div>
                    <div class="form-group mb-3">
                        <label for="supplierNote">Примечание</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Дополнительная информация"
                            name="note" id="supplierNote">
                    </div>
                    <div class="form-group">
                        <label for="brand_id">Бренд</label>
                        <select name="brand_id" class="form-select" id="brand_id" required>
                            <option disabled selected>Выберите бренд</option>
                            @foreach ($brands as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded text-white">Сохранить</button>
                </form>
            </div>
        </div>
    </div>
</div>
