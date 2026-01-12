<?php

namespace App\Exports;

use App\Models\DocumentRegistrationEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentRegistryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $entries;

    public function __construct($entries)
    {
        $this->entries = $entries;
    }

    public function collection()
    {
        return $this->entries;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Document Title',
            'Document No.',
            'Rev. No.',
            'Device Name',
            'Originator Name',
            'Customer Name',
            'Customer Code',
            'isActive',
            'Remarks',
            'Registration Status'
        ];
    }

    public function map($entry): array
    {
        return [
            $entry->submitted_at ? $entry->submitted_at->format('Y-m-d H:i') : '',
            $entry->document_title,
            $entry->document_no,
            $entry->revision_no,
            $entry->device_name ?? '',
            $entry->originator_name,
            // Customer Name
            $entry->customer ? ($entry->customer->name ?? '') : '',
            // Customer Code
            $entry->customer ? ($entry->customer->code ?? '') : '',
            // isActive as integer (1 or 0)
            $entry->customer ? (int)($entry->customer->is_active ?? 0) : 0,
            $entry->remarks ?? '',
            ucfirst($entry->status->name ?? 'Unknown')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
