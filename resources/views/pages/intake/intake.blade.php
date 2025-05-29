@extends('layouts.admin')

@section('content')
    <style>
        #search-results {
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            background: white;
            width: calc(100% - 38px);
            /* Match input width */
            position: absolute;
            cursor: pointer;
            top: 100%;
            left: 0;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }
    </style>
    <form method="POST" action="{{ route('intake.store') }}">
        @csrf
        <div class="card mb-3 border-0">
            <div class="card-body">
                <h5 class="card-title">Приход</h5>
                <div class="row g-3 p-0">

                    <div class="col-md-4">
                        <label for="supplier_id" class="form-label">Поставщик</label>
                        <select class="form-select form-select-md" id="supplier_id" name="supplier_id">
                            <option value="">Выберите поставщика</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="payment_type" class="form-label">Тип оплаты</label>
                        <div class="d-flex align-items-center">
                            <i id="payment_icon" class="mdi mdi-cash me-2 fs-4"></i>
                            <select class="form-select" id="payment_type" name="payment_type" required>
                                <option value="cash">Наличные</option>
                                <option value="card">Карта</option>
                                <option value="bank_transfer">Банковский перевод</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="type" class="form-label">Тип транзакции</label>
                        <select class="form-select form-select-md" id="type" name="type" required>
                            <option value="intake">Приход</option>
                            <option value="intake_loan">В долг</option>
                            <option value="intake_return">Возврат</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="note" class="form-label">Заметка</label>
                        <textarea class="form-control form-control-sm" name="note" id="note" rows="2">{{ old('note') }}</textarea>
                    </div>

                    <div id="return-fields" class="col-12" style="display: none;">
                        <label for="return_reason" class="form-label">Причина возврата</label>
                        <textarea class="form-control form-control-sm" name="return_reason" id="return_reason" rows="2">{{ old('return_reason') }}</textarea>
                    </div>

                    <div class="col-12" id="loan-fields" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="loan_amount" class="form-label">Сумма долга (сум)</label>
                                <input type="number" class="form-control form-control-sm" name="loan_amount"
                                    id="loan_amount" step="0.01">
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
                                <input type="date" class="form-control form-control-sm" name="loan_due_to"
                                    id="loan_due_to">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 justify-content-end d-flex gap-2">
                        <input class="form-check-input" type="checkbox" id="print-checkbox" name="print">
                        <label class="form-check-label" for="print-checkbox">
                            <small>Печать</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="total_price" id="total-price-hidden" value="0">
        <input type="hidden" name="total_usd" id="total-usd-hidden" value="0">
        <div class="card mb-3 w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center gap-4 flex-wrap">
                    <div class="d-flex align-items-center flex-grow-1 gap-3">
                        <label for="barcode" class="form-label mb-0">
                            <i class="mdi mdi-barcode-scan icon-md"></i>
                        </label>
                        <input type="text" id="barcode" class="form-control form-control rounded"
                            placeholder="Сканируйте или введите штрихкод..." autocomplete="off" autofocus>
                        <button type="button" class="border-0 bg-white" id="scan-button">
                            <i class="mdi mdi-check-circle-outline icon-md text-success"></i>
                        </button>
                    </div>
                    <div>
                        <button type="button" class="border-0 bg-white" id="add-product">
                            <i class="mdi mdi-plus-circle-multiple-outline icon-md text-warning"></i>
                        </button>
                    </div>
                    <div>
                        <button type="button" class="border-0 bg-white" id="clear-all">
                            <i class="mdi mdi-close-circle-outline icon-md text-danger"></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-3 position-relative">
                        <input type="text" id="product_search" class="form-control form-control rounded"
                            placeholder="Поиск товара..." autocomplete="off">
                        <button class="border-0 bg-white" id="search-button" type="button">
                            <i class="mdi mdi-magnify icon-md text-primary"></i>
                        </button>
                        <div id="search-results"
                            class="position-absolute top-100 start-0 w-100 bg-white z-3 shadow-sm rounded"
                            style="display: none;"></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered mb-3">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Количество</th>
                                <th>unit</th>
                                <th>Цена (сум)</th>
                                <th>Цена (доллар)</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody id="products-container">
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            <strong>Итого (сум):</strong> <span id="total-uzs">0</span> |
                            <strong>Итого (доллар):</strong> <span id="total-usd">0</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary rounded"> Сохранить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </form>
    @include('pages.intake.js.script')
    <script>
        const iconMap = {
            cash: 'mdi-cash',
            card: 'mdi-credit-card-outline',
            bank_transfer: 'mdi-bank-outline'
        };

        document.getElementById('payment_type').addEventListener('change', function() {
            const value = this.value;
            const icon = iconMap[value] || 'mdi-help-circle-outline';
            document.getElementById('payment_icon').className = `mdi ${icon} me-2 fs-4`;
        });
    </script>
@endsection
