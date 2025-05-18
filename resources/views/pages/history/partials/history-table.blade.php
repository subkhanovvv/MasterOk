<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Data transaction</th>
                <th>type</th>
                <th>total amount</th>
                <th>products</th>
                <th>payment</th>
                <th>status</th>
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                    <td>{{ $t->created_at }}</td>
                    <td>{{ $t->type }}</td>
                    <td>{{ $t->total_price }} uzs</td>
                    <td>{{ $t->items_count }} товаров</td>
                    <td>{{ $t->payment_type }}</td>
                    <td>
                        @php
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
                        <a href="javascript:void(0);" title="View Details" data-bs-toggle="modal"
                            data-bs-target="#transactionDetailsModal" class="text-decoration-none"
                            data-id="{{ $t->id }}" data-created_at="{{ $t->created_at }}"
                            data-updated_at="{{ $t->updated_at }}" data-type="{{ $t->type }}"
                            data-client_name="{{ $t->client_name ?? '-' }}"
                            data-client_phone="{{ $t->client_phone ?? '-' }}" data-status="{{ $t->status }}"
                            data-loan_direction="{{ $t->loan_direction ?? '-' }}"
                            data-total_price="{{ $t->total_price }}" data-payment_type="{{ $t->payment_type }}"
                            data-paid_amount="{{ $t->paid_amount ?? '-' }}"
                            data-loan_due_to="{{ $t->loan_due_to ?? '-' }}"
                            data-return_reason="{{ $t->return_reason ?? '-' }}" data-note="{{ $t->note ?? '-' }}"
                            data-supplier="{{ $t->supplier ? $t->supplier->name : '-' }}"
                            data-qr_code="{{ asset($t->qr_code) }}"
                            data-items="{{ $t->items->map(function ($item) {
                                    return [
                                        'product_name' => $item->product->name ?? '-',
                                        'qty' => $item->qty,
                                        'unit' => $item->unit,
                                        'price' => $item->price,
                                    ];
                                })->toJson() }}">
                            <i class="mdi mdi-eye icon-sm text-success"></i>
                        </a>

                        <a href="javascript:void(0);" title="Редактировать" class="text-decoration-none">
                            <i class="mdi mdi-pencil icon-sm text-primary"></i>
                        </a>
                        <a onclick="printTransactionCheque({{ $t->id }})" title="print"
                            class="text-decoration-none">
                            <i class="mdi mdi-printer icon-sm"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Нет history</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
