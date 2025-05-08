<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="card-title card-title-dash">Products</h4>
    </div>
    <div class="d-sm-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div>
                <form action="{{ route('products.index') }}" method="GET">
                    @csrf
                    <input type="text" class="form-control rounded" name="search" id="searchInput"
                           placeholder="Поиск..."  value="{{ request('search') }}" autofocus
                           style="height:45px; width:300px ; border:2px solid black" />
                </form>
            </div>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle text-dark" type="button" id="filterDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-filter-outline"></i> Фильтр
                </button>
                <div class="dropdown-menu p-3 shadow" style="min-width:300px;" aria-labelledby="filterDropdown">
                    <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                        <div class="mb-2">
                            <select name="category_id" class="form-select">
                                <option value="">Все категории</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}"
                                            {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <select name="brand_id" class="form-select">
                                <option value="">Все бренды</option>
                                @foreach ($brands as $b)
                                    <option value="{{ $b->id }}"
                                            {{ request('brand_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <select name="status" class="form-select">
                                <option value="">Все статусы</option>
                                <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>В наличии</option>
                                <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Мало</option>
                                <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Нет в наличии</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-filter"></i> Применить
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Сброс</a>
                        </div>
                    </form>
                </div>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProductModal" type="button">
                <i class="mdi mdi-plus"></i> Add new
            </button>
        </div>
        
    </div>
</div>