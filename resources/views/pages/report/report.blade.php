@extends('layouts.admin')

@section('content')
<style>
    *{
         cursor: pointer;
    }
</style>
    <div class="card mb-3 border-0">
        <div class="card-body mb-3">
            <h5 class="card-title card-title-dash">Отчет</h5>
            <form method="GET" class="row justify-content-center g-3">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label for="start_date" class="form-label">Дата начала</label>
                    <input type="date" name="start_date" class="form-control" style="height: 43px"
                        value="{{ $start }}">
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label for="end_date" class="form-label">Дата окончания</label>
                    <input type="date" name="end_date" class="form-control" style="height: 43px"
                        value="{{ $end }}">
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Показать</button>
                    <a href="{{ route('report.index') }}" class="btn btn-secondary">Сбросить</a>
                </div>
            </form>
        </div>
        <div class="row card-body">
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title">доход</h5>
                        <p class="card-text h4 text-primary">{{ number_format($softProfit, 0, ',', ' ') }} сум</p>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title">Касса</h5>
                        <p class="card-text h4 text-primary">{{ number_format($netCash, 0, ',', ' ') }} сум</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">займы (Выдано)</h5>
                        <p class="card-text h4 text-danger">{{ number_format($loanTotals['given'], 0, ',', ' ') }} сум</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="card-title">займы (Получено)</h5>
                        <p class="card-text h4 text-danger">{{ number_format($loanTotals['taken'], 0, ',', ' ') }} сум</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4 card-body">
            <div id="marketingOverview-legend"></div>
            <div class="chartjs-bar-wrapper mt-3">
                <canvas id="marketingOverviewchart" style="height: 200px; max-height:200px;"></canvas>
            </div>
        </div>
    </div>



    <div class="card mb-3 border-0">
        <div class="card-body">
            <h5 class="card-title">Детали по операциям</h5>
            <div class="row g-3 p-0">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label class="form-label small">Сторона операции:</label>
                    <select name="side" class="form-select">
                        <option value="">Все</option>
                        <option value="consume" {{ request('side') == 'consume' ? 'selected' : '' }}>Расход</option>
                        <option value="intake" {{ request('side') == 'intake' ? 'selected' : '' }}>Поступление
                        </option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                    <a href="{{ route('report.index') }}" class="btn btn-success">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                    <a href="{{ route('report.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                        class="btn btn-primary">
                        <i class="mdi mdi-download"></i> Pdf
                    </a>
                    <a href="{{ route('report.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
                        class="btn btn-success">
                        <i class="mdi mdi-download"></i> Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0">
        <div class="table-responsive card-body">
            <table class="table table-hover mt-3">
                <thead>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = @json($activityTypeCounts);
        const ctx = document.getElementById('marketingOverviewchart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: Object.keys(chartData).map(type => ({
                    'consume': 'Расход',
                    'loan': 'Займ',
                    'return': 'Возврат',
                    'intake': 'Приход',
                    'intake_loan': 'Приход (займ)',
                    'intake_return': 'Приход (возврат)'
                } [type] || type)),
                datasets: [{
                    label: 'Количество операций',
                    data: Object.values(chartData),
                    backgroundColor: [
                        '#4FC3F7', // Skyblue
                        '#2196F3', // Primary blue
                        '#64B5F6',
                        '#1976D2',
                        '#90CAF9',
                        '#42A5F5'
                    ],
                    borderRadius: 6,
                    barThickness: 30, // Smaller bar width
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => `${context.parsed.y} шт`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => value + ' шт'
                        }
                    }
                }
            }
        });
    </script>
@endsection
