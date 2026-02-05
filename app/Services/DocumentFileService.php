<?php

namespace App\Services;

use App\Interfaces\DocumentRegistryFileServiceInterface;
use App\Models\DocumentRegistrationEntry;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DocumentFileService implements DocumentRegistryFileServiceInterface
{
    public function download(DocumentRegistrationEntry $entry, $fileId)
    {
        $file = $entry->files()->find($fileId);

        if (!$file || !Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download(
            $file->file_path,
            $file->original_filename
        );
    }

    public function preview(DocumentRegistrationEntry $entry, $fileId)
    {
        $file = $entry->files()->find($fileId);
        if (!$file || !Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::disk('local')->path($file->file_path);
        if (str_contains($file->mime_type, 'pdf') || str_contains($file->mime_type, 'image')) {
            return response()->file($filePath, [
                'Content-Type' => $file->mime_type,
                'Content-Disposition' => 'inline; filename="' . $file->original_filename . '"'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Preview not available for this file type'
        ], 400);
    }

    public function previewApi(DocumentRegistrationEntry $entry, $fileId)
    {
        $file = $entry->files()->find($fileId);
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'No file attached'
            ], 404);
        }

        try {
            $filePath = Storage::disk('local')->path($file->file_path);
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            $fileSize = filesize($filePath);
            if ($fileSize > 20 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File too large for preview. Please download to view.'
                ], 400);
            }

            $extension = strtolower(pathinfo($file->original_filename, PATHINFO_EXTENSION));
            $mimeType = $file->mime_type;

            if (in_array($extension, ['doc', 'docx']) || str_contains($mimeType, 'word')) {
                return $this->previewWordDocument($filePath);
            }
            if (in_array($extension, ['xls', 'xlsx', 'csv']) || str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'excel')) {
                return $this->previewSpreadsheet($filePath);
            }
            if (in_array($extension, ['txt', 'csv']) || str_contains($mimeType, 'text')) {
                return $this->previewTextFile($filePath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Preview not available for this file type'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating the preview'
            ], 500);
        }
    }

    private function previewWordDocument($filePath)
    {
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
        } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document appears to be corrupted or uses an unsupported format'
            ], 400);
        }

        try {
            $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
            $tempFile = tempnam(sys_get_temp_dir(), 'phpword_preview_');
            $htmlWriter->save($tempFile);
            $htmlContent = file_get_contents($tempFile);
            unlink($tempFile);

            if (empty($htmlContent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document appears to be empty'
                ], 400);
            }

            $htmlContent = $this->cleanWordHtml($htmlContent);
            return response()->json([
                'success' => true,
                'content' => $htmlContent,
                'content_type' => 'word'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error converting document to preview format'
            ], 500);
        }
    }

    private function previewSpreadsheet($filePath)
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $maxColumn = min(20, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn));
            $limitedHighestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxColumn);
            $data = $worksheet->rangeToArray('A1:' . $limitedHighestColumn . min(100, $highestRow), null, true, true);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spreadsheet appears to be empty'
                ], 400);
            }

            $headings = array_shift($data);
            $html = $this->generateSpreadsheetHtml($headings, $data, $highestRow);

            return response()->json([
                'success' => true,
                'content' => $html,
                'content_type' => 'spreadsheet'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading spreadsheet file'
            ], 500);
        }
    }

    private function previewTextFile($filePath)
    {
        try {
            $content = file_get_contents($filePath);
            if ($content === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to read text file'
                ], 500);
            }

            if (strlen($content) > 50000) {
                $content = substr($content, 0, 50000) . "\n\n... (content truncated for preview)";
            }

            $html = '<div class="text-preview-content">' . htmlspecialchars($content) . '</div>';
            return response()->json([
                'success' => true,
                'content' => $html,
                'content_type' => 'text'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading text file'
            ], 500);
        }
    }

    private function generateSpreadsheetHtml($headings, $data, $totalRows)
    {
        $html = '<div class="spreadsheet-preview">';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-sm">';
        if (!empty($headings)) {
            $html .= '<thead><tr>';
            foreach ($headings as $heading) {
                $html .= '<th>' . htmlspecialchars($heading ?? '') . '</th>';
            }
            $html .= '</tr></thead>';
        }
        $html .= '<tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        if ($totalRows > 100) {
            $html .= '<div class="alert alert-info mt-2 mb-0">';
            $html .= '<small><i class="bx bx-info-circle"></i> Showing first 100 rows of ' . $totalRows . ' total rows. Download the file to view all data.</small>';
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    private function cleanWordHtml($html)
    {
        $html = preg_replace('/<\?xml[^>]*>/', '', $html);
        $html = preg_replace('/<!DOCTYPE[^>]*>/', '', $html);
        $html = preg_replace('/<html[^>]*>/', '<div class="word-document">', $html);
        $html = str_replace('</html>', '</div>', $html);
        $html = preg_replace('/<head[^>]*>.*?<\/head>/s', '', $html);
        $html = preg_replace('/<body[^>]*>/', '', $html);
        $html = str_replace('</body>', '', $html);
        $html = preg_replace('/style="[^"]*?(font-family|color|text-align|font-weight|font-style)[^"]*?"/', '', $html);
        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace('> <', '><', $html);
        $html = str_replace('<p></p>', '', $html);
        return trim($html);
    }
}
