@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration /</span> Roles & Permissions Management
        </h4>

        <!-- Quick Stats Cards -->
        <div class="container-fluid mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>{{ $roles->count() }}</h5>
                            <p class="mb-0">Total Roles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>{{ $totalPermissions }}</h5>
                            <p class="mb-0">Total Permissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>{{ $rolesWithoutPermissions }}</h5>
                            <p class="mb-0">Roles Without Permissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>{{ $roles->sum(function($role) { return $role->users->count(); }) }}</h5>
                            <p class="mb-0">Total Assigned Users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Roles & Permissions Overview</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Create Role
                    </a>
                    <span class="badge bg-success">{{ $roles->count() }} Total Roles</span>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table id="rolesTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Role Name</th>
                                <th>Users Count</th>
                                <th>Permissions Count</th>
{{--                                <th>Key Permissions</th>--}}
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($role->name === 'SuperAdmin')
                                                <i class="bx bx-shield-alt text-danger" style="font-size: 1.5rem;"></i>
                                            @elseif(str_contains($role->name, 'Admin'))
                                                <i class="bx bx-user-check text-warning" style="font-size: 1.5rem;"></i>
                                            @elseif(str_contains($role->name, 'Head'))
                                                <i class="bx bx-crown text-info" style="font-size: 1.5rem;"></i>
                                            @elseif(str_contains($role->name, 'Read Only'))
                                                <i class="bx bx-show text-secondary" style="font-size: 1.5rem;"></i>
                                            @else
                                                <i class="bx bx-user text-primary" style="font-size: 1.5rem;"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $role->name }}</h6>
                                            @if($role->name === 'SuperAdmin')
                                                <span class="badge bg-danger me-1">Super Admin</span>
                                            @elseif(str_contains($role->name, 'Admin'))
                                                <span class="badge bg-warning text-dark me-1">Administrator</span>
                                            @elseif(str_contains($role->name, 'Head'))
                                                <span class="badge bg-info me-1">Department Head</span>
                                            @elseif(str_contains($role->name, 'Read Only'))
                                                <span class="badge bg-secondary me-1">Read Only</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2">{{ $role->users->count() }}</span>
                                        <small class="text-muted">users</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">{{ $role->permissions->count() }}</span>
                                        <small class="text-muted">permissions</small>
                                    </div>
                                </td>
{{--                                <td>--}}
{{--                                    @php--}}
{{--                                        $keyPermissions = $role->permissions->take(2);--}}
{{--                                        $remainingCount = $role->permissions->count() - 2;--}}
{{--                                    @endphp--}}
{{--                                    @foreach($keyPermissions as $permission)--}}
{{--                                        <span class="badge bg-light text-dark me-1 mb-1 small">{{ $permission->name }}</span>--}}
{{--                                    @endforeach--}}
{{--                                    @if($remainingCount > 0)--}}
{{--                                        <span class="text-muted small">+{{ $remainingCount }} more</span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
                                <td>
                                    @if($role->permissions->count() > 0)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">No Permissions</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bx bx-cog"></i> Manage
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('roles.show', $role) }}">
                                                <i class="bx bx-edit-alt me-2"></i> Edit Permissions
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="viewRoleUsers({{ $role->id }}, '{{ addslashes($role->name) }}')">
                                                <i class="bx bx-user me-2"></i> View Users ({{ $role->users->count() }})
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#rolesTable').DataTable({
        responsive: true,
        order: [[1, 'desc']],
        pageLength: 10,
        language: {
            search: "Search roles:",
            lengthMenu: "Show _MENU_ roles per page",
            info: "Showing _START_ to _END_ of _TOTAL_ roles"
        }
    });
});

function viewRoleUsers(roleId, roleName) {
    Swal.fire({
        title: `Users with Role: ${roleName}`,
        text: 'User listing feature coming soon...',
        icon: 'info'
    });
}
</script>
@endpush
