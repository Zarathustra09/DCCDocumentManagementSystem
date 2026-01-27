<?php

namespace App\DataTables;

use App\Models\DocumentRegistrationEntry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DcnsDataTable extends DataTable
{
    protected string $logType = 'build';

    public function setLogType(string $logType): self
    {
        $this->logType = $logType === 'mechatronics' ? 'mechatronics' : 'build';
        return $this;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<DocumentRegistrationEntry> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filter(function (QueryBuilder $query) {
                $request = $this->request();

                // Apply customer filter from tab selection
                if ($request->filled('customer_id')) {
                    $query->where('customer_id', $request->input('customer_id'));
                }

                // Global search
                $searchPayload = $request->input('search');
                $globalSearch = is_array($searchPayload)
                    ? trim($searchPayload['value'] ?? '')
                    : trim((string) $searchPayload);

                if ($globalSearch !== '') {
                    $query->where(function ($q) use ($globalSearch) {
                        $q->where('dcn_no', 'like', "%{$globalSearch}%")
                            ->orWhere('document_no', 'like', "%{$globalSearch}%")
                            ->orWhere('document_title', 'like', "%{$globalSearch}%")
                            ->orWhere('device_name', 'like', "%{$globalSearch}%")
                            ->orWhere('revision_no', 'like', "%{$globalSearch}%")
                            ->orWhere('originator_name', 'like', "%{$globalSearch}%")
                            ->orWhereHas('submittedBy', fn($sq) =>
                                $sq->where('firstname', 'like', "%{$globalSearch}%")
                                   ->orWhere('lastname', 'like', "%{$globalSearch}%")
                            )
                            ->orWhereHas('customer', fn($cust) =>
                                $cust->where('name', 'like', "%{$globalSearch}%")
                            )
                            ->orWhereHas('category', fn($cat) =>
                                $cat->where('name', 'like', "%{$globalSearch}%")
                                    ->orWhere('code', 'like', "%{$globalSearch}%")
                            )
                            ->orWhereHas('status', fn($sq) =>
                                $sq->where('name', 'like', "%{$globalSearch}%")
                            );
                    });
                }
            }, false)
            ->addColumn('dcn_no_badge', function (DocumentRegistrationEntry $entry) {
                return $entry->dcn_no
                    ? '<span class="badge bg-success"><i class="bx bx-check"></i> ' . e($entry->dcn_no) . '</span>'
                    : '<span class="badge bg-warning text-dark"><i class="bx bx-time"></i> Not Assigned</span>';
            })
            ->addColumn('status_badge', function (DocumentRegistrationEntry $entry) {
                $status = $entry->status->name ?? 'Unknown';
                return match ($status) {
                    'Pending' => '<span class="badge bg-warning text-dark"><i class="bx bx-time"></i> ' . e($status) . '</span>',
                    'Implemented' => '<span class="badge bg-success text-white"><i class="bx bx-check"></i> ' . e($status) . '</span>',
                    default => '<span class="badge bg-danger text-white"><i class="bx bx-x"></i> ' . e($status) . '</span>',
                };
            })
            ->addColumn('originator', fn(DocumentRegistrationEntry $entry) =>
                e($entry->submittedBy?->name ?? $entry->originator_name ?? '-')
            )
            ->addColumn('registration_date', function (DocumentRegistrationEntry $entry) {
                if (!$entry->submitted_at) {
                    return '-';
                }
                return '<small><i class="bx bx-calendar"></i> ' .
                    e($entry->submitted_at->format('m/d/Y')) .
                    '<br><small class="text-muted">' .
                    e($entry->submitted_at->format('g:i A')) .
                    '</small></small>';
            })
            ->addColumn('effective_date', function (DocumentRegistrationEntry $entry) {
                if (!$entry->implemented_at) {
                    return '-';
                }
                return '<small><i class="bx bx-calendar"></i> ' .
                    e($entry->implemented_at->format('m/d/Y')) .
                    '<br><small class="text-muted">' .
                    e($entry->implemented_at->format('g:i A')) .
                    '</small></small>';
            })
            ->addColumn('document_no', fn(DocumentRegistrationEntry $entry) =>
                e($entry->document_no ?? '-')
            )
            ->addColumn('revision_no', fn(DocumentRegistrationEntry $entry) =>
                e($entry->revision_no ?? '-')
            )
            ->addColumn('device_name', fn(DocumentRegistrationEntry $entry) =>
                e($entry->device_name ?? 'N/A')
            )
            ->addColumn('document_title', fn(DocumentRegistrationEntry $entry) =>
                '<strong>' . e($entry->document_title ?? '-') . '</strong>'
            )
            ->addColumn('customer_display', function (DocumentRegistrationEntry $entry) {
                return e($entry->customer->name ?? '-');
            })
            ->addColumn('action', function (DocumentRegistrationEntry $entry) {
                $dropdown = '
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bx bx-cog"></i> Manage
                        </button>
                        <div class="dropdown-menu">';

                if (!$entry->dcn_no) {
                    $dropdown .= '
                            <button type="button" class="dropdown-item" onclick="openDcnModal(' . $entry->id . ')">
                                <i class="bx bx-plus me-2"></i> Assign DCN
                            </button>';
                } else {
                    $dropdown .= '
                            <button type="button" class="dropdown-item" onclick="openDcnModal(' . $entry->id . ')">
                                <i class="bx bx-edit-alt me-2"></i> Update DCN
                            </button>
                            <button type="button" class="dropdown-item text-danger" onclick="clearDcn(' . $entry->id . ')">
                                <i class="bx bx-x me-2"></i> Clear DCN
                            </button>';
                }

                $dropdown .= '
                        </div>
                    </div>';

                return $dropdown;
            })
            ->rawColumns(['dcn_no_badge', 'status_badge', 'registration_date', 'effective_date', 'document_title', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<DocumentRegistrationEntry>
     */
    public function query(DocumentRegistrationEntry $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['customer', 'category', 'submittedBy', 'status'])
            ->whereDoesntHave('status', fn($q) => $q->where('name', 'Cancelled'));

        if ($this->logType === 'mechatronics') {
            $query->whereHas('submittedBy', fn($q) => $q->where('organization_id', 1));
        } else {
            $query->whereHas('submittedBy', fn($q) => $q->where('organization_id', '!=', 1)->orWhereNull('organization_id'));
        }

        return $query->orderByDesc('submitted_at')->orderByDesc('id');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $ajaxData = <<<'JS'
function (d) {
    d.log_type = window.selectedLogType || 'build';
    d.customer_id = window.selectedCustomerId || '';
}
JS;

        $exportUrl = route('dcn.export');

        return $this->builder()
            ->setTableId('logTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->ajax(['data' => $ajaxData])
            ->orderBy(3, 'desc')
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel')
                    ->text('<i class="bx bx-download"></i> Export to Excel')
                    ->className('btn btn-success btn-sm dt-export-btn')
                    ->action(<<<JS
function (e, dt, node, config) {
    var url = '{$exportUrl}';
    url += '?log_type=' + (window.selectedLogType || 'build');
    if (window.selectedCustomerId) {
        url += '&customer_id=' + window.selectedCustomerId;
    }
    window.location.href = url;
}
JS)
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('dcn_no_badge')->title('DCN No.')->orderable(false)->searchable(false),
            Column::make('status_badge')->title('Status')->orderable(false)->searchable(false),
            Column::make('originator')->title('Originator'),
            Column::make('registration_date')->title('Registration Date')->name('submitted_at')->searchable(false),
            Column::make('effective_date')->title('Effective Date')->name('implemented_at')->searchable(false),
            Column::make('document_no')->title('Document No.'),
            Column::make('revision_no')->title('Rev.'),
            Column::make('device_name')->title('Device Name / Part Number'),
            Column::make('document_title')->title('Document Title'),
            Column::make('customer_display')->title('Customer')->orderable(false)->searchable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Dcns_' . date('YmdHis');
    }
}
