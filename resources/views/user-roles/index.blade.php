@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration /</span> User Roles & Permissions
        </h4>

        <!-- Quick Stats Cards -->
        <div class="container-fluid mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>{{ $roles->count() }}</h5>
                            <p class="mb-0">Total Roles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>{{ $usersWithoutRoles}}</h5>
                            <p class="mb-0">Users Without Roles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>{{ \Spatie\Permission\Models\Permission::count() }}</h5>
                            <p class="mb-0">Total Permissions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Users with Roles & Permissions</h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-success">{{ $users->count() }} Total Users</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Permissions Count</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px;">
                                                <img
                                                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                                    alt="{{ $user->name }}"
                                                    class="img-fluid"
                                                    style="width: 100%; height: 100%; object-fit: cover;"
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">ID: {{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-primary">{{ $user->email }}</span>
                                    @if($user->email_verified_at)
                                        <i class="bx bx-check-circle text-success ms-1" title="Verified"></i>
                                    @else
                                        <i class="bx bx-x-circle text-danger ms-1" title="Unverified"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            @php
                                                $badgeClass = match($role->name) {
                                                    'SuperAdmin' => 'bg-danger',
                                                    'DCCAdmin' => 'bg-warning',
                                                    'VP Sales and Operations' => 'bg-info',
                                                    'Comptroller' => 'bg-success',
                                                    default => 'bg-primary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} me-1 mb-1">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">No Role Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-2">{{ $user->getAllPermissions()->count() }}</span>
                                        <small class="text-muted">permissions</small>
                                    </div>
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $user->created_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bx bx-cog"></i> Manage
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">
                                                <i class="bx bx-show me-2"></i> View Details
                                            </a>
                                            <a class="dropdown-item" href="#" onclick="editUserRoles({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->roles->pluck('id') }})">
                                                <i class="bx bx-edit-alt me-2"></i> Edit Roles
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="viewUserActivity({{ $user->id }})">
                                                <i class="bx bx-time me-2"></i> Activity Log
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
    $('#usersTable').DataTable({
        responsive: true,
        order: [[5, 'desc']],
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ],
        language: {
            search: "Search users:",
            lengthMenu: "Show _MENU_ users per page",
            info: "Showing _START_ to _END_ of _TOTAL_ users"
        }
    });
});

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
