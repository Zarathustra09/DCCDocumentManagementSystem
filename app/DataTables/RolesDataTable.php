<?php

namespace App\DataTables;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RolesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Role> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', function (Role $role) {
                // icon + label + subtitle similar to blade
                $icon = '<i class="bx bx-user text-primary" style="font-size:1.5rem;"></i>';
                if ($role->name === 'SuperAdmin') {
                    $icon = '<i class="bx bx-shield-alt text-danger" style="font-size:1.5rem;"></i>';
                } elseif (str_contains($role->name, 'Admin')) {
                    $icon = '<i class="bx bx-user-check text-warning" style="font-size:1.5rem;"></i>';
                } elseif (str_contains($role->name, 'Head')) {
                    $icon = '<i class="bx bx-crown text-info" style="font-size:1.5rem;"></i>';
                } elseif (str_contains($role->name, 'Read Only')) {
                    $icon = '<i class="bx bx-show text-secondary" style="font-size:1.5rem;"></i>';
                }

                $subtitle = '';
                if ($role->name === 'SuperAdmin') {
                    $subtitle = '<small class="text-muted">Super Admin</small>';
                } elseif (str_contains($role->name, 'Admin')) {
                    $subtitle = '<small class="text-muted">Administrator</small>';
                } elseif (str_contains($role->name, 'Head')) {
                    $subtitle = '<small class="text-muted">Department Head</small>';
                } elseif (str_contains($role->name, 'Read Only')) {
                    $subtitle = '<small class="text-muted">Read Only</small>';
                }

                return '
                    <div class="d-flex align-items-center">
                        <div class="me-3">' . $icon . '</div>
                        <div>
                            <strong>' . e($role->name) . '</strong><br>' . $subtitle . '
                        </div>
                    </div>
                ';
            })
            ->addColumn('users_count', function (Role $role) {
                return '<div class="d-flex align-items-center"><span class="badge bg-primary me-2">' . $role->users->count() . '</span><small class="text-muted">users</small></div>';
            })
            ->addColumn('permissions_count', function (Role $role) {
                return '<div class="d-flex align-items-center"><span class="badge bg-success me-2">' . $role->permissions->count() . '</span><small class="text-muted">permissions</small></div>';
            })
            ->addColumn('status', function (Role $role) {
                if ($role->permissions->count() > 0) {
                    return '<span class="badge bg-success"><i class="bx bx-check"></i> Active</span>';
                }
                return '<span class="badge bg-warning text-dark"><i class="bx bx-time"></i> No Permissions</span>';
            })
            ->addColumn('action', function (Role $role) {
                $html = '
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bx bx-cog"></i> Manage
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="' . route('roles.show', $role) . '">
                                <i class="bx bx-edit-alt me-2"></i> Edit Permissions
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="viewRoleUsers(' . $role->id . ', \'' . addslashes($role->name) . '\')">
                                <i class="bx bx-user me-2"></i> View Users (' . $role->users->count() . ')
                            </a>
                        </div>
                    </div>
                ';
                return $html;
            })
            ->rawColumns(['name', 'users_count', 'permissions_count', 'status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Role>
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()->with(['users', 'permissions']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('rolesTable')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1, 'desc')
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
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
            Column::make('name')->title('Role Name'),
            Column::computed('users_count')->title('Users Count')->exportable(false)->printable(true),
            Column::computed('permissions_count')->title('Permissions Count')->exportable(false)->printable(true),
            Column::make('status'),
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
        return 'Roles_' . date('YmdHis');
    }
}
