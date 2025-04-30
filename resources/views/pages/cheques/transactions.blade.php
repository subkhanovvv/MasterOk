<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Чек - Транзакция #{{ $transaction->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 300px;
            margin: auto;
            font-size: 14px;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .mt-1 {
            margin-top: 10px;
        }

        .mt-2 {
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            padding: 5px 0;
        }

        hr {
            border: none;
            border-top: 1px dashed #aaa;
        }

        .barcode {
            margin-top: 20px;
            text-align: center;
        }
    </style>
    
</head>

<body>
    <div class="text-center">
        <h3>Компания</h3>
        <p class="bold">Чек транзакции</p>
        <p>№ {{ $transaction->id }}</p>
        <p>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d.m.Y H:i') }}</p>
        <hr>
    </div>
    <p><strong>Тип:</strong>
        @php
            $typeRu = match ($transaction->type) {
                'consume' => 'Расход',
                'intake' => 'Приход',
                'return' => 'Возврат клиента',
                'loan' => 'Займ клиенту',
                'intake_return' => 'Возврат поставщику',
                'intake_loan' => 'Займ от поставщика',
                default => $transaction->type,
            };
        @endphp
        {{ $typeRu }}
    </p>
    
    <table class="table">
        {{-- @foreach ($transaction->product as $item) --}}
            <tr>
                <td>{{ $transaction->product->name }}</td>
                <td style="text-align: right;">x{{ $transaction->qty }} {{ $transaction->product->unit }}</td>
            </tr>
        {{-- @endforeach --}}
    </table>
    <hr>
    <p><strong>Сумма:</strong> {{ number_format($transaction->total_price) }} сум</p>
    @if ($transaction->product->barcode)
        <div class="barcode">
            {!! file_get_contents(storage_path('app/public/' . $transaction->product->barcode->barcode_path)) !!}
        </div>
    @else
        <p>Штрих-код: не найден</p>
    @endif



    <div class="text-center mt-2">
        <p>Спасибо за сотрудничество!</p>
    </div>
</body>

</html>
