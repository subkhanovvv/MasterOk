<div class="d-flex justify-content-between align-items-center">
    <div>
        <h4 class="card-title card-title-dash">Бренды</h4>
    </div>
    <div class="d-flex justify-content-between align-items-center gap-2">
        <form action="{{ route('barcode.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="rounded form-control" placeholder="Поиск бренды..."
                style="height:45px; width:300px; border:1px solid black;" value="{{ request('search') }}" autofocus>
        </form>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle text-dark rounded" type="button" id="filterDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-filter-outline"></i> Фильтр
            </button>
            <div class="dropdown-menu p-3 shadow" style="min-width: 250px;" aria-labelledby="filterDropdown">
                <form method="GET" action="{{ route('barcode.index') }}">
                    <div class="mb-2">
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Сначала новые
                            </option>
                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Сначала старые
                            </option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <select name="brand_id" class="form-select">
                            <option value="">All Brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>In Stock
                            </option>
                            <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock
                            </option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>
                                Out
                                of Stock</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-filter"></i> Применить
                        </button>
                        <a href="{{ route('barcode.index') }}" class="btn btn-sm btn-outline-secondary">Сброс</a>
                    </div>
                </form>
            </div>
        </div>
        <button class="btn btn-primary rounded" onclick="openBarcodeModal('print-all')" type="button">
            <i class="mdi mdi-printer"></i> Print
        </button>
    </div>
</div>
