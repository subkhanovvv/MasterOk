@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Profit Report</h5>
                        <div class="d-flex">
                            <button id="downloadBtn" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-download me-1"></i> Download
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $endDate }}">
                            </div>
                            <div class="col-md-2">
                                <label for="brand_id" class="form-label">Brand</label>
                                <select class="form-select" id="brand_id" name="brand_id">
                                    <option value="">All Brands</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="loan_direction" class="form-label">Direction</label>
                                <select class="form-select" id="loan_direction" name="loan_direction">
                                    <option value="">All</option>
                                    <option value="given" {{ $loanDirection == 'given' ? 'selected' : '' }}>Given</option>
                                    <option value="taken" {{ $loanDirection == 'taken' ? 'selected' : '' }}>Taken</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="loan" {{ $type == 'loan' ? 'selected' : '' }}>Loans</option>
                                    <option value="consume" {{ $type == 'consume' ? 'selected' : '' }}>Consume</option>
                                    <option value="return" {{ $type == 'return' ? 'selected' : '' }}>Returns</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Top Selling Products</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total Profit (without tax)</span>
                            <span class="fw-bold">{{ number_format($totalProfit, 2) }} UZS</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Date Range</span>
                            <span class="fw-bold">{{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} -
                                {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total Transactions</span>
                            <span class="fw-bold">{{ $activities->count() }}</span>
                        </div>
                        @if ($brandId)
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Brand</span>
                                <span class="fw-bold">{{ $brands->find($brandId)->name ?? 'All' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Transaction Details</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>Phone</th>
                                        <th>Type</th>
                                        <th>Direction</th>
                                        <th>Products</th>
                                        <th class="text-end">Amount (UZS)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                        <tr>
                                            <td>{{ $activity->created_at->format('M d, Y H:i') }}</td>
                                            <td>{{ $activity->client_name ?? 'N/A' }}</td>
                                            <td>{{ $activity->client_phone ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $activity->type == 'loan' ? 'primary' : ($activity->type == 'return' ? 'success' : 'warning') }}">
                                                    {{ ucfirst($activity->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($activity->loan_direction)
                                                    <span
                                                        class="badge bg-{{ $activity->loan_direction == 'given' ? 'info' : 'secondary' }}">
                                                        {{ ucfirst($activity->loan_direction) }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($activity->items as $item)
                                                    {{ $item->product->name }} ({{ $item->qty }}),
                                                @endforeach
                                            </td>
                                            <td class="text-end">{{ number_format($activity->total_price, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">No transactions found for the
                                                selected filters.</td>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize chart
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Sales Amount (UZS)',
                        data: @json($chartData['data']),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
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
                                label: function(context) {
                                    return context.parsed.y.toLocaleString() + ' UZS';
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

            // Download button
            document.getElementById('downloadBtn').addEventListener('click', function() {
                // You can implement AJAX call to download endpoint or submit a form
                window.location.href = "{{ route('reports.download') }}?" + new URLSearchParams(
                    new FormData(document.getElementById('filterForm'))).toString();
            });

            // Reset form
            document.querySelector('button[type="reset"]').addEventListener('click', function() {
                window.location.href = "{{ route('report.index') }}";
            });
        });
    </script>
@endpush
