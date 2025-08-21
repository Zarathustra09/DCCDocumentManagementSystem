@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Quick Stats Cards -->
            <div class="container-fluid mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-white">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0 fw-bold">{{ $roles->count() }}</h4>
                                        <p class="mb-0 opacity-75">Total Roles</p>
                                    </div>
                                    <div class="ms-3">
                                        <i class="bx bx-user-check" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body text-white">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0 fw-bold">{{ $totalPermissions }}</h4>
                                        <p class="mb-0 opacity-75">Total Permissions</p>
                                    </div>
                                    <div class="ms-3">
                                        <i class="bx bx-shield-quarter" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="card-body text-white">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0 fw-bold">{{ $rolesWithoutPermissions }}</h4>
                                        <p class="mb-0 opacity-75">Roles Without Permissions</p>
                                    </div>
                                    <div class="ms-3">
                                        <i class="bx bx-error-circle" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                            <div class="card-body text-dark">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0 fw-bold">{{ $roles->sum(function($role) { return $role->users->count(); }) }}</h4>
                                        <p class="mb-0 opacity-75">Total Assigned Users</p>
                                    </div>
                                    <div class="ms-3">
                                        <i class="bx bx-group" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-user-check'></i> Roles & Permissions Management</h3>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class='bx bx-plus'></i> Create Role
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="rolesTable">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Users Count</th>
                                    <th>Permissions Count</th>
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
                                                <strong>{{ $role->name }}</strong>
                                                <br>
                                                @if($role->name === 'SuperAdmin')
                                                    <small class="text-muted">Super Admin</small>
                                                @elseif(str_contains($role->name, 'Admin'))
                                                    <small class="text-muted">Administrator</small>
                                                @elseif(str_contains($role->name, 'Head'))
                                                    <small class="text-muted">Department Head</small>
                                                @elseif(str_contains($role->name, 'Read Only'))
                                                    <small class="text-muted">Read Only</small>
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
                                    <td>
                                        @if($role->permissions->count() > 0)
                                            <span class="badge bg-success">
                                                <i class='bx bx-check'></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class='bx bx-time'></i> No Permissions
                                            </span>
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
</div>

<style>
.badge {
    font-size: 0.85em;
    padding: 0.375rem 0.75rem;
}
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}
.badge.bg-success {
    background-color: #198754 !important;
    color: #ffffff !important;
}
.badge.bg-danger {
    background-color: #dc3545 !important;
    color: #ffffff !important;
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#rolesTable').DataTable({
        responsive: true,
        order: [[1, 'desc']],
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [0, 4] }
        ],
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
