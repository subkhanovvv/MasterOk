<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Дата транзакции</th>
                <th>Тип</th>
                <th>Сумма</th>
                <th>Товары</th>
                <th>Оплата</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
                @php
                    $paymentRu = match ($t->payment_type) {
                        'cash' => 'Наличные',
                        'card' => 'Карта',
                        'bank_transfer' => 'Банковский перевод',
                        default => $t->payment_type,
                    };

                    $typeRu = match ($t->type) {
                        'consume' => 'Продажа',
                        'loan' => 'Долг',
                        'return' => 'Возврат',
                        'intake' => 'Поступление',
                        'intake_loan' => 'Поступление (в долг)',
                        'intake_return' => 'Возврат поставщику',
                        default => $t->type,
                    };

                    $color =
                        $t->status === 'complete'
                            ? 'success'
                            : ($t->status === 'incomplete'
                                ? 'warning'
                                : 'danger');

                    $statusRu = match ($t->status) {
                        'complete' => 'Завершен',
                        'incomplete' => 'Не завершен',
                        default => $t->status,
                    };
                @endphp

                <tr>
                    <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                    <td>{{ $t->created_at }}</td>
                    <td>{{ $typeRu }}</td>
                    <td>{{ $t->total_price }} сум</td>
                    <td>{{ $t->items_count }} товаров</td>
                    <td>{{ $paymentRu }}</td>
                    <td>
                        @if ($t->status === 'incomplete')
                            <form method="POST" action="{{ route('history.updateStatus', $t->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="badge badge-warning">
                                    <option value="incomplete" selected>Не завершен</option>
                                    <option value="complete">Завершен</option>
                                </select>
                            </form>
                        @else
                            <span class="badge badge-{{ $color }}">
                                {{ $statusRu }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <a href="javascript:void(0);" title="Детали" data-bs-toggle="modal"
                            data-bs-target="#transactionDetailsModal" class="text-decoration-none"
                            data-id="{{ $t->id }}" data-created_at="{{ $t->created_at }}"
                            data-updated_at="{{ $t->updated_at }}" data-type="{{ $t->type }}"
                            data-client_name="{{ $t->client_name ?? ($t->supplier?->brand?->name ?? 'N/A') }}"
                            data-client_phone="{{ $t->client_phone ?? ($t->supplier?->brand?->phone ?? 'N/A') }}"
                            data-status="{{ $t->status }}" data-loan_direction="{{ $t->loan_direction ?? '-' }}"
                            data-total_price="{{ $t->total_price }}" data-payment_type="{{ $t->payment_type }}"
                            data-loan_due_to="{{ $t->loan_due_to ?? '-' }}"
                            data-return_reason="{{ $t->return_reason ?? '-' }}" data-note="{{ $t->note ?? '-' }}"
                            data-supplier="{{ $t->supplier ? $t->supplier->name : '-' }}"
                            data-qr_code="{{ asset('storage/' . $t->qr_code) }}"
                            data-items="{{ $t->items->map(function ($item) {
                                    return [
                                        'product_name' => $item->product->name ?? '-',
                                        'qty' => $item->qty,
                                        'unit' => $item->product->unit,
                                        'price' => $item->product->sale_price,
                                    ];
                                })->toJson() }}">
                            <i class="mdi mdi-eye icon-sm text-success"></i>
                        </a>
                        <a onclick="printTransactionCheque({{ $t->id }})" title="Печать"
                            class="text-decoration-none">
                            <i class="mdi mdi-printer icon-sm"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">История пуста</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
