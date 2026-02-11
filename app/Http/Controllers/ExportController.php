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

        if (!Storage::disk($disk)->exists($path)) {
            Log::warning('Export file missing on download', [
                'export_id' => $export->id,
                'employee_no' => $export->employee_no,
                'file_name' => $path,
                'disk' => $disk,
            ]);
            abort(404, 'Export file not found.');
        }

        Log::info('Export download started', [
            'export_id' => $export->id,
            'file_name' => $path,
            'disk' => $disk,
        ]);

        return Storage::disk($disk)->download($path);
    }

    private function authorizeExport(Export $export): void
    {
        $user = Auth::user();
        if (!$user || $user->employee_no !== $export->employee_no) {
            abort(403);
        }
    }
}
