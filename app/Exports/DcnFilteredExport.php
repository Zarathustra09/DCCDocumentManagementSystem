<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DcnFilteredExport implements FromView
{
    protected $entries;

    public function __construct($entries)
    {
        $this->entries = $entries;
    }

    public function view(): View
    {
        return view('exports.dcn_filtered', [
            'entries' => $this->entries
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
