@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Quick Stats Cards -->
        <div class="container-fluid mb-4">
            <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0 fw-bold">{{ $usersWithoutRoles}}</h4>
                                    <p class="mb-0 opacity-75">Users Without Roles</p>
                                </div>
                                <div class="ms-3">
                                    <i class="bx bx-user-x" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0 fw-bold">{{ \Spatie\Permission\Models\Permission::count() }}</h4>
                                    <p class="mb-0 opacity-75">Total Permissions</p>
                                </div>
                                <div class="ms-3">
                                    <i class="bx bx-shield-quarter" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class='bx bx-user-circle'></i> User Roles & Permissions</h3>
                <span class="badge bg-success">{{ $usersCount }} Total Users</span>
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
    {!! $dataTable->scripts() !!}

<script>

function editUserRoles(userId, userName, currentRoles) {
    const roles = @json($roles);

    let rolesOptions = '';
    roles.forEach(role => {
        const isSelected = currentRoles.includes(role.id) ? 'selected' : '';
        rolesOptions += `<option value="${role.id}" ${isSelected}>${role.name}</option>`;
    });

    Swal.fire({
        title: `Edit Roles for ${userName}`,
        html: `
            <div class="mb-3 text-start">
                <label class="form-label fw-bold">Select Roles</label>
                <select id="userRoles" class="form-select" multiple style="height: 200px;">
                    ${rolesOptions}
                </select>
                <small class="text-muted">Hold Ctrl/Cmd to select multiple roles</small>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="bx bx-check"></i> Update Roles',
        cancelButtonText: '<i class="bx bx-x"></i> Cancel',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        },
        preConfirm: () => {
            const selectedRoles = Array.from(document.getElementById('userRoles').selectedOptions)
                .map(option => option.value);

            return { roles: selectedRoles };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateUserRoles(userId, result.value.roles);
        }
    });
}

function updateUserRoles(userId, roles) {
    fetch(`/admin/users/${userId}/roles`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ roles: roles })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'User roles updated successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Error!', 'Failed to update user roles.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'An error occurred while updating roles.', 'error');
    });
}

function viewUserActivity(userId) {
    Swal.fire({
        title: 'User Activity',
        text: 'Activity logging feature coming soon...',
        icon: 'info'
    });
}
</script>
@endpush
