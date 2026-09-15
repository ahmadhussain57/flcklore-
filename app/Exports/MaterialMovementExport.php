<?php

namespace App\Exports;

use App\Models\MaterialMovement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialMovementExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(public array $filters = [])
    {
    }

    public function query()
    {
        $query = MaterialMovement::with(['product', 'creator']);

        if (!empty($this->filters['product_id'])) {
            $query->where('product_id', $this->filters['product_id']);
        }
        if (!empty($this->filters['movement_type'])) {
            $query->where('movement_type', $this->filters['movement_type']);
        }
        if (!empty($this->filters['from_date'])) {
            $query->whereDate('movement_date', '>=', $this->filters['from_date']);
        }
        if (!empty($this->filters['to_date'])) {
            $query->whereDate('movement_date', '<=', $this->filters['to_date']);
        }

        return $query->orderByDesc('movement_date');
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'المنتج',
            'النوع',
            'الكمية',
            'المخزون قبل',
            'المخزون بعد',
            'سعر الوحدة',
            'التكلفة الإجمالية',
            'البيان',
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->movement_date->format('Y-m-d H:i'),
            $movement->product?->title ?? '—',
            $movement->movement_type_label,
            $movement->quantity,
            $movement->stock_before,
            $movement->stock_after,
            number_format($movement->unit_cost, 2),
            number_format($movement->total_cost, 2),
            $movement->description ?? '—',
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}