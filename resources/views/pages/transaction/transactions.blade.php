@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="card-title card-title-dash">Последние транзакции</h4>
                </div>
                <div class="d-sm-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle text-dark" type="button" id="filterDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-filter-outline"></i> Фильтр
                            </button>
                            <div class="dropdown-menu p-3 shadow" style="min-width:300px;" aria-labelledby="filterDropdown">
                                <form method="GET" action="{{ route('transactions') }}">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label">Период</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">По</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Тип операции</label>
                                        <select name="type" class="form-select">
                                            <option value="">Все типы</option>
                                            @foreach(['consume', 'intake', 'return', 'loan', 'intake_return', 'intake_loan'] as $type)
                                                @php
                                                    $typeRu = match ($type) {
                                                        'consume' => 'Расход',
                                                        'intake' => 'Приход',
                                                        'return' => 'Возврат клиента',
                                                        'loan' => 'Долг клиента',
                                                        'intake_return' => 'Возврат поставщику',
                                                        'intake_loan' => 'Долг поставщику',
                                                        default => $type,
                                                    };
                                                @endphp
                                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                    {{ $typeRu }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Товар</label>
                                        <select name="product_id" class="form-select">
                                            <option value="">Все товары</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-filter"></i> Применить
                                        </button>
                                        <a href="{{ route('transactions') }}" class="btn btn-sm btn-outline-secondary">Сброс</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Дата транзакции</th>
                            <th>Товар</th>
                            <th>Сумма</th>
                            <th>Тип</th>
                            <th>Количество</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td title="{{ $transaction->created_at->format('H:i:s') }}">
                                    {{ $transaction->created_at->format('d.m.Y') }}
                                </td>
                                <td>{{ $transaction->product->name ?? 'Удаленный товар' }}</td>
                                <td>{{ number_format($transaction->total_price, 0, ',', ' ') }} UZS</td>
                                <td>
                                    @php
                                        $typeRu = match ($transaction->type) {
                                            'consume' => 'Расход',
                                            'intake' => 'Приход',
                                            'return' => 'Возврат клиента',
                                            'loan' => 'Долг клиента',
                                            'intake_return' => 'Возврат поставщику',
                                            'intake_loan' => 'Долг поставщику',
                                            default => $transaction->type,
                                        };
                                        $typeClass = match ($transaction->type) {
                                            'consume', 'loan', 'intake_return' => 'text-danger',
                                            'intake', 'return', 'intake_loan' => 'text-success',
                                            default => '',
                                        };
                                    @endphp
                                    <span class="{{ $typeClass }}">{{ $typeRu }}</span>
                                </td>
                                <td>{{ $transaction->qty }} {{ $transaction->unit }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="javascript:void(0);" onclick="showTransactionDetailsModal(@json($transaction))" 
                                           class="btn btn-sm btn-icon btn-outline-primary" title="Просмотр">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ route('transactions', $transaction->id) }}" 
                                           class="btn btn-sm btn-icon btn-outline-success" title="Печать" target="_blank">
                                            <i class="mdi mdi-printer"></i>
                                        </a>
                                        <a href="{{ route('transactions', $transaction->id) }}" 
                                           class="btn btn-sm btn-icon btn-outline-info" title="Скачать">
                                            <i class="mdi mdi-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Нет данных о транзакциях</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="mb-2 mb-md-0">
                    <p class="text-muted mb-0">
                        Показано с {{ $transactions->firstItem() }} по {{ $transactions->lastItem() }} из {{ $transactions->total() }} записей
                    </p>
                </div>
                <div class="pagination mb-2 mb-md-0">
                    {{ $transactions->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('transactions') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-download me-1"></i> Экспорт
                    </a>
                    <a href="{{ route('transactions') }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                        <i class="mdi mdi-printer me-1"></i> Печать
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- @include('pages.transaction.modals.details') --}}
    
    @push('scripts')
    <script>
        function showTransactionDetailsModal(transaction) {
            // Format dates
            const createdAt = new Date(transaction.created_at).toLocaleString('ru-RU');
            
            // Update modal content
            document.getElementById('td_id').textContent = transaction.id;
            document.getElementById('td_product').textContent = transaction.product?.name || 'Удаленный товар';
            document.getElementById('td_type').textContent = getTypeName(transaction.type);
            document.getElementById('td_qty').textContent = `${transaction.qty} ${transaction.unit}`;
            document.getElementById('td_total_price').textContent = formatPrice(transaction.total_price);
            document.getElementById('td_paid_amount').textContent = formatPrice(transaction.paid_amount);
            document.getElementById('td_return_reason').textContent = transaction.return_reason || '-';
            document.getElementById('td_client').textContent = transaction.client_name 
                ? `${transaction.client_name} (${transaction.client_phone || 'нет телефона'})` 
                : '-';
            document.getElementById('td_created_at').textContent = createdAt;
            document.getElementById('td_note').textContent = transaction.note || '-';
            
            // QR code handling
            const qrCodeContainer = document.getElementById('qrCodePreview');
            if (transaction.qr_code) {
                qrCodeContainer.innerHTML = `<img src="/storage/${transaction.qr_code}" alt="QR Code" class="img-fluid">`;
            } else {
                qrCodeContainer.innerHTML = '<p class="text-muted">QR код отсутствует</p>';
            }
            
            // Toggle optional fields
            document.getElementById('td_return_reason_row').style.display = transaction.return_reason ? '' : 'none';
            document.getElementById('td_client_row').style.display = transaction.client_name ? '' : 'none';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
            modal.show();
        }
        
        function getTypeName(type) {
            const types = {
                'consume': 'Расход',
                'intake': 'Приход',
                'return': 'Возврат клиента',
                'loan': 'Долг клиента',
                'intake_return': 'Возврат поставщику',
                'intake_loan': 'Долг поставщику'
            };
            return types[type] || type;
        }
        
        function formatPrice(amount) {
            return amount ? new Intl.NumberFormat('ru-RU').format(amount) + ' UZS' : '-';
        }
    </script>
    @endpush
@endsection