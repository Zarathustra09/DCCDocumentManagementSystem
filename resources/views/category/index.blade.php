@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-category'></i> Category Management</h3>
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class='bx bx-plus'></i> Add Category
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td><span class="badge bg-info">{{ $category->code }}</span></td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $category->created_at->format('m/d/Y g:i A') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->code }}', {{ $category->is_active ? 'true' : 'false' }})">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openCreateModal() {
    Swal.fire({
        title: 'Add New Category',
        html: `
            <form id="categoryForm">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Code (max 3 chars)</label>
                    <input type="text" class="form-control" name="code" maxlength="3" required>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create',
        preConfirm: () => {
            const form = document.getElementById('categoryForm');
            const formData = new FormData(form);
            return fetch('{{ route("categories.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    code: formData.get('code'),
                    is_active: formData.get('is_active') ? true : false
                })
            }).then(response => response.json());
        }
    }).then((result) => {
        if (result.isConfirmed && result.value.success) {
            Swal.fire('Success!', result.value.message, 'success').then(() => {
                location.reload();
            });
        }
    });
}

function editCategory(id, name, code, isActive) {
    Swal.fire({
        title: 'Edit Category',
        html: `
            <form id="editCategoryForm">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="${name}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Code (max 3 chars)</label>
                    <input type="text" class="form-control" name="code" value="${code}" maxlength="3" required>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" ${isActive ? 'checked' : ''}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
            const form = document.getElementById('editCategoryForm');
            const formData = new FormData(form);
            return fetch(`{{ route("categories.index") }}/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    code: formData.get('code'),
                    is_active: formData.get('is_active') ? true : false
                })
            }).then(response => response.json());
        }
    }).then((result) => {
        if (result.isConfirmed && result.value.success) {
            Swal.fire('Success!', result.value.message, 'success').then(() => {
                location.reload();
            });
        }
    });
}

function deleteCategory(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${name}". This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route("categories.index") }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}

$(document).ready(function() {
    $('#categoriesTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [4] }
        ]
    });
});
</script>
@endpush
