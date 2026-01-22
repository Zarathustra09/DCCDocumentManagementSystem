<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\LogsDataTable;
use Spatie\Activitylog\Models\Activity as ActivityLog;

class LogsController extends Controller
{
    public function index(LogsDataTable $dataTable)
    {
        return $dataTable->render('logs.index');
    }

    /**
     * Return activity details (JSON) for client-side modal/alert.
     */
    public function show(ActivityLog $log)
    {
        $log->load('causer');

        $properties = $log->properties instanceof \Illuminate\Support\Collection || method_exists($log->properties ?? null, 'toArray')
            ? ($log->properties->toArray() ?? [])
            : (is_array($log->properties) ? $log->properties : (array) ($log->properties ?? []));

        // Compute diff when properties contain 'attributes' (new) and 'old'
        $diff = [];
        $attributes = $properties['attributes'] ?? null;
        $old = $properties['old'] ?? null;

        if (is_array($attributes) && is_array($old)) {
            foreach ($attributes as $key => $newValue) {
                $oldValue = array_key_exists($key, $old) ? $old[$key] : null;

                // Normalize arrays/objects to JSON strings for comparison/return
                $normalize = function ($v) {
                    if (is_array($v) || is_object($v)) {
                        return json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }
                    // cast booleans and nulls to explicit strings to avoid loose equal issues
                    if (is_bool($v)) return $v ? 'true' : 'false';
                    if ($v === null) return null;
                    return (string) $v;
                };

                $n = $normalize($newValue);
                $o = $normalize($oldValue);

                if ($n !== $o) {
                    $diff[] = [
                        'field' => $key,
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return response()->json([
            'id' => $log->id,
            'description' => $log->description,
            'subject_type' => $log->subject_type ? class_basename($log->subject_type) : null,
            'subject_id' => $log->subject_id ?? null,
            'event' => $log->event,
            'causer' => $log->causer ? ($log->causer->name ?? $log->causer->email ?? "User #{$log->causer_id}") : null,
            'created_at' => $log->created_at, // raw
            // human-friendly time like "01/22/2026 9:30 AM"
            'created_at_human' => $log->created_at ? $log->created_at->format('m/d/Y g:i A') : null,
            'updated_at' => $log->updated_at,
            'updated_at_human' => $log->updated_at ? $log->updated_at->format('m/d/Y g:i A') : null,
            'properties' => $properties,
            'diff' => $diff, // added diff data
        ]);
    }
}
