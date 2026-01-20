@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
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
                    {{-- Yajra-rendered table --}}
                    {!! $dataTable->table(['class' => 'table table-striped table-hover'], true) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
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
    {{-- Yajra scripts --}}
    {!! $dataTable->scripts() !!}

<script>
function viewRoleUsers(roleId, roleName) {
    Swal.fire({
        title: `Users with Role: ${roleName}`,
        text: 'User listing feature coming soon...',
        icon: 'info'
    });
}
</script>
@endpush
