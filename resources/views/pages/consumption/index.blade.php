@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <h4 class="mb-4">Расход продуктов</h4>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('consumption.history') }}" class="btn btn-outline-primary">
                <i class="mdi mdi-history"></i> История расходов
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- LEFT SIDE: Product Search and Add -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('consumption') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Поиск по названию или штрихкоду..." style="height: 45px" value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    </form>

                    <div class="mt-3" id="product-list" style="max-height: 500px; overflow-y: auto;">
                        @forelse($products as $product)
                            <form method="POST" action="{{ route('consumption.add') }}" class="mb-3 consumption-add-form">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="card mb-2 p-2">
                                    <div class="d-flex">
                                        @if($product->photo)
                                            <img src="{{ asset('storage/' . $product->photo) }}"
                                                 class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ $product->name }}</strong><br>
                                            <small class="text-muted">Штрихкод: {{ $product->barcode }}</small><br>
                                            <small>Цена: {{ number_format($product->price_uzs, 2) }} UZS</small><br>
                                            <small>Остаток: {{ $product->qty }} {{ $product->unit }}</small>
                                        </div>
                                    </div>
                                    <div class="mt-2 row g-2">
                                        <div class="col-md-6">
                                            <input type="text" name="unit" value="{{ $product->unit }}"  id="" readonly>
                                           
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Кол-во</label>
                                            <input type="number" name="quantity" class="form-control form-control-sm"
                                                   step="0.001" min="0.001" value="1" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary mt-2 w-100">
                                        <i class="fas fa-plus"></i> Добавить
                                    </button>
                                </div>
                            </form>
                        @empty
                            <div class="alert alert-info text-center">
                                Ничего не найдено
                            </div>
                        @endforelse
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Consumption Table -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>Список расходов</strong>
                </div>
                <div class="card-body" id="consumption-table-container">
                    @include('pages.consumption_table', ['consumptions' => session('consumptions', [])])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function bindConsumptionForms() {
        document.querySelectorAll('.consumption-add-form').forEach(form => {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        updateConsumptionTable(result.table_html);
                        bindConsumptionForms(); // rebind
                    } else {
                        alert(result.message || 'Ошибка при добавлении.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Ошибка сети.');
                }
            });
        });
    }

    function bindRemoveButtons() {
        document.querySelectorAll('.consumption-remove-form').forEach(form => {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const confirmed = confirm('Удалить этот продукт из списка?');
                if (!confirmed) return;

                const formData = new FormData(this);
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        updateConsumptionTable(result.table_html);
                        bindConsumptionForms();
                        bindRemoveButtons();
                    } else {
                        alert(result.message || 'Ошибка при удалении.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Ошибка сети.');
                }
            });
        });
    }

    function updateConsumptionTable(html) {
        document.querySelector('#consumption-table-container').innerHTML = html;
    }

    bindConsumptionForms();
    bindRemoveButtons();
});
</script>
@endsection
