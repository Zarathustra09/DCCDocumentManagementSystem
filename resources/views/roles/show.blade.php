@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Administration / User Management /</span>
                {{ $user->name }}
            </h4>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Back to Users
            </a>
        </div>

        <div class="row">
            <!-- User Info Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar avatar-lg mb-3">
                            <div class="rounded-circle overflow-hidden" style="width: 60px; height: 60px; margin: 0 auto;">
                                <img
                                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                    alt="{{ $user->name }}"
                                    class="img-fluid"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                />
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted small mb-3">{{ $user->email }}</p>

                        <!-- Quick Stats -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <span class="badge bg-primary">{{ $user->roles->count() }}</span>
                                <small class="d-block text-muted">Roles</small>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-info">{{ $directPermissions->count() }}</span>
                                <small class="d-block text-muted">Direct</small>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-success">{{ $allPermissions->count() }}</span>
                                <small class="d-block text-muted">Total</small>
                            </div>
                        </div>

                        <!-- Current Roles -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Current Roles</h6>
                                <button type="button" class="btn btn-xs btn-outline-primary" onclick="editUserRoles({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->roles->pluck('id') }})">
                                    <i class="bx bx-edit"></i>
                                </button>
                            </div>
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
                                    <span class="badge {{ $badgeClass }} me-1 mb-1 small">{{ $role->name }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary small">No Role Assigned</span>
                            @endif
                        </div>

                        <!-- User Details -->
                        <hr>
                        <div class="text-start">
                            <small class="text-muted d-block">ID: {{ $user->id }}</small>
                            <small class="text-muted d-block">Created: {{ $user->created_at->format('M d, Y') }}</small>
                            <small class="text-muted d-block">Status:
                                @if($user->email_verified_at)
                                    <span class="badge bg-success badge-sm">Active</span>
                                @else
                                    <span class="badge bg-warning badge-sm">Pending</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Management -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Direct Permissions Management</h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetPermissions()">
                                    <i class="bx bx-reset"></i> Reset
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="savePermissionsBtn" onclick="saveUserPermissions()">
                                    <i class="bx bx-save"></i> Save
                                </button>
                            </div>
                        </div>
                        <!-- Search Filter -->
                        <div class="mt-3">
                            <input type="text" id="permissionSearch" class="form-control form-control-sm" placeholder="Search permissions..." onkeyup="filterPermissions()">
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        @php
                            $groupedPermissions = $permissions->groupBy(function($permission) {
                                $parts = explode(' ', $permission->name);
                                return count($parts) > 1 ? $parts[1] : 'System';
                            });
                        @endphp

                        <div class="row" id="permissionsContainer">
                            @foreach($groupedPermissions as $department => $departmentPermissions)
                                <div class="col-md-6 mb-3 permission-group" data-department="{{ strtolower($department) }}">
                                    <div class="card border-light">
                                        <div class="card-header bg-light py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 small">{{ $department }}</h6>
                                                <div>
                                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="selectAllInGroup('{{ $department }}')">All</button>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="selectNoneInGroup('{{ $department }}')">None</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body py-2">
                                            @foreach($departmentPermissions as $permission)
                                                <div class="form-check form-check-sm mb-1 permission-item" data-permission="{{ strtolower($permission->name) }}">
                                                    <input
                                                        class="form-check-input permission-checkbox"
                                                        type="checkbox"
                                                        value="{{ $permission->id }}"
                                                        id="permission_{{ $permission->id }}"
                                                        data-group="{{ $department }}"
                                                        {{ $allPermissions->contains('id', $permission->id) ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label small" for="permission_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                        @if($directPermissions->contains('id', $permission->id) && $rolePermissions->contains('id', $permission->id))
                                                            <span class="badge bg-warning text-dark badge-sm ms-1">Both</span>
                                                        @elseif($directPermissions->contains('id', $permission->id))
                                                            <span class="badge bg-info badge-sm ms-1">Direct</span>
                                                        @elseif($rolePermissions->contains('id', $permission->id))
                                                            <span class="badge bg-success badge-sm ms-1">Role</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- No Results Message -->
                        <div id="noResultsMessage" class="text-center text-muted py-3" style="display: none;">
                            <i class="bx bx-search"></i>
                            <p class="mb-0">No permissions found matching your search.</p>
                        </div>
                    </div>

                    <!-- Summary Footer -->
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <span id="selectedCount">{{ $allPermissions->count() }}</span> of {{ $permissions->count() }} permissions selected
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    Direct: <span class="badge bg-info badge-sm">{{ $directPermissions->count() }}</span>
                                    Via Roles: <span class="badge bg-success badge-sm">{{ $rolePermissions->count() }}</span>
                                </small>
                            </div>
                        </div>
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
            updateSelectedCount();
        });
    });

    updateSelectedCount();
});

function filterPermissions() {
    const searchTerm = document.getElementById('permissionSearch').value.toLowerCase();
    const permissionGroups = document.querySelectorAll('.permission-group');
    const permissionItems = document.querySelectorAll('.permission-item');
    let hasVisibleResults = false;

    if (searchTerm === '') {
        // Show all groups and items
        permissionGroups.forEach(group => {
            group.style.display = 'block';
            const items = group.querySelectorAll('.permission-item');
            items.forEach(item => item.style.display = 'block');
        });
        hasVisibleResults = true;
    } else {
        permissionGroups.forEach(group => {
            let hasVisibleItems = false;
            const items = group.querySelectorAll('.permission-item');

            items.forEach(item => {
                const permissionName = item.dataset.permission;
                if (permissionName.includes(searchTerm)) {
                    item.style.display = 'block';
                    hasVisibleItems = true;
                    hasVisibleResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            group.style.display = hasVisibleItems ? 'block' : 'none';
        });
    }

    document.getElementById('noResultsMessage').style.display = hasVisibleResults ? 'none' : 'block';
}

function selectAllInGroup(department) {
    const checkboxes = document.querySelectorAll(`input[data-group="${department}"]`);
    checkboxes.forEach(checkbox => {
        if (checkbox.closest('.permission-item').style.display !== 'none') {
            checkbox.checked = true;
        }
    });
    checkForPermissionChanges();
    updateSelectedCount();
}

function selectNoneInGroup(department) {
    const checkboxes = document.querySelectorAll(`input[data-group="${department}"]`);
    checkboxes.forEach(checkbox => {
        if (checkbox.closest('.permission-item').style.display !== 'none') {
            checkbox.checked = false;
        }
    });
    checkForPermissionChanges();
    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.permission-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = selectedCount;
}

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
        saveBtn.innerHTML = '<i class="bx bx-save"></i> Save';
    }
}

function arraysEqual(a, b) {
    return a.length === b.length && a.every((val, i) => val === b[i]);
}

function resetPermissions() {
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    permissionCheckboxes.forEach(checkbox => {
        checkbox.checked = originalPermissions.includes(parseInt(checkbox.value));
    });

    checkForPermissionChanges();
    updateSelectedCount();

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
</script>
@endpush
