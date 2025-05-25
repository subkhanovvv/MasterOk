@extends('layouts.admin')

@section('content')
    <div class="col-sm-12">
        <div class="home-tab">
            <div class="tab-content tab-content-basic">
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                    <div class="row">
                        <div class="col-lg-8 d-flex flex-column">
                            <div class="row flex-grow">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="d-sm-flex justify-content-between align-items-start">
                                                <div>
                                                    <h4 class="card-title card-title-dash">Касса на сегодня</h4>
                                                    <p class="card-subtitle card-subtitle-dash">
                                                        Общая сумма поступлений за текущий день по всем типам операций
                                                    </p>
                                                </div>
                                                <div>
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-light dropdown-toggle toggle-dark btn-lg mb-0 me-0"
                                                            type="button" id="dropdownMenuButton2"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            {{ $day === 'yesterday' ? 'Вчера' : 'Сегодня' }}
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                                            <a class="dropdown-item"
                                                                href="{{ route('index', ['day' => 'today']) }}">Сегодня</a>
                                                            <a class="dropdown-item"
                                                                href="{{ route('index', ['day' => 'yesterday']) }}">Вчера</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="d-sm-flex align-items-center mt-1 justify-content-between">
                                                <div class="d-sm-flex align-items-center mt-4">
                                                    <h2 class="me-2 fw-bold">{{ number_format($netCash, 2, '.', ' ') }}</h2>
                                                    <h4 class="me-2">сум</h4>

                                                </div>
                                                <div class="me-3">
                                                    <div id="marketingOverview-legend"></div>
                                                </div>
                                            </div>

                                            <div class="chartjs-bar-wrapper mt-3">
                                                <canvas id="marketingOverviewchart"
                                                    style="height: 200px; max-height:200px;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row flex-grow">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="d-sm-flex justify-content-between align-items-start">
                                                <div>
                                                    <h4 class="card-title card-title-dash">Незавершённые займы</h4>
                                                    <p class="card-subtitle card-subtitle-dash">Займы с просрочкой или
                                                        сроком оплаты на этой неделе
                                                    </p>
                                                </div>
                                                <div>
                                                    <a href="{{ route('history.index', array_merge(request()->all(), ['loan_filter' => 'all', 'status' => 'incomplete'])) }}"
                                                        class="fw-bold text-primary">Показать все<i
                                                            class="mdi mdi-arrow-right ms-2"></i></a>
                                                </div>
                                            </div>
                                            <div class="table-responsive  mt-1">
                                                <table class="table select-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Имя</th>
                                                            <th>Телефон</th>
                                                            <th>Заем</th>
                                                            <th>Сумма Заемa</th>
                                                            <th>Дата окончания</th>
                                                            <th>Действие</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($activities as $a)
                                                            <tr>
                                                                <td>
                                                                    {{ $a->client_name ?? ($a->supplier?->brand?->name ?? 'N/A') }}
                                                                </td>
                                                                <td>
                                                                    {{ $a->client_phone ?? ($a->supplier?->brand?->phone ?? 'N/A') }}
                                                                </td>
                                                                <td>
                                                                    @if ($a->loan_direction === 'given')
                                                                        Выдан
                                                                    @elseif($a->loan_direction === 'taken')
                                                                        Получен
                                                                    @else
                                                                        —
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ $a->loan_amount ?? 'не выбрано' }}
                                                                </td>
                                                                <td>
                                                                    {{ $a->loan_due_to ?? 'не выбрано' }}
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $color =
                                                                            $a->status === 'complete'
                                                                                ? 'success'
                                                                                : ($a->status === 'incomplete'
                                                                                    ? 'warning'
                                                                                    : 'danger');

                                                                        $statusRu = match ($a->status) {
                                                                            'complete' => 'Завершен',
                                                                            'incomplete' => 'Не завершен',
                                                                            default => $a->status,
                                                                        };
                                                                    @endphp

                                                                    @if ($a->status === 'incomplete')
                                                                        <form method="POST"
                                                                            action="{{ route('history.updateStatus', $a->id) }}"
                                                                            class="d-inline">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <select name="status"
                                                                                onchange="this.form.submit()"
                                                                                class="badge badge-warning">
                                                                                <option value="incomplete" selected>Не
                                                                                    завершен</option>
                                                                                <option value="complete">Завершен</option>
                                                                            </select>
                                                                        </form>
                                                                    @else
                                                                        <span class="badge badge-{{ $color }}">
                                                                            {{ $statusRu }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center py-4">
                                                                    Нет незавершённых
                                                                    займов
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
                        <div class="col-lg-4 d-flex flex-column">
                            <div class="row flex-grow">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card card-rounded">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h4 class="card-title card-title-dash">Топ товары</h4>
                                                    </div>
                                                    <div>
                                                        <canvas id="topProductsChart"></canvas>
                                                    </div>
                                                    <div id="doughnutChart-legend" class="mt-5 text-center"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Top 5 Consumed Products (Doughnut Chart)
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(topProductsCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    data: {!! json_encode($data) !!},
                    backgroundColor: ['#4caf50', '#2196f3', '#f44336', '#ff9800', '#9c27b0'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Топ потреблённых продуктов (7 дней)'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' шт';
                            }
                        }
                    }
                }
            }
        });

        // Касса по операциям за сегодня (consume/loan/return)
        const chartData = @json($activityTypeCounts);
        const ctx = document.getElementById('marketingOverviewchart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
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
