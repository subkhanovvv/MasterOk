<div class="d-flex justify-content-between align-items-center">
    <div>
        <h4 class="card-title card-title-dash">Категории</h4>
    </div>
    <div class="d-flex justify-content-between align-items-center gap-2">
        <form action="{{ route('categories.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control rounded" placeholder="Поиск категории..."
                style="height:45px; width:300px; border:2px solid black;" value="{{ request('search') }}"
                autofocus>
        </form>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle text-dark rounded" type="button" id="filterDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-filter-outline"></i> Фильтр
            </button>
            <div class="dropdown-menu p-3 shadow" style="min-width: 250px;" aria-labelledby="filterDropdown">
                <form method="GET" action="{{ route('categories.index') }}">
                    <div class="mb-2">
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Сначала новые
                            </option>
                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Сначала старые
                            </option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <button class="btn btn-primary rounded" data-bs-toggle="modal" data-bs-target="#newCategoryModal"
            type="button">
            <i class="mdi mdi-plus"></i> Новый
        </button>
    </div>
</div>
