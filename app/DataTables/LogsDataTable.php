<?php

namespace App\DataTables;

use Spatie\Activitylog\Models\Activity as ActivityLog;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LogsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<ActivityLog> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('subject_type', fn($row) => $row->subject_type ? class_basename($row->subject_type) : '-')
            ->addColumn('causer', fn($row) => $row->causer->name ?? $row->causer->email ?? ($row->causer_id ? 'User #'.$row->causer_id : '-'))
            ->addColumn('properties_preview', function ($row) {
                // new smarter preview: show key fields for DocumentRegistrationEntry / DocumentRegistrationEntryFile
                $props = is_array($row->properties) ? $row->properties : (method_exists($row->properties ?? null, 'toArray') ? $row->properties->toArray() : (array) ($row->properties ?? []));
                // If action stored as attributes/old structure (model changes)
                $attrs = $props['attributes'] ?? $props;
                // Prefer readable keys
                $previewParts = [];

                if (isset($attrs['document_title'])) {
                    $previewParts[] = $attrs['document_title'];
                }
                if (isset($attrs['original_filename'])) {
                    $previewParts[] = $attrs['original_filename'];
                }
                if (isset($attrs['file_path']) && empty($previewParts)) {
                    // show filename portion of path if nothing else
                    $previewParts[] = basename($attrs['file_path']);
                }
                if (isset($attrs['status_id'])) {
                    $previewParts[] = 'Status: ' . $attrs['status_id'];
                }
                if (empty($previewParts)) {
                    $json = json_encode($props, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $preview = Str::limit($json, 140, '...');
                    return '<pre class="small mb-0" style="max-width:420px;white-space:pre-wrap;word-break:break-word;">' . e($preview) . '</pre>';
                }

                $text = implode(' · ', array_filter($previewParts));
                return '<div class="small text-muted" style="max-width:420px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' . e($text) . '</div>';
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-sm btn-outline-primary view-log" data-id="' . $row->id . '" title="View details"><i class="bx bx-show-alt"></i></button>';
            })

            // format created_at / updated_at to "01/22/2026 9:30 AM"
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('m/d/Y g:i A') : '';
            })
            ->addColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('m/d/Y g:i A') : '';
            })

            ->rawColumns(['properties_preview', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<ActivityLog>
     */
    public function query(ActivityLog $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('causer')
            ->select('id', 'description', 'subject_type', 'subject_id', 'event', 'causer_id', 'properties', 'created_at', 'updated_at');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('logs-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(80)
                  ->addClass('text-center'),
            Column::make('id'),
            Column::make('description')->title('Description'),
            Column::make('subject_type')->title('Subject'),
            Column::make('event')->title('Event'),
            Column::make('causer')->title('Causer'),
            Column::make('properties_preview')->title('Properties Preview')->orderable(false)->searchable(false),
            Column::make('created_at')->title('Logged At'),
            Column::make('updated_at')->title('Updated At'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Logs_' . date('YmdHis');
    }
}
