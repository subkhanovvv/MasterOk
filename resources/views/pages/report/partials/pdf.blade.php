<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 5px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }
    </style>
</head>

<body>
    <h3>Отчет по деятельности с {{ $start }} по {{ $end }}</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Дата</th>
                <th>Тип</th>
                <th>Поставщик</th>
                <th>Сумма</th>
                <th>Займ</th>
                <th>Продукты</th>
                <th>Примечание</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activities as $index => $activity)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $activity->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @php
                            $typeRu = match ($activity->type) {
                                'consume' => 'Продажа',
                                'loan' => 'Долг',
                                'return' => 'Возврат',
                                'intake' => 'Поступление',
                                'intake_loan' => 'Поступление (в долг)',
                                'intake_return' => 'Возврат поставщику',
                                default => $activity->type,
                            };
                            $loandru = $activity->loan_direction === 'given' ? 'Выдано' : 'Получено';
                            $paymentRu = match ($activity->payment_type) {
                                'cash' => 'Наличные',
                                'card' => 'Карта',
                                'bank_transfer' => 'Банковский перевод',
                                default => $activity->payment_type,
                            };
                        @endphp
                        {{ strtoupper($typeRu) }}
                    </td>
                    <td>{{ $activity->brand->name ?? 'нет' }}
                        <br>
                        <small>{{ $activity->supplier->name ?? '' }}</small>
                    </td>
                    <td>{{ number_format($activity->total_price, 0, ',', ' ') }} сум
                        <br>
                        <small>{{ $paymentRu }}</small>
                    </td>
                    <td>
                        @if (in_array($activity->type, ['loan', 'intake_loan']))
                            {{ number_format($activity->loan_amount, 0, ',', ' ') }} сум
                            <br>
                            <small><strong>{{ $loandru }}</strong></small>
                        @else
                            нет
                        @endif
                    </td>
                    <td>
                        <ul class="mb-0">
                            @foreach ($activity->items as $item)
                                <li>{{ $item->product->name }} <strong>x</strong>
                                    {{ number_format($item->qty, 0, ',', ' ') }}
                                    {{ $item->unit }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $activity->note ?? 'нет' }}
                        <br>
                        {{ $activity->return_reason }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
