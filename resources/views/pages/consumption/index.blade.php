@extends('layouts.admin')

@section('content')
    <form method="POST" action="{{ route('consumption.store') }}">
        @csrf

        <div class="card mb-3 border-0">
            <div class="card-body">
                <h5 class="card-title">Расход</h5>
                <div class="row g-3 p-0">

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
                            <option value="consume">Расход</option>
                            <option value="loan">В долг</option>
                            <option value="return">Возврат</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="note" class="form-label">Заметка</label>
                        <textarea class="form-control form-control-sm" name="note" id="note" rows="2">{{ old('note') }}</textarea>
                    </div>

                    <div id="return-fields" class="col-12" style="display: none;">
                        <label for="return_reason" class="form-label">Причина возврата</label>
                        <textarea class="form-control form-control-sm" name="return_reason" id="return_reason" rows="2">{{ old('return_reason') }}</textarea>
                    </div>

                    <div id="loan-fields" class="col-12" style="display: none;">
                        <div class="row g-3">
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
                            <div class="col-md-6">
                                <label for="client_name" class="form-label">Имя клиента</label>
                                <input type="text" class="form-control form-control-sm" name="client_name" id="client_name">
                            </div>
                            <div class="col-md-6">
                                <label for="client_phone" class="form-label">Телефон клиента</label>
                                <input type="number" class="form-control form-control-sm" name="client_phone" id="client_phone">
                            </div>
                        </div>
                    </div>

                       <div class="col-md-12 justify-content-end d-flex gap-2">
                        <input class="form-check-input" type="checkbox" id="print-checkbox" name="print" checked>
                        <label class="form-check-label" for="print-checkbox">
                            <small>Печать</small>
                        </label>
                    </div>

                </div>
            </div>
        </div>

        <input type="hidden" name="total_price" id="total-price-hidden" value="0">

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
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody id="products-container">
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted small">
                        <strong>Итого (сум):</strong> <span id="total-uzs">0</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary rounded">
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @include('pages.consumption.js.script')
@endsection
