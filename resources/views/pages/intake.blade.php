@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Добавление прихода товаров</h4>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="{{ route('intake.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Поиск по названию или штрих-коду" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Поиск</button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Ед. изм.</th>
                                <th>Цена</th>
                                <th>Кол-во</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td>{{ number_format($product->price_uzs, 2) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('intake.add') }}" class="d-flex">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="unit" value="{{ $product->unit }}">
                                            <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="0.001" step="0.001" style="width: 80px;">
                                            <input type="number" name="price" class="form-control form-control-sm ms-2" value="{{ $product->price_uzs }}" min="0" step="0.01" style="width: 100px;">
                                            <button type="submit" class="btn btn-sm btn-success ms-2">
                                                <i class="mdi mdi-plus"></i> Добавить
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $products->links() }}
                </div>
            </div>
            <div class="col-md-4">
                <form method="POST" action="{{ route('intake.store') }}">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h5>Список прихода</h5>
                        </div>
                        <div class="card-body">
                            @if(count($intakes = session('intakes', [])) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Товар</th>
                                                <th>Кол-во</th>
                                                <th>Цена</th>
                                                <th>Сумма</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total = 0; @endphp
                                            @foreach($intakes as $index => $item)
                                                <tr>
                                                    <td>{{ $item['name'] }}</td>
                                                    <td>{{ $item['quantity'] }} {{ $item['unit'] }}</td>
                                                    <td>{{ number_format($item['price'], 2) }}</td>
                                                    <td>{{ number_format($item['total'], 2) }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('intake.remove', $index) }}" class="btn btn-sm btn-danger">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @php $total += $item['total']; @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3">Итого:</th>
                                                <th colspan="2">{{ number_format($total, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                @foreach($intakes as $index => $item)
                                    <input type="hidden" name="product_id[]" value="{{ $item['product_id'] }}">
                                    <input type="hidden" name="unit[]" value="{{ $item['unit'] }}">
                                    <input type="hidden" name="quantity[]" value="{{ $item['quantity'] }}">
                                    <input type="hidden" name="price[]" value="{{ $item['price'] }}">
                                @endforeach

                                <div class="mb-3">
                                    <label class="form-label">Тип прихода</label>
                                    <select name="type" class="form-select">
                                        <option value="intake">Обычный приход</option>
                                        <option value="intake_loan">Приход в долг</option>
                                        <option value="intake_return">Возврат от клиента</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Тип оплаты</label>
                                    <select name="payment_type" class="form-select">
                                        <option value="cash">Наличные</option>
                                        <option value="card">Карта</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Поставщик</label>
                                    <select name="supplier_id" class="form-select">
                                        <option value="">Без поставщика</option>
                                        {{-- @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Примечание</label>
                                    <textarea name="note" class="form-control" rows="2"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="mdi mdi-content-save"></i> Сохранить приход
                                </button>
                            @else
                                <div class="alert alert-info text-center">
                                    Нет добавленных товаров
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection