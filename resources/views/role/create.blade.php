@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Administration / Role Management /</span>
                Create New Role
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
                                <i class="bx bx-user-plus text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h5 class="mb-1" id="preview-role-name">New Role</h5>
                        <span class="badge bg-primary mb-3">Creating Role</span>

                        <!-- Quick Stats -->
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <span class="badge bg-primary">0</span>
                                <small class="d-block text-muted">Users</small>
                            </div>
                            <div class="col-6">
                                <span class="badge bg-success" id="selected-permission-count">0</span>
                                <small class="d-block text-muted">Permissions</small>
                            </div>
                        </div>

                        <!-- Role Form -->
                        <div class="text-start">
                            <div class="mb-3">
                                <label for="role-name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="role-name" name="name" placeholder="Enter role name" required>
                                <div class="invalid-feedback" id="role-name-error"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-start">
                            <small class="text-muted d-block">Status: <span class="badge bg-warning badge-sm">Draft</span></small>
                            <small class="text-muted d-block">Type: <span class="badge bg-info badge-sm">Custom Role</span></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Management -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Role Permissions Assignment</h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" id="select-all">Select All</button>
                                <button type="button" class="btn btn-sm btn-warning" id="deselect-all">Deselect All</button>
                                <button type="button" class="btn btn-sm btn-primary" id="create-role">
                                    <i class="bx bx-plus"></i> Create Role
                                </button>
                            </div>
                        </div>
                        <!-- Search Filter -->
                        <div class="mt-3">
                            <input type="text" id="permissionSearch" class="form-control form-control-sm" placeholder="Search permissions..." onkeyup="filterPermissions()">
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <form id="role-form">
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
                                    <span id="selectedCount">0</span> of {{ $allPermissions->flatten()->count() }} permissions selected
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
    // Update role name preview
    $('#role-name').on('input', function() {
        const name = $(this).val() || 'New Role';
        $('#preview-role-name').text(name);
    });

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

    // Handle form submission
    $('#create-role').click(function(e) {
        e.preventDefault();
        createRole();
    });

    function updatePermissionCount() {
        var count = $('.permission-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#selected-permission-count').text(count);
    }

    function createRole() {
        const roleName = $('#role-name').val().trim();

        if (!roleName) {
            $('#role-name').addClass('is-invalid');
            $('#role-name-error').text('Role name is required.');
            return;
        }

        $('#role-name').removeClass('is-invalid');

        const selectedPermissions = $('.permission-checkbox:checked').map(function() {
            return parseInt(this.value);
        }).get();

        // Build summary HTML
        let summaryHtml = '<div class="text-start">';
        summaryHtml += `<p class="mb-3">You are about to create role: <strong>${roleName}</strong></p>`;
        summaryHtml += `
            <div class="alert alert-info small mb-2">
                <i class="bx bx-info-circle me-1"></i>
                <strong>Permissions:</strong> ${selectedPermissions.length} of ${$('.permission-checkbox').length} permissions will be assigned
            </div>
            <p class="text-muted small mb-0">
                <i class="bx bx-shield-check me-1"></i>
                The role will be created and ready for user assignment.
            </p>
        </div>`;

        Swal.fire({
            title: 'Create Role?',
            html: summaryHtml,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bx bx-check"></i> Yes, Create Role',
            cancelButtonText: '<i class="bx bx-x"></i> Cancel',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            reverseButtons: true,
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                const submitButton = $('#create-role');
                const originalText = submitButton.html();
                submitButton.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Creating...');

                // Prepare form data
                const formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('name', roleName);
                selectedPermissions.forEach(id => {
                    formData.append('permissions[]', id);
                });

                $.ajax({
                    url: '{{ route("roles.store") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    window.location.href = '{{ route("roles.index") }}';
                                }
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while creating the role.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#role-name').addClass('is-invalid');
                                $('#role-name-error').text(errors.name[0]);
                                errorMessage = errors.name[0];
                            }
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).html(originalText);
                    }
                });
            }
        });
    }

    updatePermissionCount();
});

function filterPermissions() {
    const searchTerm = document.getElementById('permissionSearch').value.toLowerCase();
    const permissionGroups = document.querySelectorAll('.permission-group');
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
</script>
@endpush
