@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4">Отчет по деятельности</h4>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Дата начала</label>
                <input type="date" name="start_date" class="form-control" value="{{ $start }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Дата окончания</label>
                <input type="date" name="end_date" class="form-control" value="{{ $end }}">
            </div>
            <div class="col-md-3">
                <label for="brand_id" class="form-label">Бренд</label>
                <select name="brand_id" class="form-select">
                    <option value="">Все бренды</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
            <label class="form-label small">Сторона операции:</label>
            <select name="side" class="form-select">
                <option value="">Все</option>
                <option value="consume" {{ request('side') == 'consume' ? 'selected' : '' }}>Расход</option>
                <option value="intake" {{ request('side') == 'intake' ? 'selected' : '' }}>Поступление
                </option>
            </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Показать</button>
            </div>
        </form>
        <div class="mb-3 d-flex gap-2">
            <a href="{{ route('report.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                class="btn btn-danger">
                Скачать PDF
            </a>
            <a href="{{ route('report.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
                class="btn btn-success">
                Скачать Excel
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title">Общий доход</h5>
                        <p class="card-text h4 text-success">{{ number_format($softProfit, 0, ',', ' ') }} сум
                            /{{ $softProfitUsd, 0, ',', ' ' }} $</p>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body">
                        <h5 class="card-title">Касса</h5>
                        <p class="card-text h4 text-info">{{ number_format($netCash, 0, ',', ' ') }} сум</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <h5 class="card-title">Незавершенные займы (Выдано)</h5>
                        <p class="card-text h4 text-warning">{{ number_format($loanTotals['given'], 0, ',', ' ') }} сум</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="card-title">Незавершенные займы (Получено)</h5>
                        <p class="card-text h4 text-danger">{{ number_format($loanTotals['taken'], 0, ',', ' ') }} сум</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            @php
                $consumeTotal = $counts['consume'] + $counts['loan'];
                $intakeTotal = $counts['intake'] + $counts['intake_loan'];
                $returnTotal = $counts['return'] + $counts['intake_return'];
            @endphp
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title">Расходы</h5>
                        <p class="card-text h4 text-primary">{{ $consumeTotal }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-secondary">
                    <div class="card-body">
                        <h5 class="card-title">Поступления</h5>
                        <p class="card-text h4 text-secondary">{{ $intakeTotal }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-dark">
                    <div class="card-body">
                        <h5 class="card-title">Возвраты</h5>
                        <p class="card-text h4 text-dark">{{ $returnTotal }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive bg-white rounded p-3 shadow-sm">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Дата</th>
                        <th>Тип</th>
                        <th>Поставщик</th>
                        <th>Сумма</th>
                        <th>Займ</th>
                        <th>Продукты</th>
                        <th>Примечание</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $index => $activity)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $activity->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                @php

                                    $typeRu = match ($activity->type) {
                                        'consume' => 'Продажа',
                                        'loan' => 'Долг',
                                        'return' => 'Возврат',
                                        'intake' => 'Поступление',
                                        'intake_loan' => 'Поступление (в долг)',
                                        'intake_return' => 'Возврат поставщику',
                                        default => $activity->type,
                                    };
                                @endphp
                                {{ strtoupper($typeRu) }}
                            </td>
                            <td>{{ $activity->supplier->name ?? '-' }}</td>
                            <td>{{ number_format($activity->total_price, 0, ',', ' ') }}/{{ $activity->total_usd }}</td>
                            <td>
                                @if (in_array($activity->type, ['loan', 'intake_loan']))
                                    {{ $activity->loan_amount ?? 0 }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <ul class="mb-0">
                                    @foreach ($activity->items as $item)
                                        <li>{{ $item->product->name }} x{{ $item->qty }} {{ $item->unit }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $activity->note }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Нет данных за выбранный период.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
