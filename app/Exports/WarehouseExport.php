<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarehouseExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(public array $filters = [])
    {
    }

    public function query()
    {
        $query = Product::where('type', Product::TYPE_PHYSICAL);

        if (!empty($this->filters['category_id'])) {
            $query->whereHas('categories', function ($q) {
                $q->where('product_categories.id', $this->filters['category_id']);
            });
        }
        if (!empty($this->filters['out_of_stock'])) {
            $query->where('stock_quantity', '<=', 0);
        }
        if (!empty($this->filters['low_stock'])) {
            $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5);
        }

        return $query->orderBy('title');
    }

    public function headings(): array
    {
        return [
            'المنتج',
            'SKU',
            'التصنيف',
            'الكمية',
            'السعر',
            'القيمة الإجمالية',
            'الحالة',
        ];
    }

    public function map($product): array
    {
        $status = $product->stock_quantity <= 0
            ? 'نفد'
            : ($product->stock_quantity <= 5 ? 'منخفض' : 'متوفر');

        return [
            $product->title,
            $product->sku ?? '—',
            $product->categories->first()?->name ?? '—',
            $product->stock_quantity,
            number_format($product->effective_price, 2),
            number_format($product->stock_quantity * $product->effective_price, 2),
            $status,
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