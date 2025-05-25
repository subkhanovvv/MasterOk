<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Чек</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0;
            padding: 5mm;
        }

        h2,
        h4,
        p {
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

        .item-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .qr-container {
            text-align: center;
            margin: 10px 0;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .notes {
            margin-top: 5px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center;">MasterOK</h2>
    <h4 style="text-align:center;">Чек №{{ $transaction->id }}</h4>
    <hr>
    <p><strong>Тип:</strong> {{ strtoupper($transaction->type) }}</p>
    <p><strong>Дата:</strong> {{ $transaction->created_at->format('d.m.Y H:i') }}</p>

    @if ($transaction->payment_type)
        <p><strong>Тип оплаты:</strong> {{ $transaction->payment_type }}</p>
    @endif

    @if ($transaction->client_name)
        <p><strong>Клиент:</strong> {{ $transaction->client_name }}</p>
    @endif

    @if ($transaction->supplier)
        <p><strong>Поставщик:</strong> {{ $transaction->supplier->name }}</p>
    @endif

    <hr>
    <strong>Товары:</strong>
    @foreach ($transaction->items as $item)
        <div class="item-line">
            <span>{{ $item->product->name }} x {{ $item->qty }}</span>
            <span>{{ number_format($item->product->sale_price * $item->qty, 2) ?? '-' }}</span>
        </div>
    @endforeach
    <hr>
    <div class="line">
        <strong>Итого:</strong>
        <span>{{ number_format($transaction->total_price, 2) }} сум</span>
    </div>
    @if ($transaction->paid_amount)
        <div class="line">
            <strong>Оплачено:</strong>
            <span>{{ number_format($transaction->paid_amount, 2) }} сум</span>
        </div>
        @if ($transaction->change_amount && $transaction->change_amount > 0)
            <div class="line">
                <strong>Сдача:</strong>
                <span>{{ number_format($transaction->change_amount, 2) }} сум</span>
            </div>
        @endif
    @endif
    <hr>

    @if ($transaction->note)
        <div class="notes">
            <strong>Примечание:</strong> {{ $transaction->note }}
        </div>
        <hr>
    @endif

    <div class="qr-container">
        @if ($transaction->qr_code)
            <img src="{{ asset('storage/' . $transaction->qr_code) }}" alt="QR Code" class="qr-code">
            <p>Сканируйте QR-код для проверки</p>
        @endif
    </div>
    <div style="text-align:center;">Спасибо за сотрудничество!</div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
