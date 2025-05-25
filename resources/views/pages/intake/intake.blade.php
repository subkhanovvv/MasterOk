@extends('layouts.admin')

@section('content')
    <div class="container">
        <form method="POST" action="{{ route('intake.store') }}">
            @csrf
            <div class="card mb-3 border-0">
                <div class="card-body row g-3 p-0">
                    <div class="col-md-4">
                        <label for="supplier_id" class="form-label">Поставщик</label>
                        <select class="form-select form-select-sm" id="supplier_id" name="supplier_id">
                            <option value="">Выберите поставщика</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="payment_type" class="form-label">Тип оплаты</label>
                        <select class="form-select form-select-sm" id="payment_type" name="payment_type" required>
                            <option value="cash">Наличные</option>
                            <option value="card">Карта</option>
                            <option value="bank_transfer">Банковский перевод</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="type" class="form-label">Тип транзакции</label>
                        <select class="form-select form-select-sm" id="type" name="type" required>
                            <option value="intake">Поступление</option>
                            <option value="intake_loan">В долг</option>
                            <option value="intake_return">Возврат</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="note" class="form-label">Заметка</label>
                        <textarea class="form-control form-control-sm" name="note" id="note" rows="2">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Поля для возврата -->
            <div id="return-fields" class="mb-3" style="display: none;">
                <div class="card border-0">
                    <div class="card-body p-2">
                        <label for="return_reason" class="form-label">Причина возврата</label>
                        <textarea class="form-control form-control-sm" name="return_reason" id="return_reason" rows="2">{{ old('return_reason') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Поля для долга -->
            <div id="loan-fields" class="mb-3" style="display: none;">
                <div class="card border-0">
                    <div class="card-body row g-3 p-0">
                        <div class="col-md-4">
                            <label for="loan_amount" class="form-label">Сумма долга (сум)</label>
                            <input type="number" class="form-control form-control-sm" name="loan_amount" id="loan_amount" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="loan_direction" class="form-label">Направление долга</label>
                            <select name="loan_direction" id="loan_direction" class="form-select form-select-sm">
                                <option value="given">Выдан</option>
                                <option value="taken">Получен</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="loan_due_to" class="form-label">Срок погашения</label>
                            <input type="date" class="form-control form-control-sm" name="loan_due_to" id="loan_due_to">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Скрытые поля -->
            <input type="hidden" name="total_price" id="total-price-hidden" value="0">
            <input type="hidden" name="total_usd" id="total-usd-hidden" value="0">

            <!-- Сканер штрих-кода -->
            <div class="mb-3 d-flex gap-2">
                <input type="text" id="barcode" class="form-control form-control-sm" placeholder="Сканируйте или введите штрихкод..." autocomplete="off" autofocus>
                <button type="button" class="btn btn-outline-success btn-sm" id="scan-button">
                    <i class="fas fa-barcode"></i> Сканировать
                </button>
            </div>

            <!-- Таблица товаров -->
            <div id="products-container" class="mb-3">
                <!-- JavaScript отобразит здесь таблицу -->
            </div>

            <!-- Действия -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn btn-outline-primary btn-sm" id="add-product">
                    <i class="fas fa-plus"></i> Добавить товар
                </button>
                <div class="text-muted small">
                    <strong>Итого (сум):</strong> <span id="total-uzs">0</span> |
                    <strong>Итого (доллар):</strong> <span id="total-usd">0</span>
                </div>
            </div>

            <!-- Кнопка отправки -->
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-check"></i> Сохранить поступление
            </button>
        </form>
    </div>

    @include('pages.intake.js.script')
@endsection
