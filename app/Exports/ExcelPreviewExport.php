<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelPreviewExport implements FromCollection, WithHeadings
{
    private $filePath;
    private $data = [];
    private $headings = [];

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->loadData();
    }

    private function loadData()
    {
        try {
            $spreadsheet = IOFactory::load($this->filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Get all data as array
            $allData = $worksheet->toArray();

            if (!empty($allData)) {
                // First row as headings
                $this->headings = array_shift($allData);

                // Limit to first 100 rows for preview
                $this->data = array_slice($allData, 0, 100);
            }
        } catch (\Exception $e) {
            // Handle error silently, data will remain empty
        }
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
