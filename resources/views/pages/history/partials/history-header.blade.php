<div class="d-flex justify-content-between align-items-center">
    <div>
        <h4 class="card-title card-title-dash">Transactions</h4>
    </div>
    <div class="d-flex justify-content-between align-items-center gap-2">
        <form action="{{ route('history.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control rounded" placeholder="Поиск категории..."
                style="height:45px; width:300px; border:2px solid black;" value="{{ request('search') }}" autofocus>
        </form>
       <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle text-dark rounded" type="button" id="filterDropdown"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="mdi mdi-filter-outline"></i> Фильтр
    </button>
    <div class="dropdown-menu p-3 shadow" style="min-width: 280px;" aria-labelledby="filterDropdown">
        <form method="GET" action="{{ route('history.index') }}">
            {{-- Сортировка --}}
            <div class="mb-2">
                <label class="form-label small">Сортировка:</label>
                <select name="sort" class="form-select">
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Сначала новые</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Сначала старые</option>
                </select>
            </div>

            {{-- Сторона операции --}}
            <div class="mb-2">
                <label class="form-label small">Сторона операции:</label>
                <select name="side" class="form-select">
                    <option value="">Все</option>
                    <option value="consume" {{ request('side') == 'consume' ? 'selected' : '' }}>Расход</option>
                    <option value="intake" {{ request('side') == 'intake' ? 'selected' : '' }}>Поступление</option>
                </select>
            </div>

            {{-- Тип операции --}}
            <div class="mb-2">
                <label class="form-label small">Тип операции:</label>
                <select name="type" class="form-select">
                    <option value="">Все типы</option>
                    <option value="consume" {{ request('type') == 'consume' ? 'selected' : '' }}>Расход</option>
                    <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>Займ (выдача)</option>
                    <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Возврат (клиент)</option>
                    <option value="intake" {{ request('type') == 'intake' ? 'selected' : '' }}>Поступление (поставка)</option>
                    <option value="intake_loan" {{ request('type') == 'intake_loan' ? 'selected' : '' }}>Поступление (в долг)</option>
                    <option value="intake_return" {{ request('type') == 'intake_return' ? 'selected' : '' }}>Возврат поставщику</option>
                </select>
            </div>

            {{-- Статус займа --}}
            <div class="mb-2">
                <label class="form-label small">Статус займа:</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Завершен</option>
                    <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Не завершен</option>
                </select>
            </div>

            {{-- Направление займа --}}
            <div class="mb-2">
                <label class="form-label small">Направление займа:</label>
                <select name="loan_direction" class="form-select">
                    <option value="">Все</option>
                    <option value="given" {{ request('loan_direction') == 'given' ? 'selected' : '' }}>Выдан клиенту</option>
                    <option value="taken" {{ request('loan_direction') == 'taken' ? 'selected' : '' }}>Получен от лица</option>
                </select>
            </div>

            {{-- Тип оплаты --}}
            <div class="mb-2">
                <label class="form-label small">Тип оплаты:</label>
                <select name="payment_type" class="form-select">
                    <option value="">Все</option>
                    <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>Наличные</option>
                    <option value="card" {{ request('payment_type') == 'card' ? 'selected' : '' }}>Карта</option>
                    <option value="bank_transfer" {{ request('payment_type') == 'bank_transfer' ? 'selected' : '' }}>Банковский перевод</option>
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

    </div>
</div>
