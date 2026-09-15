<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        public Account $account,
        public array $entries = []
    ) {
    }

    public function array(): array
    {
        return array_map(function ($entry) {
            return [
                $entry['date']->format('Y-m-d'),
                $entry['entry_number'],
                $entry['description'],
                $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '—',
                $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '—',
                number_format(abs($entry['balance']), 2),
            ];
        }, $this->entries);
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'رقم السند',
            'البيان',
            'مدين',
            'دائن',
            'الرصيد',
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