@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title card-title-dash">Sales & Inventory Report</h4>
                    </div>
                    <div class="d-sm-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle text-dark" type="button" id="filterDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-filter-outline"></i> Filter
                                </button>
                                <div class="dropdown-menu p-3 shadow" style="min-width:300px;" aria-labelledby="filterDropdown">
                                    <form method="GET" action="{{ route('admin.reports.index') }}">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label">Date Range</label>
                                            <div class="input-group">
                                                <input type="date" name="start_date" class="form-control" 
                                                    value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                                                <span class="input-group-text">to</span>
                                                <input type="date" name="end_date" class="form-control" 
                                                    value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Transaction Type</label>
                                            <select name="type" class="form-select">
                                                @foreach($transactionTypes as $key => $label)
                                                <option value="{{ $key }}" {{ $selectedType == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-filter"></i> Apply Filters
                                            </button>
                                            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Revenue</h6>
                                <h3 class="card-text">{{ number_format($totalRevenue) }} UZS</h3>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">vs previous period</span>
                                    <i class="mdi mdi-arrow-{{ $revenueChange >= 0 ? 'up text-success' : 'down text-danger' }}"></i>
                                    <span class="{{ $revenueChange >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($revenueChange), 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Cost</h6>
                                <h3 class="card-text">{{ number_format($totalCost) }} UZS</h3>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">vs previous period</span>
                                    <i class="mdi mdi-arrow-{{ $costChange >= 0 ? 'up text-success' : 'down text-danger' }}"></i>
                                    <span class="{{ $costChange >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($costChange), 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Profit</h6>
                                <h3 class="card-text">{{ number_format($totalProfit) }} UZS</h3>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">vs previous period</span>
                                    <i class="mdi mdi-arrow-{{ $profitChange >= 0 ? 'up text-success' : 'down text-danger' }}"></i>
                                    <span class="{{ $profitChange >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($profitChange), 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title card-title-dash mb-4">Daily Profit Trend</h4>
                        <canvas id="profitChart" height="250"></canvas>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title card-title-dash mb-4">Transaction Details</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Profit</th>
                                        <th>Client Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->created_at->format('d-m-Y H:i') }}</td>
                                        <td>
                                            @if($transaction->product)
                                            {{ $transaction->product->name }}
                                            @else
                                            <span class="text-danger">Product deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $typeBadge = match($transaction->type) {
                                                    'consume' => ['bg-primary', 'Sale'],
                                                    'intake' => ['bg-info', 'Purchase'],
                                                    'return' => ['bg-warning', 'Customer Return'],
                                                    'loan' => ['bg-secondary', 'Customer Credit'],
                                                    'intake_return' => ['bg-warning text-dark', 'Supplier Return'],
                                                    'intake_loan' => ['bg-secondary', 'Supplier Credit'],
                                                    default => ['bg-dark', $transaction->type]
                                                };
                                            @endphp
                                            <span class="badge {{ $typeBadge[0] }}">{{ $typeBadge[1] }}</span>
                                        </td>
                                        <td>{{ $transaction->qty }}</td>
                                        <td>
                                            @if($transaction->product)
                                            {{ number_format($transaction->product->price_uzs) }} UZS
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>{{ number_format($transaction->total_price) }} UZS</td>
                                        <td class="{{ $transaction->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($transaction->profit) }} UZS
                                        </td>
                                        <td>{{ $transaction->client_phone ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <div class="pagination">
                                {{ $transactions->links('pagination::bootstrap-4') }}
                            </div>
                            <p class="text-muted">
                                Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of
                                {{ $transactions->total() }} entries
                            </p>
                            <div class="d-flex justify-content-between gap-3 text-muted">
                                <a href="{{ route('admin.reports.export', request()->query()) }}" class="text-decoration-none">
                                    <i class="mdi mdi-download"></i> Export
                                </a>
                                <a href="javascript:window.print()" class="text-decoration-none">
                                    <i class="mdi mdi-printer"></i> Print
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('profitChart').getContext('2d');
        
        const labels = @json($chartData['labels']);
        const revenueData = @json($chartData['revenue']);
        const costData = @json($chartData['cost']);
        const profitData = @json($chartData['profit']);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue (UZS)',
                        data: revenueData,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Cost (UZS)',
                        data: costData,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Profit (UZS)',
                        data: profitData,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        type: 'line',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toLocaleString() + ' UZS';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' UZS';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection