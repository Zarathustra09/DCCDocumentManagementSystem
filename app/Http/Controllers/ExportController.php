<?php

namespace App\Http\Controllers;

use App\Models\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function show(Export $export)
    {
        $this->authorizeExport($export);

        $downloadUrl = route('exports.download', $export);

        return response()->json([
            'id' => $export->id,
            'status' => $export->status,
            'completed_at' => $export->completed_at,
            'download_url' => $downloadUrl,
        ]);
    }

    public function download(Export $export)
    {
        $this->authorizeExport($export);

        Log::info('Export download requested', [
            'export_id' => $export->id,
            'employee_no' => $export->employee_no,
            'file_name' => $export->file_name,
        ]);

        return Storage::disk($export->disk)->download($export->file_name);
    }

    private function authorizeExport(Export $export): void
    {
        $user = Auth::user();
        if (!$user || $user->employee_no !== $export->employee_no) {
            abort(403);
        }
    }
}
