<?php

namespace App\DataTables;

use App\Models\DocumentRegistrationEntry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class HomeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<DocumentRegistrationEntry> $query
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dt = new EloquentDataTable($query);

        $dt->addColumn('document_details', function ($row) {
            $html = '<strong>' . e($row->document_title) . '</strong><br>';
            $html .= '<small class="text-muted">' . e($row->document_no) . ' Rev. ' . e($row->revision_no) . '</small>';
            if ($row->customer) {
                $html .= '<br><small class="text-info">Customer: ' . e($row->customer->name) . '</small>';
            }
            return $html;
        });

        $dt->addColumn('submitted_by', function ($row) {
            $time = $row->submitted_at ? $row->submitted_at->format('m/d/Y g:i A') : '-';
            $name = $row->submittedBy ? e($row->submittedBy->name) : '-';
            return $name . '<br><small class="text-muted">' . $time . '</small>';
        });

        $dt->addColumn('status_label', function ($row) {
            $statusName = $row->status ? e($row->status->name) : '-';
            return '<span class="badge bg-warning text-dark"><i class="bx bx-time"></i> ' . $statusName . '</span>';
        });

        $dt->addColumn('action', function ($row) {
            $viewUrl = route('document-registry.show', $row);
            $html = '<div class="dropdown">';
            $html .= '<button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">';
            $html .= '<i class="bx bx-cog"></i> Manage</button>';
            $html .= '<div class="dropdown-menu">';
            $html .= '<a class="dropdown-item" href="' . $viewUrl . '"><i class="bx bx-show me-2"></i> View Details</a>';

            // Edit button (server-side permissions)
            if ($row->status && $row->status->name === 'Pending' &&
                $row->submitted_by === auth()->id() &&
                auth()->user()->can('edit document registration details')) {
                $editUrl = route('document-registry.edit', $row);
                $html .= '<a class="dropdown-item" href="' . $editUrl . '"><i class="bx bx-edit-alt me-2"></i> Edit</a>';
            }

            // Withdraw form
            if ($row->status && $row->status->name === 'Pending' &&
                $row->submitted_by === auth()->id() &&
                auth()->user()->can('withdraw document submission')) {
                $withdrawUrl = route('document-registry.withdraw', $row);
                $html .= '<form action="' . $withdrawUrl . '" method="POST" onsubmit="return confirm(\'Are you sure you want to withdraw this submission?\')" class="px-3 py-1">';
                $html .= csrf_field() . method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-link text-danger p-0"><i class="bx bx-trash me-2"></i> Withdraw</button>';
                $html .= '</form>';
            }

            $html .= '</div></div>';
            return $html;
        });

        $dt->rawColumns(['document_details', 'submitted_by', 'status_label', 'action']);

        return $dt->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @param DocumentRegistrationEntry $model
     * @return QueryBuilder<DocumentRegistrationEntry>
     */
    public function query(DocumentRegistrationEntry $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['submittedBy', 'status', 'customer'])
            ->whereHas('status', function ($q) {
                $q->where('name', 'Pending');
            })
            ->latest('submitted_at');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('documentRegistry')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2, 'desc')
            ->pageLength(10)
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
            Column::make('control_no')->title('Control No.'),
            Column::computed('document_details')->title('Document Details')->orderable(false)->searchable(false),
            Column::make('originator_name')->title('Originator'),
            Column::make('device_name')->title('Device Name'),
            Column::computed('submitted_by')->title('Submitted By')->orderable(false)->searchable(false),
            Column::computed('status_label')->title('Status')->orderable(false)->searchable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Home_' . date('YmdHis');
    }
}
