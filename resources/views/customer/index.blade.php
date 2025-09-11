@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-buildings'></i> Customer Management</h3>
                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                        <i class='bx bx-plus'></i> Add Customer
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="customersTable">
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
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>{{ $customer->name }}</td>
                                        <td><span class="badge bg-info">{{ $customer->code }}</span></td>
                                        <td>
                                            @if($customer->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $customer->created_at->format('m/d/Y g:i A') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editCustomer({{ $customer->id }}, '{{ $customer->name }}', '{{ $customer->code }}', {{ $customer->is_active ? 'true' : 'false' }})">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteCustomer({{ $customer->id }}, '{{ $customer->name }}')">
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
        title: 'Add New Customer',
        html: `
            <form id="customerForm">
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
            const form = document.getElementById('customerForm');
            const formData = new FormData(form);
            return fetch('{{ route("customers.store") }}', {
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

function editCustomer(id, name, code, isActive) {
    Swal.fire({
        title: 'Edit Customer',
        html: `
            <form id="editCustomerForm">
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
            const form = document.getElementById('editCustomerForm');
            const formData = new FormData(form);
            return fetch(`{{ route("customers.index") }}/${id}`, {
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

function deleteCustomer(id, name) {
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
            fetch(`{{ route("customers.index") }}/${id}`, {
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
    $('#customersTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [4] }
        ]
    });
});
</script>
@endpush
