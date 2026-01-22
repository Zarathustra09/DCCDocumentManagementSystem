<?php

namespace App\DataTables;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CustomersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Customer> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // build into a variable so we can attach a custom server-side filter (same pattern as RegistrationsDataTable)
        $dataTable = (new EloquentDataTable($query))
            // use editColumn for DB-backed 'name' so server-side searching maps to DB column
            ->editColumn('name', fn(Customer $c) => e($c->name))
            ->addColumn('code', fn(Customer $c) => '<span class="badge bg-info">' . e($c->code) . '</span>')
            ->addColumn('status', function (Customer $c) {
                return $c->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('created_at', fn(Customer $c) => optional($c->created_at)->format('m/d/Y g:i A') ?: '-')
            ->addColumn('action', function (Customer $c) {
                return '
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary"
                            onclick="editCustomer(' . $c->id . ', \'' . addslashes($c->name) . '\', \'' . addslashes($c->code) . '\', ' . ($c->is_active ? 'true' : 'false') . ')">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger"
                            onclick="deleteCustomer(' . $c->id . ', \'' . addslashes($c->name) . '\')">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['code', 'status', 'action'])
            ->setRowId('id');

        // Attach server-side global search handling (reads DataTables' search.value)
        $dataTable->filter(function (QueryBuilder $q) {
            $searchValue = $this->request()->input('search.value', '');
            $sv = trim((string) $searchValue);

            if ($sv !== '') {
                $q->where(function ($qq) use ($sv) {
                    $qq->where('customers.name', 'like', "%{$sv}%")
                       ->orWhere('customers.code', 'like', "%{$sv}%");
                });
            }
        }, false); // false prevents default global search from being applied in addition to this filter

        return $dataTable;
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Customer>
     */
    public function query(Customer $model): QueryBuilder
    {
        // ensure selecting customers.* so where clauses reference the correct table columns
        return $model->newQuery()->select('customers.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('customersTable')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    // after moving 'action' to the end, order by first column (name)
                    ->orderBy(0)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('reset'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Name'),
            Column::make('code')->title('Code'),
            Column::make('status')->title('Status')->orderable(false)->searchable(false),
            Column::make('created_at')->title('Created At'),
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
        return 'Customers_' . date('YmdHis');
    }
}
