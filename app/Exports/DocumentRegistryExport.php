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
            'Customer',
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
            $entry->customer ?? '',
            $entry->remarks ?? '',
            ucfirst($entry->status)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
