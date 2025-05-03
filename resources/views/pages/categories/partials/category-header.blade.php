<div class="d-flex justify-content-between align-items-center">
    <div>
        <h4 class="card-title card-title-dash">Категории</h4>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <form action="{{ route('categories.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control rounded me-2" placeholder="Поиск категории..."
                style="height:45px;" value="{{ request('search') }}">
        </form>
        <button class="btn btn-primary rounded" data-bs-toggle="modal" data-bs-target="#newCategoryModal"
            type="button">
            <i class="mdi mdi-plus"></i> Новый
        </button>
    </div>
</div>
