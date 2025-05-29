<div class="d-flex justify-content-between align-items-center">
    <div>
        <h4 class="card-title card-title-dash">Транзакции</h4>
    </div>
    <div class="d-flex justify-content-between align-items-center gap-2">
        <form action="{{ route('history.index') }}" method="GET" class="d-flex">
            <div class="d-flex gap-2">
                {{-- Поиск --}}
                <input type="text" name="search" class="form-control rounded"
                    style="height: 45px;" placeholder="Поиск по бренду..."
                    value="{{ request('search') }}" autofocus>

                <input type="date" name="start_date" class="form-control rounded"
                    style="height: 45px; width: 160px;" value="{{ request('start_date') }}">
                <input type="date" name="end_date" class="form-control rounded"
                    style="height: 45px; width: 160px;" value="{{ request('end_date') }}">
                <button type="submit" class="bg-white border-0" style="height: 45px;">
                    <i class="mdi mdi-check-circle-outline icon-md text-success"></i>
                </button>
            </div>
        </form>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle text-dark rounded" type="button" id="filterDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-filter-outline"></i> Фильтр
            </button>
            <div class="dropdown-menu p-3 shadow" style="min-width: 280px;" aria-labelledby="filterDropdown">
                <form method="GET" action="{{ route('history.index') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Сортировка:</label>
                        <select name="sort" class="form-select">
                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Сначала новые
                            </option>
                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Сначала старые
                            </option>
                        </select>
                    </div>

                    {{-- Тип операции --}}
                    <div class="mb-2">
                        <label class="form-label small">Тип операции:</label>
                        <select name="side" class="form-select">
                            <option value="">Все</option>
                            <option value="consume" {{ request('side') == 'consume' ? 'selected' : '' }}>Расход</option>
                            <option value="intake" {{ request('side') == 'intake' ? 'selected' : '' }}>Поступление
                            </option>
                            <option value="all" {{ request('loan_filter') == 'all' ? 'selected' : '' }}>Все займы
                            </option>
                            <option value="return" {{request('side') == 'return' ? 'selected' : ''}}>Все возвраты</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="brand_id" class="form-label small">Фильтр по бренду</label>
                        <select name="brand_id" id="brand_id" class="form-select">
                            <option value="">Все бренды</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Статус займа --}}
                    <div class="mb-2">
                        <label class="form-label small">Статус займа:</label>
                        <select name="status" class="form-select">
                            <option value="">Все</option>
                            <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Завершён
                            </option>
                            <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Не
                                завершён</option>
                        </select>
                    </div>

                    {{-- Направление займа --}}
                    <div class="mb-2">
                        <label class="form-label small">Направление займа:</label>
                        <select name="loan_direction" class="form-select">
                            <option value="">Все</option>
                            <option value="given" {{ request('loan_direction') == 'given' ? 'selected' : '' }}>Выдан
                                клиенту</option>
                            <option value="taken" {{ request('loan_direction') == 'taken' ? 'selected' : '' }}>Получен
                                от клиента</option>
                        </select>
                    </div>

                    {{-- Тип оплаты --}}
                    <div class="mb-2">
                        <label class="form-label small">Тип оплаты:</label>
                        <select name="payment_type" class="form-select">
                            <option value="">Все</option>
                            <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>Наличные
                            </option>
                            <option value="card" {{ request('payment_type') == 'card' ? 'selected' : '' }}>Карта
                            </option>
                            <option value="bank_transfer"
                                {{ request('payment_type') == 'bank_transfer' ? 'selected' : '' }}>Банковский перевод
                            </option>
                        </select>
                    </div>

                    {{-- Кнопки --}}
                    <div class="d-grid gap-2 mt-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-filter"></i> Применить
                        </button>
                        <a href="{{ route('history.index') }}" class="btn btn-sm btn-outline-secondary">Сбросить</a>
                    </div>
                </form>
            </div>
        </div>
        <button class="bg-white border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Очистить фильтры"
            onclick="window.location.href='{{ route('history.index') }}'">
            <i class="mdi mdi-refresh text-primary icon-md"></i>
        </button>
    </div>
</div>
