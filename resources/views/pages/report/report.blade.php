@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h4 class="mb-0 text-primary fw-bold">Отчет по деятельности</h4>
        </div>
        
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="start_date" class="form-label text-muted small mb-1">Дата начала</label>
                    <input type="date" name="start_date" class="form-control form-control-lg" value="{{ $start }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label text-muted small mb-1">Дата окончания</label>
                    <input type="date" name="end_date" class="form-control form-control-lg" value="{{ $end }}">
                </div>
                <div class="col-md-3">
                    <label for="brand_id" class="form-label text-muted small mb-1">Бренд</label>
                    <select name="brand_id" class="form-select form-select-lg">
                        <option value="">Все бренды</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-filter me-2"></i>Показать
                    </button>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex gap-2">
                    <a href="{{ route('report.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                       class="btn btn-danger px-4">
                        <i class="fas fa-file-pdf me-2"></i>PDF
                    </a>
                    <a href="{{ route('report.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                       class="btn btn-success px-4">
                        <i class="fas fa-file-excel me-2"></i>Excel
                    </a>
                </div>
                <div class="text-muted small">
                    Отчет сформирован: {{ now()->format('d.m.Y H:i') }}
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="row mb-4 g-4">
                <div class="col-md-3">
                    <div class="card border-start border-4 border-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Общий доход</h6>
                                    <h3 class="mb-0 text-success">{{ number_format($softProfit, 0, ',', ' ') }} <small class="fs-6">сум</small></h3>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-wallet text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-start border-4 border-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Касса</h6>
                                    <h3 class="mb-0 text-info">{{ number_format($netCash, 0, ',', ' ') }} <small class="fs-6">сум</small></h3>
                                </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-cash-register text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-start border-4 border-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Выдано займов</h6>
                                    <h3 class="mb-0 text-warning">{{ number_format($loanTotals['given'], 0, ',', ' ') }} <small class="fs-6">сум</small></h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-hand-holding-usd text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-start border-4 border-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Получено займов</h6>
                                    <h3 class="mb-0 text-danger">{{ number_format($loanTotals['taken'], 0, ',', ' ') }} <small class="fs-6">сум</small></h3>
                                </div>
                                <div class="bg-danger bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-hand-holding text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Count Cards -->
            <div class="row mb-4 g-4">
                @php
                    $consumeTotal = $counts['consume'] + $counts['loan'];
                    $intakeTotal = $counts['intake'] + $counts['intake_loan'];
                    $returnTotal = $counts['return'] + $counts['intake_return'];
                @endphp
                <div class="col-md-4">
                    <div class="card border-start border-4 border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Расходы</h6>
                                    <h3 class="mb-0 text-primary">{{ $consumeTotal }}</h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-arrow-up text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-start border-4 border-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Поступления</h6>
                                    <h3 class="mb-0 text-secondary">{{ $intakeTotal }}</h3>
                                </div>
                                <div class="bg-secondary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-arrow-down text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-start border-4 border-dark h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Возвраты</h6>
                                    <h3 class="mb-0 text-dark">{{ $returnTotal }}</h3>
                                </div>
                                <div class="bg-dark bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-exchange-alt text-dark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activities Table -->
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-4 text-uppercase small fw-bold">#</th>
                                    <th class="py-3 px-4 text-uppercase small fw-bold">Дата</th>
                                    <th class="py-3 px-4 text-uppercase small fw-bold">Тип</th>
                                    <th class="py-3 px-4 text-uppercase small fw-bold">Поставщик</th>
                                    <th class="py-3 px-4 text-uppercase small fw-bold text-end">Сумма</th>
                                    <th class="py-3 px-4 text-uppercase small fw-bold text-end">Займ</th>
                                    <th class="py-3 px-4 text-uppercase small fw-bold">Продукты</th>
                                    <th class="py-3 px-4 text-uppercase small fw-bold">Примечание</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($activities as $index => $activity)
                                    <tr class="align-middle">
                                        <td class="px-4">{{ $index + 1 }}</td>
                                        <td class="px-4">
                                            <div class="text-nowrap">{{ $activity->created_at->format('d.m.Y') }}</div>
                                            <div class="text-muted small">{{ $activity->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-4">
                                            <span class="badge 
                                                @if($activity->type === 'consume') bg-danger bg-opacity-10 text-danger
                                                @elseif($activity->type === 'intake') bg-success bg-opacity-10 text-success
                                                @elseif($activity->type === 'return') bg-dark bg-opacity-10 text-dark
                                                @elseif(str_contains($activity->type, 'loan')) bg-warning bg-opacity-10 text-warning
                                                @endif p-2 text-uppercase">
                                                {{ $activity->type }}
                                            </span>
                                        </td>
                                        <td class="px-4">
                                            {{ $activity->supplier->name ?? '-' }}
                                        </td>
                                        <td class="px-4 text-end fw-bold">
                                            {{ number_format($activity->total_price, 0, ',', ' ') }}
                                        </td>
                                        <td class="px-4 text-end">
                                            @if(in_array($activity->type, ['loan', 'intake_loan']))
                                                <span class="badge bg-warning bg-opacity-20 text-warning p-2">
                                                    {{ $activity->loan_amount ?? 0 }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4">
                                            <ul class="mb-0 list-unstyled">
                                                @foreach($activity->items as $item)
                                                    <li class="mb-1">
                                                        <span class="d-inline-block bg-light rounded px-2 py-1 small">
                                                            {{ $item->product->name }} ×{{ $item->quantity }} {{ $item->unit }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-4 small text-muted">
                                            {{ $activity->note ?: '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-database me-2"></i>Нет данных за выбранный период
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection