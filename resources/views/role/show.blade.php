@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Administration / Role Management /</span>
                {{ $role->name }}
            </h4>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Back to Roles
            </a>
        </div>

        <div class="row">
            <!-- Role Info Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar avatar-lg mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; margin: 0 auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                @if($role->name === 'SuperAdmin')
                                    <i class="bx bx-shield-alt text-white" style="font-size: 2rem;"></i>
                                @elseif(str_contains($role->name, 'Admin'))
                                    <i class="bx bx-user-check text-white" style="font-size: 2rem;"></i>
                                @elseif(str_contains($role->name, 'Head'))
                                    <i class="bx bx-crown text-white" style="font-size: 2rem;"></i>
                                @elseif(str_contains($role->name, 'Read Only'))
                                    <i class="bx bx-show text-white" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bx bx-user text-white" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $role->name }}</h5>
                        @if($role->name === 'SuperAdmin')
                            <span class="badge bg-danger mb-3">Super Administrator</span>
                        @elseif(str_contains($role->name, 'Admin'))
                            <span class="badge bg-warning text-dark mb-3">Administrator</span>
                        @elseif(str_contains($role->name, 'Head'))
                            <span class="badge bg-info mb-3">Department Head</span>
                        @elseif(str_contains($role->name, 'Read Only'))
                            <span class="badge bg-secondary mb-3">Read Only Access</span>
                        @else
                            <span class="badge bg-primary mb-3">Standard Role</span>
                        @endif

                        <!-- Quick Stats -->
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <span class="badge bg-primary">{{ $role->users->count() }}</span>
                                <small class="d-block text-muted">Users</small>
                            </div>
                            <div class="col-6">
                                <span class="badge bg-success" id="current-permission-count">{{ $role->permissions->count() }}</span>
                                <small class="d-block text-muted">Permissions</small>
                            </div>
                        </div>

                        <!-- Role Users -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Assigned Users</h6>
                            </div>
                            @if($role->users->count() > 0)
                                @foreach($role->users->take(3) as $user)
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="avatar avatar-xs me-2">
                                            <div class="rounded-circle overflow-hidden" style="width: 20px; height: 20px;">
                                                <img
                                                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                                    alt="{{ $user->name }}"
                                                    class="img-fluid"
                                                    style="width: 100%; height: 100%; object-fit: cover;"
                                                />
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $user->name }}</small>
                                    </div>
                                @endforeach
                                @if($role->users->count() > 3)
                                    <small class="text-muted">+{{ $role->users->count() - 3 }} more users</small>
                                @endif
                            @else
                                <small class="text-muted">No users assigned</small>
                            @endif
                        </div>

                        <!-- Role Details -->
                        <hr>
                        <div class="text-start">
                            <small class="text-muted d-block">ID: {{ $role->id }}</small>
                            <small class="text-muted d-block">Created: {{ $role->created_at ? $role->created_at->format('M d, Y') : 'System Role' }}</small>
                            <small class="text-muted d-block">Status:
                                @if($role->permissions->count() > 0)
                                    <span class="badge bg-success badge-sm">Active</span>
                                @else
                                    <span class="badge bg-warning badge-sm">No Permissions</span>
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
                            <h5 class="mb-0">Role Permissions Management</h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" id="select-all">Select All</button>
                                <button type="button" class="btn btn-sm btn-warning" id="deselect-all">Deselect All</button>
                                <button type="button" class="btn btn-sm btn-primary" id="save-permissions">
                                    <i class="bx bx-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                        <!-- Search Filter -->
                        <div class="mt-3">
                            <input type="text" id="permissionSearch" class="form-control form-control-sm" placeholder="Search permissions..." onkeyup="filterPermissions()">
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <form id="permission-form">
                            @csrf
                            <div class="row" id="permissionsContainer">
                                @foreach($allPermissions as $category => $permissions)
                                    <div class="col-md-6 mb-3 permission-group" data-category="{{ strtolower($category) }}">
                                        <div class="card border-light">
                                            <div class="card-header bg-light py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 small">{{ $category }}</h6>
                                                    <div>
                                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="selectAllInGroup('{{ $category }}')">All</button>
                                                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="selectNoneInGroup('{{ $category }}')">None</button>
                                                        <span class="badge badge-info small">{{ $permissions->count() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body py-2">
                                                @foreach($permissions as $permission)
                                                    <div class="form-check form-check-sm mb-1 permission-item" data-permission="{{ strtolower($permission->name) }}">
                                                        <input
                                                            class="form-check-input permission-checkbox"
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $permission->id }}"
                                                            id="permission_{{ $permission->id }}"
                                                            data-group="{{ $category }}"
                                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                        >
                                                        <label class="form-check-label small" for="permission_{{ $permission->id }}">
                                                            {{ $permission->name }}
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
                        </form>
                    </div>

                    <!-- Summary Footer -->
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <span id="selectedCount">{{ $role->permissions->count() }}</span> of {{ $allPermissions->flatten()->count() }} permissions selected
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    Categories: <span class="badge bg-info badge-sm">{{ $allPermissions->count() }}</span>
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
$(document).ready(function() {
    // Select All functionality
    $('#select-all').click(function() {
        $('.permission-checkbox').prop('checked', true);
        updatePermissionCount();
    });

    // Deselect All functionality
    $('#deselect-all').click(function() {
        $('.permission-checkbox').prop('checked', false);
        updatePermissionCount();
    });

    // Update permission count when checkboxes change
    $('.permission-checkbox').change(function() {
        updatePermissionCount();
    });

    // Update permission count display
    function updatePermissionCount() {
        var count = $('.permission-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#current-permission-count').text(count);
    }

    // Handle form submission
    $('#permission-form').submit(function(e) {
        e.preventDefault();
        savePermissions();
    });

    $('#save-permissions').click(function(e) {
        e.preventDefault();
        savePermissions();
    });

    function savePermissions() {
        var formData = $('#permission-form').serialize();
        var submitButton = $('#save-permissions');

        // Disable submit button and show loading
        submitButton.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Saving...');

        $.ajax({
            url: '{{ route("roles.update-permissions", $role) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#current-permission-count').text(response.permissionCount);
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                var errorMessage = 'An error occurred while updating permissions.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMessage, 'error');
            },
            complete: function() {
                // Re-enable submit button
                submitButton.prop('disabled', false).html('<i class="bx bx-save"></i> Save Changes');
            }
        });
    }

    updatePermissionCount();
});

function filterPermissions() {
    const searchTerm = document.getElementById('permissionSearch').value.toLowerCase();
    const permissionGroups = document.querySelectorAll('.permission-group');
    const permissionItems = document.querySelectorAll('.permission-item');
    let hasVisibleResults = false;

    if (searchTerm === '') {
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

function selectAllInGroup(category) {
    const checkboxes = document.querySelectorAll(`input[data-group="${category}"]`);
    checkboxes.forEach(checkbox => {
        if (checkbox.closest('.permission-item').style.display !== 'none') {
            checkbox.checked = true;
        }
    });
    updatePermissionCount();
}

function selectNoneInGroup(category) {
    const checkboxes = document.querySelectorAll(`input[data-group="${category}"]`);
    checkboxes.forEach(checkbox => {
        if (checkbox.closest('.permission-item').style.display !== 'none') {
            checkbox.checked = false;
        }
    });
    updatePermissionCount();
}

function updatePermissionCount() {
    var count = $('.permission-checkbox:checked').length;
    $('#selectedCount').text(count);
    $('#current-permission-count').text(count);
}
</script>
@endpush
