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
    <h2 style="text-align:center;">{{ strtoupper($settings->name ?? 'STORE') }}</h2>
    <h4 style="text-align:center;">Чек №{{ $transaction->id }}</h4>
    <hr>
    @php
        $paymentRu = match ($transaction->payment_type) {
            'cash' => 'Наличные',
            'card' => 'Карта',
            'bank_transfer' => 'Банковский перевод',
            default => $transaction->payment_type,
        };

        $transactionTypeRu = match ($transaction->type) {
            'consume' => 'Продажа',
            'loan' => 'Долг',
            'return' => 'Возврат',
            default => $transaction->type,
        };
    @endphp
    <p><strong>Тип:</strong> {{ strtoupper($transactionTypeRu) }}</p>
    <p><strong>Дата:</strong> {{ $transaction->created_at->format('d.m.Y H:i') }}</p>
    @if ($transaction->payment_type)
        <p><strong>Тип оплаты:</strong> {{ $paymentRu }}</p>
    @endif
    @if ($transaction->client_name)
        <p><strong>Клиент:</strong> {{ $transaction->client_name }}</p>
    @endif
    <hr>
    <strong>Товары:</strong>
    @foreach ($transaction->items as $item)
        <div class="item-line">
            <span>{{ $item->product->name }} x {{ number_format($item->qty) }}</span>
            <span>{{ number_format($item->product->sale_price * $item->qty, 0, ',', ' ') ?? '-' }} сум</span>
        </div>
    @endforeach
    <hr>
    <div class="line">
        <strong>Итого:</strong>
        <span>{{ number_format($transaction->total_price, 0, ',', ' ') }} сум</span>
    </div>
    <hr>
    @if ($transaction->note)
        <div class="notes">
            <strong>Примечание:</strong> {{ $transaction->note }}
        </div>
        <hr>
    @endif
    @if ($transaction->qr_code)
        <div class="qr-container">
            <img src="{{ asset('storage/' . $transaction->qr_code) }}" alt="QR Code" class="qr-code">
            <p>Сканируйте QR-код для проверки</p>
        </div>
    @endif
    <div style="text-align:center;">Спасибо за сотрудничество!</div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        };
    </script>
</body>

</html>
