@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
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
                            {{-- Yajra rendered table --}}
                            {!! $dataTable->table(['class' => 'table table-striped table-hover'], true) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{!! $dataTable->scripts() !!}

<script>
function getCustomersTableInstance() {
    if (window.LaravelDataTables && window.LaravelDataTables['customersTable']) {
        return window.LaravelDataTables['customersTable'];
    }
    if ($.fn.DataTable.isDataTable('#customersTable')) {
        return $('#customersTable').DataTable();
    }
    return null;
}

function reloadCustomersTable() {
    const dt = getCustomersTableInstance();
    if (dt && dt.ajax && typeof dt.ajax.reload === 'function') {
        dt.ajax.reload(null, false);
    } else if (dt && typeof dt.draw === 'function') {
        dt.draw();
    } else {
        // fallback full reload
        location.reload();
    }
}

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
                    is_active: !!formData.get('is_active')
                })
            }).then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message ||
                        Object.values(data.errors || {})[0]?.[0] ||
                        'Failed to create customer.'
                    );
                }
                return data;
            }).catch(error => {
                Swal.showValidationMessage(error.message);
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value && result.value.success) {
            Swal.fire('Success!', result.value.message, 'success').then(() => {
                reloadCustomersTable();
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
                    is_active: !!formData.get('is_active')
                })
            }).then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message ||
                        Object.values(data.errors || {})[0]?.[0] ||
                        'Failed to update customer.'
                    );
                }
                return data;
            }).catch(error => {
                Swal.showValidationMessage(error.message);
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value && result.value.success) {
            Swal.fire('Success!', result.value.message, 'success').then(() => {
                reloadCustomersTable();
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
            }).then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to delete customer.');
                }
                Swal.fire('Deleted!', data.message, 'success').then(() => {
                    reloadCustomersTable();
                });
            }).catch(error => {
                Swal.fire('Error', error.message, 'error');
            });
        }
    });
}
</script>
@endpush
