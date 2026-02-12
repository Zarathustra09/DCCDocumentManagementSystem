<?php

namespace App\Http\Controllers;

use App\Models\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ExportController extends Controller
{
    public function show(Export $export)
    {
        $this->authorizeExport($export);

        $downloadUrl = URL::signedRoute('exports.download', $export);

        return view('export.show', [
            'export' => $export,
            'downloadUrl' => $downloadUrl,
        ]);
    }

    public function download(Request $request, Export $export)
    {
        $this->authorizeExport($export);

        $disk = $export->disk;
        $path = $export->file_name;

        Log::info('Export download requested', [
            'export_id' => $export->id,
            'employee_no' => $export->employee_no,
            'file_name' => $path,
            'disk' => $disk,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => Auth::id(),
        ]);

        if (empty($disk)) {
            Log::error('Export download: missing disk configuration', [
                'export_id' => $export->id,
                'disk' => $disk,
                'path' => $path,
            ]);
            abort(404, 'Export file not found.');
        }

        try {
            if (!Storage::disk($disk)->exists($path)) {
                Log::warning('Export file missing on download', [
                    'export_id' => $export->id,
                    'employee_no' => $export->employee_no,
                    'file_name' => $path,
                    'disk' => $disk,
                ]);
                abort(404, 'Export file not found.');
            }
        } catch (\Throwable $e) {
            Log::error('Export download: storage check failed', [
                'export_id' => $export->id,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Unable to access export storage.');
        }

        Log::info('Export download started', [
            'export_id' => $export->id,
            'file_name' => $path,
            'disk' => $disk,
        ]);

        try {
            return Storage::disk($disk)->download($path);
        } catch (\Throwable $e) {
            Log::error('Export download failed', [
                'export_id' => $export->id,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Unable to download export file.');
        }
    }

    private function authorizeExport(Export $export): void
    {
        $user = Auth::user();
        if (!$user || $user->employee_no !== $export->employee_no) {
            abort(403);
        }
    }
}
