<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ReportExport implements FromCollection
{
    protected $activities;

    public function __construct($activities)
    {
        $this->activities = $activities;
    }

    public function collection(): Collection
    {
        return collect($this->activities)->map(function ($activity) {
            return [
                $activity->created_at->format('d.m.Y H:i'),
                strtoupper($activity->type),
                $activity->supplier->name ?? '-',
                $activity->total_price,
                in_array($activity->type, ['loan', 'intake_loan']) ? $activity->loan_amount ?? 0 : '-',
                implode(', ', $activity->items->map(fn($item) => $item->product->name . ' x' . $item->quantity . ' ' . $item->unit)->toArray()),
                $activity->note,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Дата',
            'Тип',
            'Поставщик',
            'Сумма',
            'Займ',
            'Продукты',
            'Примечание',
        ];
    }
}
