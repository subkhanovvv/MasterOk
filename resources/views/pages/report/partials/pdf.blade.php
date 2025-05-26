<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
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
                    <td>{{ strtoupper($activity->type) }}</td>
                    <td>{{ $activity->supplier->name ?? '-' }}</td>
                    <td>{{ number_format($activity->total_price, 0, ',', ' ') }}</td>
                    <td>
                        @if(in_array($activity->type, ['loan', 'intake_loan']))
                            {{ $activity->loan_amount ?? 0 }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @foreach($activity->items as $item)
                            {{ $item->product->name }} x{{ $item->quantity }} {{ $item->unit }}<br>
                        @endforeach
                    </td>
                    <td>{{ $activity->note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
