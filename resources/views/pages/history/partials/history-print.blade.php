<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Чек</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0;
            padding: 10px;
        }
        h2, h4, p {
            margin: 4px 0;
        }
        hr {
            border: none;
            border-top: 1px dashed #000;
        }
        .line {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">MasterOK</h2>
    <h4 style="text-align:center;">Чек №{{ $transaction->id }}</h4>
    <hr>
    <p><strong>Тип:</strong> {{ strtoupper($transaction->type) }}</p>
    <p><strong>Дата:</strong> {{ $transaction->created_at->format('d.m.Y H:i') }}</p>

    @if($transaction->client_name)
        <p><strong>Клиент:</strong> {{ $transaction->client_name }}</p>
    @endif

    @if($transaction->supplier)
        <p><strong>Поставщик:</strong> {{ $transaction->supplier->name }}</p>
    @endif

    <hr>
    <strong>Товары:</strong>
    @foreach($transaction->items as $item)
        <div class="line">
            <span>{{ $item->product->name }}</span>
            <span>{{ $item->quantity }} x {{ number_format($item->price, 2) }}</span>
        </div>
    @endforeach
    <hr>
    <div class="line">
        <strong>Итого:</strong>
        <span>{{ number_format($transaction->total_price, 2) }} сум</span>
    </div>
    @if($transaction->paid_amount)
        <div class="line">
            <strong>Оплачено:</strong>
            <span>{{ number_format($transaction->paid_amount, 2) }} сум</span>
        </div>
    @endif
    <hr>
    <div style="text-align:center;">Спасибо за покупку!</div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
