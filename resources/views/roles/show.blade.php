@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Administration / User Management /</span>
                {{ $user->name }} - Permissions
            </h4>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Back to Users
            </a>
        </div>

        <div class="row">
            <!-- User Info Card -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="avatar avatar-xl mb-3">
                            <div class="rounded-circle overflow-hidden" style="width: 80px; height: 80px; margin: 0 auto;">
                                <img
                                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=80' }}"
                                    alt="{{ $user->name }}"
                                    class="img-fluid"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                />
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-3">{{ $user->email }}</p>

                        <!-- User Stats -->
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <h6 class="mb-0">{{ $user->roles->count() }}</h6>
                                <small class="text-muted">Roles</small>
                            </div>
                            <div class="col-4">
                                <h6 class="mb-0">{{ $directPermissions->count() }}</h6>
                                <small class="text-muted">Direct Permissions</small>
                            </div>
                            <div class="col-4">
                                <h6 class="mb-0">{{ $allPermissions->count() }}</h6>
                                <small class="text-muted">Total Permissions</small>
                            </div>
                        </div>

                        <!-- Current Roles -->
                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Current Roles</h6>
                                <button class="btn btn-sm btn-light" onclick="editUserRoles({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->roles->pluck('id') }})">
                                    <i class="bx bx-edit"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        @php
                                            $badgeClass = match($role->name) {
                                                'SuperAdmin' => 'bg-danger',
                                                'DCCAdmin' => 'bg-warning text-dark',
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
                            </div>
                        </div>

                        <!-- User Info -->
                        <hr>
                        <div class="text-start">
                            <small class="text-muted d-block">User ID: {{ $user->id }}</small>
                            <small class="text-muted d-block">Created: {{ $user->created_at->format('M d, Y') }}</small>
                            <small class="text-muted d-block">Status:
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Management -->
            <div class="col-md-8">
                <!-- Direct Permissions Management -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Direct Permissions Management</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetPermissions()">
                                <i class="bx bx-reset"></i> Reset Changes
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="savePermissionsBtn" onclick="saveUserPermissions()">
                                <i class="bx bx-save"></i> Save Permissions
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $groupedPermissions = $permissions->groupBy(function($permission) {
                                $parts = explode(' ', $permission->name);
                                return count($parts) > 1 ? $parts[1] : 'System';
                            });
                        @endphp

                        <div class="row">
                            @foreach($groupedPermissions as $department => $departmentPermissions)
                                <div class="col-md-6 mb-4">
                                    <div class="card border-light">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">{{ $department }} Permissions</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($departmentPermissions as $permission)
                                                <div class="form-check mb-2">
                                                    <input
                                                        class="form-check-input permission-checkbox"
                                                        type="checkbox"
                                                        value="{{ $permission->id }}"
                                                        id="permission_{{ $permission->id }}"
                                                        {{ $allPermissions->contains('id', $permission->id) ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                        @if($directPermissions->contains('id', $permission->id) && $rolePermissions->contains('id', $permission->id))
                                                            <small class="badge bg-warning text-dark ms-1">Both</small>
                                                        @elseif($directPermissions->contains('id', $permission->id))
                                                            <small class="badge bg-info ms-1">Direct</small>
                                                        @elseif($rolePermissions->contains('id', $permission->id))
                                                            <small class="badge bg-success ms-1">Via Role</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Current Direct Permissions Display -->
                        <div class="mt-3">
                            <h6>Current Direct Permissions:</h6>
                            <div id="currentDirectPermissionsDisplay">
                                @if($directPermissions->count() > 0)
                                    <div class="permissions-grid" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($directPermissions as $permission)
                                            <span class="badge bg-info me-1 mb-1">{{ $permission->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No direct permissions assigned.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Overview -->
{{--                <div class="card mb-4">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5 class="mb-0">Permissions Overview</h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-md-6">--}}
{{--                                <h6>Permissions via Roles ({{ $rolePermissions->count() }})</h6>--}}
{{--                                @if($rolePermissions->count() > 0)--}}
{{--                                    <div class="permissions-grid" style="max-height: 200px; overflow-y: auto;">--}}
{{--                                        @foreach($rolePermissions as $permission)--}}
{{--                                            <span class="badge bg-success me-1 mb-1">{{ $permission->name }}</span>--}}
{{--                                        @endforeach--}}
{{--                                    </div>--}}
{{--                                @else--}}
{{--                                    <p class="text-muted">No permissions via roles.</p>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <div class="col-md-6">--}}
{{--                                <h6>Direct Permissions ({{ $directPermissions->count() }})</h6>--}}
{{--                                @if($directPermissions->count() > 0)--}}
{{--                                    <div class="permissions-grid" style="max-height: 200px; overflow-y: auto;">--}}
{{--                                        @foreach($directPermissions as $permission)--}}
{{--                                            <span class="badge bg-info me-1 mb-1">{{ $permission->name }}</span>--}}
{{--                                        @endforeach--}}
{{--                                    </div>--}}
{{--                                @else--}}
{{--                                    <p class="text-muted">No direct permissions assigned.</p>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <!-- All Effective Permissions -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Effective Permissions</h5>
                        <span class="badge bg-info">{{ $allPermissions->count() }} Total</span>
                    </div>
                    <div class="card-body">
                        @if($allPermissions->count() > 0)
                            <div class="permissions-grid" style="max-height: 300px; overflow-y: auto;">
                                @foreach($allPermissions as $permission)
                                    @php
                                        $isDirect = $directPermissions->contains('id', $permission->id);
                                        $isViaRole = $rolePermissions->contains('id', $permission->id);
                                        $badgeClass = $isDirect && $isViaRole ? 'bg-warning text-dark' : ($isDirect ? 'bg-info' : 'bg-success');
                                        $title = $isDirect && $isViaRole ? 'Both Direct and via Role' : ($isDirect ? 'Direct Permission' : 'Via Role');
                                    @endphp
                                    <span class="badge {{ $badgeClass }} me-1 mb-1" title="{{ $title }}">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <span class="badge bg-success me-1">Via Role</span>
                                    <span class="badge bg-info me-1">Direct</span>
                                    <span class="badge bg-warning text-dark me-1">Both</span>
                                </small>
                            </div>
                        @else
                            <p class="text-muted">No permissions assigned to this user.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Store original permissions for reset functionality
const originalPermissions = @json($directPermissions->pluck('id'));
let hasPermissionChanges = false;

// Track changes in permission checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            checkForPermissionChanges();
            updateCurrentDirectPermissionsDisplay();
        });
    });
});

function checkForPermissionChanges() {
    const currentPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => parseInt(cb.value));
    const originalPermissionsArray = originalPermissions.map(id => parseInt(id));

    hasPermissionChanges = !arraysEqual(currentPermissions.sort(), originalPermissionsArray.sort());

    const saveBtn = document.getElementById('savePermissionsBtn');
    if (hasPermissionChanges) {
        saveBtn.classList.remove('btn-primary');
        saveBtn.classList.add('btn-success');
        saveBtn.innerHTML = '<i class="bx bx-save"></i> Save Changes';
    } else {
        saveBtn.classList.remove('btn-success');
        saveBtn.classList.add('btn-primary');
        saveBtn.innerHTML = '<i class="bx bx-save"></i> Save Permissions';
    }
}

function arraysEqual(a, b) {
    return a.length === b.length && a.every((val, i) => val === b[i]);
}

function updateCurrentDirectPermissionsDisplay() {
    const permissions = @json($permissions);
    const checkedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => parseInt(cb.value));
    const currentDirectPermissionsDisplay = document.getElementById('currentDirectPermissionsDisplay');

    if (checkedPermissions.length === 0) {
        currentDirectPermissionsDisplay.innerHTML = '<p class="text-muted mb-0">No direct permissions assigned.</p>';
        return;
    }

    let html = '<div class="permissions-grid" style="max-height: 200px; overflow-y: auto;">';
    checkedPermissions.forEach(permissionId => {
        const permission = permissions.find(p => p.id === permissionId);
        if (permission) {
            html += `<span class="badge bg-info me-1 mb-1">${permission.name}</span>`;
        }
    });
    html += '</div>';

    currentDirectPermissionsDisplay.innerHTML = html;
}

function resetPermissions() {
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    permissionCheckboxes.forEach(checkbox => {
        checkbox.checked = originalPermissions.includes(parseInt(checkbox.value));
    });

    checkForPermissionChanges();
    updateCurrentDirectPermissionsDisplay();

    Swal.fire({
        icon: 'info',
        title: 'Permissions Reset',
        text: 'Permissions have been reset to original values.',
        timer: 1500,
        showConfirmButton: false
    });
}

function saveUserPermissions() {
    if (!hasPermissionChanges) {
        Swal.fire({
            icon: 'info',
            title: 'No Changes',
            text: 'No changes have been made to the permissions.',
            timer: 1500,
            showConfirmButton: false
        });
        return;
    }

    const selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => cb.value);

    const saveBtn = document.getElementById('savePermissionsBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Saving...';

    fetch(`{{ route('admin.users.permissions.update', $user->id) }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ permissions: selectedPermissions })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'User permissions updated successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            throw new Error(data.message || 'Failed to update user permissions');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error!', error.message || 'An error occurred while updating permissions.', 'error');

        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
}

// Keep the role editing as dropdown (SweetAlert modal)
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
    fetch(`{{ route('admin.users.roles.update', $user->id) }}`, {
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
