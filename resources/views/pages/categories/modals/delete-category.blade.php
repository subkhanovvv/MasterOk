<!-- delete-category.blade.php -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить этот товар?
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteCategoryForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="category_id" id="category_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-danger text-white">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>
