<?php

namespace App\DataTables;

use App\Models\DocumentRegistrationEntry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserRegistrationsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<UserRegistration> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filter(function ($builder) {
                $request = $this->request();

                $status = $request->get('status');
                if (!empty($status)) {
                    $builder->whereHas('status', fn($q) => $q->where('name', $status));
                }

                $category = $request->get('category_id');
                if (!empty($category)) {
                    $builder->where('category_id', $category);
                }

                $searchInput = $request->input('search');
                $searchValue = is_array($searchInput)
                    ? trim($searchInput['value'] ?? '')
                    : trim((string) $searchInput);

                if ($searchValue !== '') {
                    $builder->where(function ($q) use ($searchValue) {
                        $q->where('control_no', 'like', "%{$searchValue}%")
                          ->orWhere('document_no', 'like', "%{$searchValue}%")
                          ->orWhere('document_title', 'like', "%{$searchValue}%")
                          ->orWhere('device_name', 'like', "%{$searchValue}%")
                          ->orWhere('originator_name', 'like', "%{$searchValue}%")
                          ->orWhereHas('category', function ($cat) use ($searchValue) {
                              $cat->where('name', 'like', "%{$searchValue}%")
                                  ->orWhere('code', 'like', "%{$searchValue}%");
                          })
                          ->orWhereHas('customer', fn($cust) => $cust->where('name', 'like', "%{$searchValue}%"))
                          ->orWhereHas('status', fn($stat) => $stat->where('name', 'like', "%{$searchValue}%"));
                    });
                }
            })
            ->addColumn('control_no', fn(DocumentRegistrationEntry $entry) => '<strong>' . e($entry->control_no ?? '-') . '</strong>')
            ->addColumn('document_title', fn(DocumentRegistrationEntry $entry) => '<strong>' . e($entry->document_title ?? '-') . '</strong>')
            ->addColumn('category', fn(DocumentRegistrationEntry $entry) => $entry->category ? '<small>' . e($entry->category->name) . '</small>' : '-')
            ->addColumn('device_name', fn(DocumentRegistrationEntry $entry) => e($entry->device_name ?? '-'))
            ->addColumn('document_no', fn(DocumentRegistrationEntry $entry) => e($entry->document_no ?? '-'))
            ->addColumn('revision_no', fn(DocumentRegistrationEntry $entry) => e($entry->revision_no ?? '-'))
            ->addColumn('originator_name', fn(DocumentRegistrationEntry $entry) => e($entry->originator_name ?? '-'))
            ->addColumn('customer', fn(DocumentRegistrationEntry $entry) => e($entry->customer->name ?? '-'))
            ->addColumn('submitted_at', function (DocumentRegistrationEntry $entry) {
                if (!$entry->submitted_at) {
                    return '-';
                }
                return '<small><i class="bx bx-calendar"></i> ' . e($entry->submitted_at->format('m/d/Y')) .
                    '<br><small class="text-muted">' . e($entry->submitted_at->format('g:i A')) . '</small></small>';
            })
            ->addColumn('implemented_by', function (DocumentRegistrationEntry $entry) {
                $name = $entry->approvedBy?->name ?? '-';
                $date = $entry->implemented_at?->format('m/d/Y');
                $time = $entry->implemented_at?->format('g:i A');
                $dateLine = $date ? '<br><i class="bx bx-calendar"></i> ' . e($date) . ' <small class="text-muted">' . e($time) . '</small>' : '';
                return '<small><i class="bx bx-user"></i> ' . e($name) . $dateLine . '</small>';
            })
            ->addColumn('status', function (DocumentRegistrationEntry $entry) {
                $name = $entry->status->name ?? 'Unknown';
                return match ($name) {
                    'Pending' => '<span class="badge bg-warning text-dark"><i class="bx bx-time"></i> ' . e($name) . '</span>',
                    'Implemented' => '<span class="badge bg-success text-white"><i class="bx bx-check"></i> ' . e($name) . '</span>',
                    default => '<span class="badge bg-danger text-white"><i class="bx bx-x"></i> ' . e($name) . '</span>',
                };
            })
            ->addColumn('action', function (DocumentRegistrationEntry $entry) {
                $viewUrl = route('document-registry.show', $entry);
                $editLink = '';
                if ($entry->submitted_by === auth()->id() || auth()->user()?->can('edit document registration details')) {
                    $editLink = '<a class="dropdown-item" href="' . route('document-registry.edit', $entry) . '">
                                    <i class="bx bx-edit-alt me-2"></i> Edit
                                 </a>';
                }

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
            ->rawColumns(['control_no', 'document_title', 'category', 'submitted_at', 'implemented_by', 'status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<UserRegistration>
     */
    public function query(DocumentRegistrationEntry $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['category', 'customer', 'status', 'approvedBy'])
            ->where('submitted_by', Auth::id())
            ->orderByDesc('id');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('documentRegistry')
            ->columns($this->getColumns())
            ->ajax([
                'url' => request()->url(),
                'type' => 'GET',
                'data' => 'function(data) {
                    data.status = $("#filter-status").val() || "";
                    data.category_id = $("#filter-category").val() || "";
                }',
            ])
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
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
            Column::make('control_no')->title('Control No.'),
            Column::make('document_title')->title('Document Title'),
            Column::make('category')->title('Category')->orderable(false)->searchable(false),
            Column::make('device_name')->title('Device Name / Part Number'),
            Column::make('document_no')->title('Document No.'),
            Column::make('revision_no')->title('Rev.'),
            Column::make('originator_name')->title('Originator'),
            Column::make('customer')->title('Customer')->orderable(false)->searchable(false),
            Column::make('submitted_at')->title('Submitted At')->orderable(false)->searchable(false),
            Column::make('implemented_by')->title('Implemented By')->orderable(false)->searchable(false),
            Column::make('status')->title('Status')->orderable(false)->searchable(false),
            Column::computed('action')
                  ->title('Actions')
                  ->exportable(false)
                  ->printable(false)
                  ->width(90)
                  ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'UserRegistrations_' . date('YmdHis');
    }
}
