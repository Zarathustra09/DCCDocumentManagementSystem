<?php

namespace App\DataTables;

use App\Models\DocumentRegistrationEntry;
use App\Models\SubCategory; // <-- added import
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DcnsDataTable extends DataTable
{
    protected string $logType = 'build';
    protected $subcategoryId = null;

    public function setLogType(string $logType): self
    {
        $this->logType = $logType === 'mechatronics' ? 'mechatronics' : 'build';
        return $this;
    }

    public function setSubcategoryId($subcategoryId)
    {
        $this->subcategoryId = $subcategoryId;
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
                // Support special marker '__no_customer__' => customer_id IS NULL
                if ($request->has('customer_id')) {
                    $cid = $request->input('customer_id');
                    if ($cid === '__no_customer__') {
                        $query->whereNull('customer_id');
                    } elseif ($cid !== '') {
                        $query->where('customer_id', $cid);
                    }
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
                            // include organization fields in search via submittedBy.organization
                            ->orWhereHas('submittedBy.organization', fn($org) =>
                                $org->where('organization', 'like', "%{$globalSearch}%")
                                    ->orWhere('orgcode', 'like', "%{$globalSearch}%")
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
            ->addColumn('originator', fn(DocumentRegistrationEntry $entry) =>
                e($entry->submittedBy?->name ?? $entry->originator_name ?? 'N/A')
            )
            ->addColumn('dept', function (DocumentRegistrationEntry $entry) {
                return e(

                     $entry->submittedBy?->organization?->orgcode
                    ?? $entry->submittedBy?->organization?->organization
                    ?? $entry->submittedBy?->department?->name
                    ?? $entry->submittedBy?->department_name
                    ?? $entry->department_name
                    ?? 'N/A'
                );
            })
            ->addColumn('registration_date', function (DocumentRegistrationEntry $entry) {
                if (!$entry->submitted_at) {
                    return 'N/A';
                }
                return '<small><i class="bx bx-calendar"></i> ' .
                    e($entry->submitted_at->format('m/d/Y')) .
                    '<br><small class="text-muted">' .
                    e($entry->submitted_at->format('g:i A')) .
                    '</small></small>';
            })
            ->addColumn('effective_date', function (DocumentRegistrationEntry $entry) {
                if (!$entry->expiration_date) {
                    return 'N/A';
                }
                return '<small><i class="bx bx-calendar"></i> ' .
                    e($entry->expiration_date->format('m/d/Y')) .
                    '<br><small class="text-muted">' .
                    e($entry->expiration_date->format('g:i A')) .
                    '</small></small>';
            })
            ->addColumn('document_no', fn(DocumentRegistrationEntry $entry) =>
                e($entry->document_no ?? 'N/A')
            )
            ->addColumn('revision_no', fn(DocumentRegistrationEntry $entry) =>
                e($entry->revision_no ?? 'N/A')
            )
            ->addColumn('document_title', fn(DocumentRegistrationEntry $entry) =>
                '<strong>' . e($entry->document_title ?? 'N/A') . '</strong>'
            )
            ->addColumn('remarks', fn(DocumentRegistrationEntry $entry) =>
                e($entry->remarks ?? 'N/A')
            )
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
            ->rawColumns(['dcn_no_badge', 'registration_date', 'effective_date', 'document_title', 'action'])
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
            // eager-load submittedBy.organization
            ->with(['customer', 'category', 'submittedBy.organization', 'status'])
            ->whereDoesntHave('status', fn($q) => $q->where('name', 'Cancelled'));

        // Filter by subcategory if set
        if ($this->subcategoryId) {
            $query->where('category_id', $this->subcategoryId);
        }

        // Filter by customer: support '__no_customer__' => customer IS NULL
        if (request()->has('customer_id')) {
            $cid = request('customer_id');
            if ($cid === '__no_customer__') {
                $query->whereNull('customer_id');
            } elseif ($cid !== '') {
                $query->where('customer_id', $cid);
            }
        }

        return $query->orderByDesc('submitted_at')->orderByDesc('id');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $exportUrl = route('dcn.export');

        return $this->builder()
            ->setTableId('logTable')
            ->columns($this->getColumns())
            ->ajax([
                'url' => route('dcn.list.data'),
                'data' => <<<'JS'
function (d) {
    d.subcategory_id = window.selectedSubcategoryId || '';
    if (window.selectedCustomerId) {
        d.customer_id = window.selectedCustomerId;
    } else {
        delete d.customer_id;
    }
}
JS
            ])
            ->orderBy(3, 'desc')
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel')
                    ->text('<i class="bx bx-download"></i> Export to Excel')
                    ->className('btn btn-success btn-sm dt-export-btn')
                    ->action(<<<JS
function (e, dt, node, config) {
    var url = '{$exportUrl}';
    url += '?subcategory_id=' + (window.selectedSubcategoryId || '');
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
        // If a subcategory is selected and its code is 'S' (case-insensitive)
        // return the minimal set required: DCN, Originator, Dept, Reg-Date, Expiration, Title of Doc, Remarks.
        if ($this->subcategoryId) {
            try {
                $sc = SubCategory::find($this->subcategoryId);
                if ($sc && strcasecmp($sc->code ?? '', 'S') === 0) {
                    return [
                        Column::make('dcn_no_badge')->title('DCN')->orderable(false)->searchable(false),
                        Column::make('originator')->title('Originator'),
                        Column::make('dept')->title('Dept.')->orderable(false)->searchable(false),
                        Column::make('registration_date')->title('Reg-Date')->name('submitted_at')->searchable(false),
                        Column::make('effective_date')->title('Expiration')->name('expiration_date')->searchable(false),
                        Column::make('document_title')->title('Title of Doc'),
                        Column::make('remarks')->title('Remarks')->orderable(false)->searchable(false),
                        // Added action column for 'S' category so Manage dropdown appears
                        Column::computed('action')
                            ->exportable(false)
                            ->printable(false)
                            ->width(80)
                            ->addClass('text-center'),
                    ];
                }
            } catch (\Throwable $e) {
                // If anything goes wrong fetching the subcategory, fall back to default columns
            }
        }

        return [
            Column::make('dcn_no_badge')->title('DCN No.')->orderable(false)->searchable(false),
            Column::make('originator')->title('Originator'),
            Column::make('dept')->title('Dept.')->orderable(false)->searchable(false),
            Column::make('registration_date')->title('Date Registered')->name('submitted_at')->searchable(false),
            Column::make('document_no')->title('Document No.'),
            Column::make('revision_no')->title('Rev No.'),
            Column::make('document_title')->title('Title'),
            Column::make('remarks')->title('Remarks')->orderable(false)->searchable(false),
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
