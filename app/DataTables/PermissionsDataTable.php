<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PermissionsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Build into a variable so we can attach a custom filter (same pattern as RegistrationsDataTable)
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('name', function (User $user) {
                // Changed: use internal image host + ltrim, fallback to placeholder
                $avatarUrl = $user->profile_image
                    ? 'http://172.16.0.3:8012/images/' . ltrim($user->profile_image, '/')
                    : 'https://placehold.co/40';

                return '
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px;">
                                <img src="' . $avatarUrl . '" alt="' . e($user->name) . '" class="img-fluid" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        </div>
                        <div>
                            <strong>' . e($user->name) . '</strong><br><small class="text-muted">Employee #: ' . $user->employee_no . '</small>
                        </div>
                    </div>
                ';
            })
            ->addColumn('email', function (User $user) {
                $badge = $user->email_verified_at
                    ? '<i class="bx bx-check-circle text-success ms-1" title="Verified"></i>'
                    : '<i class="bx bx-x-circle text-danger ms-1" title="Unverified"></i>';
                return '<span class="text-primary">' . e($user->email) . '</span> ' . $badge;
            })
            ->addColumn('roles', function (User $user) {
                if ($user->roles->count() === 0) {
                    return '<span class="badge bg-secondary">No Role Assigned</span>';
                }
                $badges = $user->roles->map(function ($role) {
                    $badgeClass = match ($role->name) {
                        'SuperAdmin' => 'bg-danger',
                        'DCCAdmin' => 'bg-warning',
                        'VP Sales and Operations' => 'bg-info',
                        'Comptroller' => 'bg-success',
                        default => 'bg-primary'
                    };
                    return '<span class="badge ' . $badgeClass . ' me-1 mb-1">' . e($role->name) . '</span>';
                })->implode(' ');
                return $badges;
            })
            ->addColumn('permissions_count', function (User $user) {
                return '<div class="d-flex align-items-center"><span class="badge bg-light text-dark me-2">' . $user->getAllPermissions()->count() . '</span><small class="text-muted">permissions</small></div>';
            })
            ->addColumn('status', function (User $user) {
                if ($user->email_verified_at) {
                    return '<span class="badge bg-success"><i class="bx bx-check"></i> Active</span>';
                }
                return '<span class="badge bg-warning text-dark"><i class="bx bx-time"></i> Pending</span>';
            })
            ->addColumn('created', function (User $user) {
                $invalidDates = [
                    '0000-00-00',
                    '0000-00-00 00:00:00',
                    '2025-02-11 00:00:00',
                ];
                $createdOnRaw = $user->getRawOriginal('created_on');
                $validCreatedOn = $user->created_on && !in_array($createdOnRaw, $invalidDates, true);
                $display = $validCreatedOn ? optional($user->created_on)->format('M d, Y') : 'N/A';
                return '<small><i class="bx bx-calendar"></i> ' . $display . '</small>';
            })
            ->addColumn('action', function (User $user) {
                $roleIdsJson = json_encode($user->roles->pluck('id')->values());
                $viewUrl = route('admin.users.show', $user->id);
                $html = '
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bx bx-cog"></i> Manage
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="' . $viewUrl . '">
                                <i class="bx bx-show me-2"></i> View Details
                            </a>
                            <a class="dropdown-item" href="#" onclick="editUserRoles(' . $user->id . ', \'' . addslashes($user->name) . '\', ' . $roleIdsJson . ')">
                                <i class="bx bx-edit-alt me-2"></i> Edit Roles
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="viewUserActivity(' . $user->id . ')">
                                <i class="bx bx-time me-2"></i> Activity Log
                            </a>
                        </div>
                    </div>
                ';
                return $html;
            })
            ->rawColumns(['name', 'email', 'roles', 'permissions_count', 'status', 'created', 'action'])
            ->setRowId('id');

        // Attach server-side global search handling (reads DataTables' search.value)
        $dataTable->filter(function (QueryBuilder $q) {
            $request = $this->request();

            $searchPayload = $request->input('search');
            $globalSearch = is_array($searchPayload)
                ? trim($searchPayload['value'] ?? '')
                : trim((string) $searchPayload);

            if ($globalSearch !== '') {
                $sv = $globalSearch;
                $q->where(function ($qq) use ($sv) {
                    // search using actual DB columns (firstname/lastname) and other fields
                    $qq->where('users.firstname', 'like', "%{$sv}%")
                       ->orWhere('users.lastname', 'like', "%{$sv}%")
                       ->orWhere('users.email', 'like', "%{$sv}%")
                       ->orWhere('users.username', 'like', "%{$sv}%")
                       ->orWhere('users.employee_no', 'like', "%{$sv}%")
                       ->orWhereHas('roles', function ($rq) use ($sv) {
                           $rq->where('name', 'like', "%{$sv}%");
                       });
                });
            }
        }, false); // false prevents default global search from being applied in addition to this filter

        return $dataTable;
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        // ensure selecting users.* and add a computed name alias so DataTables can sort/search "name" column
        return $model->newQuery()
            ->with('roles')
            ->select('users.*', DB::raw("CONCAT(COALESCE(users.firstname, ''), ' ', COALESCE(users.lastname, '')) as name"));
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('usersTable')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(5, 'desc')
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
            Column::make('name')->title('User'),
            Column::make('email'),
            Column::make('roles'),
            Column::make('permissions_count')->title('Permissions Count'),
            Column::make('status'),
            Column::make('created')->title('Created'),
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
        return 'Permissions_' . date('YmdHis');
    }
}
