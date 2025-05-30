<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $activities;

    public function __construct($activities)
    {
        $this->activities = $activities;
    }

    public function collection(): Collection
    {
        return collect($this->activities);
    }

    public function map($activity): array
    {
        $typeRu = match ($activity->type) {
            'consume' => 'Продажа',
            'loan' => 'Долг',
            'return' => 'Возврат',
            'intake' => 'Поступление',
            'intake_loan' => 'Поступление (в долг)',
            'intake_return' => 'Возврат поставщику',
            default => $activity->type,
        };

        $loandru = $activity->loan_direction === 'given' ? 'Выдано' : ($activity->loan_direction === 'taken' ? 'Получено' : '');
        $paymentRu = match ($activity->payment_type) {
            'cash' => 'Наличные',
            'card' => 'Карта',
            'bank_transfer' => 'Банковский перевод',
            default => $activity->payment_type ?? '',
        };

        $products = $activity->items->map(function ($item) {
            return $item->product->name . ' x' . number_format($item->qty, 0, ',', ' ') . ' ' . $item->unit;
        })->implode(', ');

        return [
            $activity->created_at->format('d.m.Y H:i'),
            $typeRu,
            $activity->brand->name ?? 'нет',
            $activity->supplier->name ?? 'нет',
            number_format($activity->total_price, 0, ',', ' ') . ' сум' . ($paymentRu ? " ({$paymentRu})" : ''),
            in_array($activity->type, ['loan', 'intake_loan']) ? number_format($activity->loan_amount, 0, ',', ' ') . ' сум (' . $loandru . ')' : 'нет',
            $products,
            $activity->note ?? 'нет' . ($activity->return_reason ? ' — ' . $activity->return_reason : ''),
        ];
    }

    public function headings(): array
    {
        return [
            'Дата',
            'Тип',
            'Бренд',
            'Поставщик',
            'Сумма',
            'Займ',
            'Продукты',
            'Примечание',
        ];
    }
}
