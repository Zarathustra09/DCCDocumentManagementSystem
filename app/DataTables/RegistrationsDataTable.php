<?php

namespace App\DataTables;

use App\Models\DocumentRegistrationEntry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RegistrationsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Registration> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $exportUrl = route('document-excel.export');

        return (new EloquentDataTable($query))
            ->filter(function (QueryBuilder $query) {
                $request = $this->request();

                $submittedBy = $request->get('submitted_by');
                if ($submittedBy !== null && $submittedBy !== '') {
                    is_array($submittedBy)
                        ? $query->whereIn('submitted_by', array_filter($submittedBy))
                        : $query->where('submitted_by', $submittedBy);
                }

                $searchPayload = $request->input('search');
                $globalSearch = is_array($searchPayload)
                    ? trim($searchPayload['value'] ?? '')
                    : trim((string) $searchPayload);

                if ($globalSearch !== '') {
                    $query->where(function ($q) use ($globalSearch) {
                        $q->where('control_no', 'like', "%{$globalSearch}%")
                            ->orWhere('document_no', 'like', "%{$globalSearch}%")
                            ->orWhere('document_title', 'like', "%{$globalSearch}%")
                            ->orWhere('device_name', 'like', "%{$globalSearch}%")
                            ->orWhere('originator_name', 'like', "%{$globalSearch}%")
                            ->orWhereHas('category', fn($cq) =>
                                $cq->where('name', 'like', "%{$globalSearch}%")
                                   ->orWhere('code', 'like', "%{$globalSearch}%")
                            )
                            ->orWhereHas('status', fn($sq) =>
                                $sq->where('name', 'like', "%{$globalSearch}%")
                            )
                            ->orWhereHas('customer', fn($cust) =>
                                $cust->where('name', 'like', "%{$globalSearch}%")
                            );
                    });
                }
            }, false)
            ->filterColumn('category', function ($query, $keyword) {
                if ($keyword === null || $keyword === '') {
                    return;
                }

                $query->whereHas('category', function ($q) use ($keyword) {
                    if (is_numeric($keyword)) {
                        $q->where('id', $keyword);
                    } else {
                        $q->where(function ($inner) use ($keyword) {
                            $inner->where('name', 'like', "%{$keyword}%")
                                  ->orWhere('code', 'like', "%{$keyword}%");
                        });
                    }
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword === null || $keyword === '') {
                    return;
                }

                $query->whereHas('status', function ($q) use ($keyword) {
                    $q->where('name', $keyword);
                });
            })
            ->filterColumn('submitted_at', function ($query, $keyword) {
                if ($keyword === null || $keyword === '') {
                    return;
                }
                [$from, $to] = array_pad(explode('|', $keyword), 2, null);
                if ($from) {
                    $query->whereDate('submitted_at', '>=', $from);
                }
                if ($to) {
                    $query->whereDate('submitted_at', '<=', $to);
                }
            })
            ->addColumn('control_no', fn(DocumentRegistrationEntry $entry) =>
                '<strong>' . e($entry->control_no ?? '-') . '</strong>'
            )
            ->addColumn('document_title', fn(DocumentRegistrationEntry $entry) =>
                '<strong>' . e($entry->document_title ?? '-') . '</strong>'
            )
            ->addColumn('category', fn(DocumentRegistrationEntry $entry) =>
                $entry->category
                    ? '<small>' . e($entry->category->name) . '</small>'
                    : '-'
            )
            ->addColumn('device_name', fn(DocumentRegistrationEntry $entry) =>
                e($entry->device_name ?? 'N/A')
            )
            ->addColumn('document_no', fn(DocumentRegistrationEntry $entry) =>
                e($entry->document_no ?? '-')
            )
            ->addColumn('revision_no', fn(DocumentRegistrationEntry $entry) =>
                e($entry->revision_no ?? '-')
            )
            ->addColumn('originator_name', fn(DocumentRegistrationEntry $entry) =>
                e($entry->originator_name ?? '-')
            )
            ->addColumn('customer', fn(DocumentRegistrationEntry $entry) =>
                e($entry->customer->name ?? '-')
            )
            ->addColumn('status', function (DocumentRegistrationEntry $entry) {
                $name = $entry->status->name ?? 'Unknown';
                return match ($name) {
                    'Pending' => '<span class="badge bg-warning text-dark"><i class="bx bx-time"></i> ' . e($name) . '</span>',
                    'Implemented' => '<span class="badge bg-success text-white"><i class="bx bx-check"></i> ' . e($name) . '</span>',
                    default => '<span class="badge bg-danger text-white"><i class="bx bx-x"></i> ' . e($name) . '</span>',
                };
            })
            ->addColumn('submitted_at', function (DocumentRegistrationEntry $entry) {
                if (!$entry->submitted_at) {
                    return '-';
                }

                return '<small><i class="bx bx-calendar"></i> ' .
                    e($entry->submitted_at->format('m/d/Y')) .
                    '<br><small class="text-muted">' .
                    e($entry->submitted_at->format('g:i A')) .
                    '</small></small>';
            })
            ->addColumn('action', function (DocumentRegistrationEntry $entry) {
                $viewUrl = route('document-registry.show', $entry);
                $canEdit = $entry->submitted_by === auth()->id()
                    || auth()->user()?->can('edit document registration details');

                $editLink = $canEdit
                    ? '<a class="dropdown-item" href="' . route('document-registry.edit', $entry) . '">
                            <i class="bx bx-edit-alt me-2"></i> Edit
                       </a>'
                    : '';

                return '
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bx bx-cog"></i> Manage
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="' . $viewUrl . '">
                                <i class="bx bx-show me-2"></i> View Details
                            </a>
                            ' . $editLink . '
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['control_no', 'document_title', 'category', 'status', 'submitted_at', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Registration>
     */
    public function query(DocumentRegistrationEntry $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['submittedBy', 'approvedBy', 'status', 'category', 'customer']);

        if (!auth()->user()?->can('view all document registrations')) {
            $query->where('submitted_by', auth()->id());
        }

        return $query->orderByDesc('id');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $ajaxData = <<<'JS'
function (d) {
    var $advancedToggle = $('#advancedToggle');
    var isAdvanced = !$advancedToggle.length || $advancedToggle.is(':checked');

    var status = $('#status').val() || '';
    var category = $('#category_id').val() || '';
    var submittedBy = $('#submitted_by').val() || '';
    var dateFrom = $('#date_from').val() || '';
    var dateTo = $('#date_to').val() || '';
    var advSearch = $('#search').val() || '';
    var searchInput = d.search && d.search.value ? d.search.value : '';

    if (isAdvanced && advSearch) {
        d.search.value = advSearch;
    } else if (searchInput) {
        d.search.value = searchInput;
    } else {
        d.search.value = '';
    }

    if (d.columns && d.columns.length) {
        if (d.columns[2]) {
            d.columns[2].search.value = isAdvanced ? category : '';
        }
        if (d.columns[8]) {
            d.columns[8].search.value = isAdvanced ? status : '';
        }
        if (d.columns[9]) {
            d.columns[9].search.value = (isAdvanced && (dateFrom || dateTo))
                ? (dateFrom + '|' + dateTo)
                : '';
        }
    }

    d.submitted_by = isAdvanced ? submittedBy : '';
    d.advanced = isAdvanced ? 1 : 0;
}
JS;

        $exportUrl = route('document-excel.export');

        return $this->builder()
            ->setTableId('documentRegistry')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->ajax(['data' => $ajaxData])
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel')
                    ->text('<i class="bx bx-download"></i> Export to Excel')
                    ->className('btn btn-success btn-sm dt-export-btn')
                    ->action(<<<JS
function (e, dt, node, config) {
    var form = document.getElementById('filterForm');
    var url = '{$exportUrl}';
    if (!form) {
        window.location.href = url;
        return;
    }
    var formData = new FormData(form);
    var params = new URLSearchParams(formData);
    var query = params.toString();
    if (query) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + query;
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
            Column::make('control_no')->title('Control No.'),
            Column::make('document_title')->title('Document Title'),
            Column::make('category')->title('Category'),
            Column::make('device_name')->title('Device Name / Part Number'),
            Column::make('document_no')->title('Document No.'),
            Column::make('revision_no')->title('Rev.'),
            Column::make('originator_name')->title('Originator'),
            Column::make('customer')->title('Customer'),
            Column::make('status')->title('Status'),
            Column::make('submitted_at')->title('Submitted At'),
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
        return 'Registrations_' . date('YmdHis');
    }
}
