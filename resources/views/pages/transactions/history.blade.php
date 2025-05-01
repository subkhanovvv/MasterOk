@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="card-title card-title-dash">last transactions</h4>
                </div>
                <div class="d-sm-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle text-dark" type="button" id="filterDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-filter-outline"></i> Фильтр
                            </button>
                            <div class="dropdown-menu p-3 shadow" style="min-width:300px;" aria-labelledby="filterDropdown">
                                <form method="POST" action="#">
                                    @csrf
                                    <div class="mb-2">
                                        <input type="text" name="name" class="form-control" placeholder="Название"
                                            value="{{ request('name') }}">
                                    </div>
                                    <div class="mb-2">
                                        <select name="category_id" class="form-select">
                                            <option value="">Все категории</option>
                                            @foreach ($categories as $c)
                                                <option value="{{ $c->id }}"
                                                    {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <select name="brand_id" class="form-select">
                                            <option value="">Все бренды</option>
                                            @foreach ($brands as $b)
                                                <option value="{{ $b->id }}"
                                                    {{ request('brand_id') == $b->id ? 'selected' : '' }}>
                                                    {{ $b->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <select name="status" class="form-select">
                                            <option value="">Все статусы</option>
                                            <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>В
                                                наличии</option>
                                            <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Мало
                                            </option>
                                            <option value="out_of_stock"
                                                {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Нет в наличии
                                            </option>
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-filter"></i> Применить
                                        </button>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">Сброс</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        {{-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProductModal"
                            type="button">
                            <i class="mdi mdi-plus"></i> Add new
                        </button> --}}
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>transaction date</th>
                            <th>type</th>
                            <th>total price</th>
                            <th>product</th>
                            <th>qty</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product_act as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td title="{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}">
                                    {{ \Carbon\Carbon::parse($p->created_at)->format('d-m-Y') }}
                                </td>
                                <td>{{ $p->product->name }}</td>
                                <td>{{ number_format($p->total_price) }} sum / ${{ $p->price_usd }}</td>
                                <td>
                                    @php
                                        $typeRu = match ($p->type) {
                                            'consume' => 'Расход',
                                            'intake' => 'Приход',
                                            'return' => 'Возврат клиента',
                                            'loan' => 'Долг клиента',
                                            'intake_return' => 'Возврат поставщику',
                                            'intake_loan' => 'Долг поставщику',
                                            default => $p->type,
                                        };
                                    @endphp

                                    <span>{{ $typeRu }}</span>

                                </td>
                                {{-- <td>{{ number_format($p->sale_price) }}</td> --}}
                                <td>{{ $p->qty }} {{ $p->unit }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a onclick='showTransactionDetailsModal(@json($p))'>
                                            <i class="mdi mdi-eye">
                                            </i>
                                        </a>
                                        <a href="javascript:void(0);" title="Приход товара" data-bs-toggle="modal"
                                            data-bs-target="#intakeProductModal" data-id="{{ $p->id }}"
                                            data-photo="{{ $p->photo ? Storage::url($p->photo) : asset('admin/assets/images/default_product.png') }}"
                                            data-name="{{ $p->name }}" data-sale_price="{{ $p->sale_price }}"
                                            data-unit="{{ $p->unit }}" onclick="openModal(this)">
                                            <i class="mdi mdi-printer icon-sm text-success"></i>
                                        </a>
                                        <a href="javascript:void(0);" title="Редактировать" data-bs-toggle="modal"
                                            data-bs-target="#editProductModal">
                                            <i class="mdi mdi-download icon-sm text-primary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-between align-items-center mb-3">
                <div class="pagination">
                    {{ $product_act->links('pagination::bootstrap-4') }}
                </div>
                <p class="text-muted">
                    Показаны с {{ $product_act->firstItem() }} по {{ $product_act->lastItem() }} из
                    {{ $product_act->total() }} результатов
                </p>
                <div class="d-flex justify-content-between gap-3 text-muted">
                    <a href="#" class="text-decoration-none"><i class="mdi mdi-download"></i> export</a>
                    <a href="#" class="text-decoration-none">
                        <i class="mdi mdi-printer"></i> print
                    </a>
                </div>
            </div>
        </div>
    </div>
    @include('pages.transactions.modals.cheque')
    <script>
        function showTransactionDetailsModal(transaction) {
            document.getElementById('td_id').textContent = transaction.id;
            document.getElementById('td_product_id').textContent = transaction.product_id;
            document.getElementById('td_type').textContent = transaction.type;
            document.getElementById('td_qty').textContent = transaction.qty;
            document.getElementById('td_total_price').textContent = transaction.total_price;
            document.getElementById('td_paid_amount').textContent = transaction.paid_amount;
            document.getElementById('td_return_reason').textContent = transaction.return_reason ?? '-';
            document.getElementById('td_number').textContent = transaction.client_phone ?? '-';

            if (transaction.qr_code) {
                fetch(`/storage/${transaction.qr_code}`)
                    .then(res => res.text())
                    .then(svg => {
                        document.getElementById('qrCodePreview').innerHTML = svg;
                    });
            }
            if (transaction.return_reason) {
                document.getElementById('td_return_reason_row').style.display = 'block';
            } else {
                document.getElementById('td_return_reason_row').style.display = 'none';
            }
            if (transaction.client_phone) {
                document.getElementById('td_number_row').style.display = 'block';
            } else {
                document.getElementById('td_number_row').style.display = 'none';
            }
            const modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
            modal.show();
        }
    </script>
@endsection
