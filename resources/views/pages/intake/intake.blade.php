@extends('layouts.admin')

@section('content')
    <h1>Создание прихода</h1>

    @if (session('success'))
        <div style="color: green">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('intake.store') }}">
        @csrf

        <!-- Type -->
        <div>
            <label>Тип операции:</label>
            <select name="type">
                <option value="intake" {{ old('type') == 'intake' ? 'selected' : '' }}>Приход</option>
                <option value="intake_loan" {{ old('type') == 'intake_loan' ? 'selected' : '' }}>Приход (Займ)</option>
                <option value="intake_return" {{ old('type') == 'intake_return' ? 'selected' : '' }}>Возврат</option>
            </select>
        </div>

        @if (old('type') == 'intake_loan')
            <div>
                <label>Направление займа:</label>
                <select name="loan_direction">
                    <option value="given">Выдан</option>
                    <option value="taken">Получен</option>
                </select>
            </div>
            <div>
                <label>Имя клиента:</label>
                <input type="text" name="client_name">
            </div>
            <div>
                <label>Телефон клиента:</label>
                <input type="text" name="client_phone">
            </div>
            <div>
                <label>Сумма займа:</label>
                <input type="number" step="0.01" name="loan_amount">
            </div>
            <div>
                <label>Остаток по займу:</label>
                <input type="number" step="0.01" name="loan_due_to">
            </div>
        @elseif(old('type') == 'intake_return')
            <div>
                <label>Причина возврата:</label>
                <input type="text" name="return_reason">
            </div>
        @endif

        <div>
            <label>Тип оплаты:</label>
            <select name="payment_type">
                <option value="cash">Наличные</option>
                <option value="card">Карта</option>
                <option value="bank_transfer">Банковский перевод</option>
            </select>
        </div>

        <div>
            <label>Оплачено:</label>
            <input type="number" step="0.01" name="paid_amount">
        </div>

        <div>
            <label>Заметка:</label>
            <textarea name="note"></textarea>
        </div>

        <hr>
        <h3>Добавить товар</h3>

        <div>
            <label>Товар:</label>
            <select name="product_id">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }} ({{ $product->stock_unit ?? $product->unit }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Количество:</label>
            <input type="number" step="0.01" name="qty" value="1">
        </div>

        <div>
            <button type="submit" formaction="{{ route('intake.addItem') }}">Добавить в список</button>
        </div>

        @if (session('intake_products'))
            <hr>
            <h3>Выбранные товары</h3>
            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Кол-во</th>
                        <th>Ед.изм</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('intake_products') as $productId => $item)
                        @php
                            $product = \App\Models\Product::find($item['product_id']);
                        @endphp
                        <tr>
                            <td>{{ $product->name ?? '—' }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>{{ $product->unit ?? '-' }}</td>
                            <td>
                                <form action="{{ route('intake.incrementItem', $productId) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    <button type="submit">+</button>
                                </form>

                                <form action="{{ route('intake.decrementItem', $productId) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    <button type="submit">−</button>
                                </form>

                                <form action="{{ route('intake.removeItem', $productId) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    <button type="submit">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

            <form method="POST" action="{{ route('intake.clearItems') }}" style="margin-top: 10px;">
                @csrf
                <button type="submit" onclick="return confirm('Вы уверены, что хотите удалить все товары?')">Очистить все
                    товары</button>
            </form>
        @endif

        <hr>
        <button type="submit">Сохранить приход</button>
    </form>
@endsection
