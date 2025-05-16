<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Чек: Транзакция №{{ $transaction->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #000;
            margin: 20px;
        }
        h2, h4 { margin: 0 0 5px; }
        .header, .footer { text-align: center; }
        .section { margin-top: 15px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            font-size: 14px;
        }
        .total {
            font-weight: bold;
            font-size: 16px;
        }
        .btn-print {
            margin-top: 20px;
            display: block;
            width: 100%;
        }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

@php
    $typeLabels = [
        'consume' => 'Расход',
        'loan' => $transaction->loan_direction === 'given' ? 'Выдано в долг' : 'Получено в долг',
        'return' => 'Возврат от клиента',
        'intake' => 'Поступление товара',
        'intake_loan' => 'Поступление (в долг)',
        'intake_return' => 'Возврат поставщику',
    ];
    $typeLabel = $typeLabels[$transaction->type] ?? strtoupper($transaction->type);
@endphp

<div class="header">
    <h2>Товарный чек</h2>
    <h4>{{ $typeLabel }} №{{ $transaction->id }}</h4>
    <p><small>{{ $transaction->created_at->format('d.m.Y H:i') }}</small></p>
</div>

<div class="section">
    <p><strong>Тип транзакции:</strong> {{ $typeLabel }}</p>
    <p><strong>Тип оплаты:</strong> {{ ucfirst($transaction->payment_type) }}</p>

    @if($transaction->type === 'loan')
        <p><strong>Направление долга:</strong> {{ $transaction->loan_direction === 'given' ? 'Выдан' : 'Получен' }}</p>
    @endif

    @if($transaction->supplier)
        <p><strong>Поставщик:</strong> {{ $transaction->supplier->name }}</p>
    @endif

    @if($transaction->client_name)
        <p><strong>Клиент:</strong> {{ $transaction->client_name }}</p>
    @endif

    @if($transaction->client_phone)
        <p><strong>Телефон клиента:</strong> {{ $transaction->client_phone }}</p>
    @endif

    @if($transaction->return_reason)
        <p><strong>Причина возврата:</strong> {{ $transaction->return_reason }}</p>
    @endif

    <p><strong>Статус:</strong> {{ $transaction->status ?? '—' }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>Товар</th>
            <th>Кол-во</th>
            <th>Ед.</th>
            <th>Цена</th>
            <th>Итого</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transaction->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '—' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="section">
    <p class="total">Общая сумма: {{ number_format($transaction->total_price, 2) }} UZS</p>

    @if($transaction->paid_amount !== null)
        <p>Оплачено: {{ number_format($transaction->paid_amount, 2) }} UZS</p>
    @endif

    @if($transaction->loan_amount !== null)
        <p>Сумма долга: {{ number_format($transaction->loan_amount, 2) }} UZS</p>
    @endif

    @if($transaction->loan_due_to !== null && $transaction->loan_due_to > 0)
        <p>Остаток к оплате: {{ number_format($transaction->loan_due_to, 2) }} UZS</p>
    @endif

    @if($transaction->qr_code)
        <p>QR-код: {{ $transaction->qr_code }}</p>
    @endif

    @if($transaction->note)
        <p><strong>Примечание:</strong> {{ $transaction->note }}</p>
    @endif
</div>

<div class="footer">
    <p>Спасибо за сотрудничество!</p>
</div>

<button class="btn-print" onclick="window.print()">🖨️ Печать</button>

</body>
</html>
