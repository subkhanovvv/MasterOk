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
                        <a href="javascript:void(0);" title="view" data-bs-toggle="modal"
                            data-bs-target="#transactionDetailsModal" class="text-decoration-none view-transaction">
                            <i class="mdi mdi-eye icon-sm text-success"></i>
                        </a>
                        <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                            data-bs-target="#editCategoryModal" data-id="{{ $t->id }}"
                            data-name="{{ $t->name }}"
                            data-photo="{{ $t->photo ? Storage::url($c->photo) : asset('admin/assets/images/default_product.png') }}"
                            onclick="openModal(this)" class="text-decoration-none">
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
