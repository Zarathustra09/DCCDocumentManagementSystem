@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class='bx bx-category'></i> Category Management
                    </h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="openMainCategoryModal()">
                            <i class='bx bx-plus-circle'></i> Add Main Category
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="openSubCategoryModal()">
                            <i class='bx bx-subdirectory-right'></i> Add Subcategory
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Main Category</th>
                                    <th>Subcategories</th>
                                    <th class="text-end">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mainCategories as $mainCategory)
                                    <tr>
                                        <td>
                                            <strong>{{ $mainCategory->name }}</strong>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#subcategories-{{ $mainCategory->id }}">
                                                View ({{ $mainCategory->subcategories->count() }})
                                            </button>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="openMainCategoryModal(this.dataset.mainCategory)"
                                                    data-main-category="{{ json_encode(['id' => $mainCategory->id, 'name' => $mainCategory->name]) }}">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="deleteMainCategory({{ $mainCategory->id }}, '{{ addslashes($mainCategory->name) }}')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-primary"
                                                    onclick="openSubCategoryModal({{ $mainCategory->id }})">
                                                    <i class="bx bx-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-top-0">
                                        <td colspan="3" class="p-0">
                                            <div class="collapse" id="subcategories-{{ $mainCategory->id }}">
                                                <div class="p-3 bg-light">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Code</th>
                                                                    <th>Status</th>
                                                                    <th>Created</th>
                                                                    <th class="text-end">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($mainCategory->subcategories as $subcategory)
                                                                    <tr>
                                                                        <td>{{ $subcategory->name }}</td>
                                                                        <td><span class="badge bg-info">{{ $subcategory->code }}</span></td>
                                                                        <td>
                                                                            @if($subcategory->is_active)
                                                                                <span class="badge bg-success">Active</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">Inactive</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $subcategory->created_at?->format('m/d/Y g:i A') }}</td>
                                                                        <td class="text-end">
                                                                            <div class="btn-group btn-group-sm">
                                                                                <button type="button" class="btn btn-outline-secondary"
                                                                                    onclick="editSubCategory(this.dataset.subcategory)"
                                                                                    data-subcategory="{{ json_encode([
                                                                                        'id' => $subcategory->id,
                                                                                        'name' => $subcategory->name,
                                                                                        'code' => $subcategory->code,
                                                                                        'is_active' => (bool) $subcategory->is_active,
                                                                                        'main_category_id' => $subcategory->main_category_id,
                                                                                    ]) }}">
                                                                                    <i class="bx bx-edit"></i>
                                                                                </button>
                                                                                <button type="button" class="btn btn-outline-danger"
                                                                                    onclick="deleteSubCategory({{ $subcategory->id }}, '{{ addslashes($subcategory->name) }}')">
                                                                                    <i class="bx bx-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5" class="text-center text-muted">No subcategories yet.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No main categories found.</td>
                                    </tr>
                                @endforelse
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
const csrfToken = '{{ csrf_token() }}';
const subCategoryStoreUrl = '{{ route("categories.store") }}';
const subCategoryBaseUrl = '{{ route("categories.index") }}';
const mainCategoryStoreUrl = '{{ route("main-categories.store") }}';
const mainCategoryBaseUrl = '{{ route("main-categories.index") }}';
const mainCategoriesForSelect = @json(
    $mainCategories->map(function ($category) {
        return ['id' => $category->id, 'name' => $category->name];
    })->values()
);

function renderMainCategoryOptions(selectedId = null) {
    return mainCategoriesForSelect.map(cat => `
        <option value="${cat.id}" ${Number(selectedId) === Number(cat.id) ? 'selected' : ''}>${cat.name}</option>
    `).join('');
}

function openMainCategoryModal(mainCategory = null) {
    if (typeof mainCategory === 'string') {
        mainCategory = JSON.parse(mainCategory);
    }
    const isEdit = !!mainCategory;
    Swal.fire({
        title: isEdit ? 'Edit Main Category' : 'Add Main Category',
        html: `
            <div class="mb-3 text-start">
                <label class="form-label">Name</label>
                <input class="form-control" name="name" value="${mainCategory ? mainCategory.name : ''}" required>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: isEdit ? 'Update' : 'Create',
        preConfirm: () => {
            const name = Swal.getPopup().querySelector('input[name="name"]').value.trim();
            if (!name) {
                Swal.showValidationMessage('Name is required.');
                return false;
            }

            return fetch(isEdit ? `${mainCategoryBaseUrl}/${mainCategory.id}` : mainCategoryStoreUrl, {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ name }),
            }).then(handleJsonResponse);
        }
    }).then(handleResult);
}

function deleteMainCategory(id, name) {
    Swal.fire({
        title: 'Delete Main Category?',
        html: `
            <div class="text-start">
                <p class="text-danger mb-3"><strong>Warning:</strong> This action cannot be undone.</p>
                <p class="mb-3">You are about to delete the main category: <strong>"${escapeHtml(name)}"</strong></p>
                <p class="mb-2">Please type <strong>${escapeHtml(name)}</strong> to confirm:</p>
                <input type="text" id="deleteConfirmInput" class="form-control" placeholder="Type category name here">
                <div id="deleteError" class="text-danger mt-2" style="display: none;"></div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Delete Main Category',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const input = document.getElementById('deleteConfirmInput').value.trim();
            const errorDiv = document.getElementById('deleteError');

            if (input !== name) {
                errorDiv.textContent = 'Category name does not match. Please type the exact name.';
                errorDiv.style.display = 'block';
                return false;
            }

            return fetch(`${mainCategoryBaseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            }).then(handleJsonResponse);
        }
    }).then(handleResult);
}

function openSubCategoryModal(mainCategoryId = null) {
    upsertSubCategory(null, mainCategoryId);
}

function editSubCategory(subCategory) {
    if (typeof subCategory === 'string') {
        subCategory = JSON.parse(subCategory);
    }
    upsertSubCategory(subCategory, subCategory.main_category_id);
}

function upsertSubCategory(subCategory = null, defaultMainCategoryId = null) {
    const isEdit = !!subCategory;
    Swal.fire({
        title: isEdit ? 'Edit Subcategory' : 'Add Subcategory',
        html: `
            <form id="subcategoryForm">
                <div class="mb-3 text-start">
                    <label class="form-label">Main Category</label>
                    <select class="form-select" name="main_category_id" required>
                        ${renderMainCategoryOptions(subCategory ? subCategory.main_category_id : defaultMainCategoryId)}
                    </select>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Name</label>
                    <input class="form-control" name="name" value="${subCategory ? subCategory.name : ''}" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Code (max 3 chars)</label>
                    <input class="form-control text-uppercase" maxlength="3" name="code" value="${subCategory ? subCategory.code : ''}" required>
                </div>
                <div class="form-check text-start">
                    <input class="form-check-input" type="checkbox" name="is_active" ${subCategory ? (subCategory.is_active ? 'checked' : '') : 'checked'}>
                    <label class="form-check-label">Active</label>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: isEdit ? 'Update' : 'Create',
        preConfirm: () => {
            const form = Swal.getPopup().querySelector('#subcategoryForm');
            const formData = new FormData(form);
            const payload = {
                name: formData.get('name')?.trim(),
                code: formData.get('code')?.trim(),
                is_active: formData.get('is_active') ? true : false,
                main_category_id: formData.get('main_category_id'),
            };

            if (!payload.name || !payload.code || !payload.main_category_id) {
                Swal.showValidationMessage('All fields are required.');
                return false;
            }

            return fetch(isEdit ? `${subCategoryBaseUrl}/${subCategory.id}` : subCategoryStoreUrl, {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            }).then(handleJsonResponse);
        }
    }).then(handleResult);
}

function deleteSubCategory(id, name) {
    Swal.fire({
        title: 'Delete Subcategory?',
        html: `
            <div class="text-start">
                <p class="text-danger mb-3"><strong>Warning:</strong> This action cannot be undone.</p>
                <p class="mb-3">You are about to delete the subcategory: <strong>"${escapeHtml(name)}"</strong></p>
                <p class="mb-2">Please type <strong>${escapeHtml(name)}</strong> to confirm:</p>
                <input type="text" id="deleteConfirmInput" class="form-control" placeholder="Type subcategory name here">
                <div id="deleteError" class="text-danger mt-2" style="display: none;"></div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Delete Subcategory',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const input = document.getElementById('deleteConfirmInput').value.trim();
            const errorDiv = document.getElementById('deleteError');

            if (input !== name) {
                errorDiv.textContent = 'Subcategory name does not match. Please type the exact name.';
                errorDiv.style.display = 'block';
                return false;
            }

            return fetch(`${subCategoryBaseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            }).then(handleJsonResponse);
        }
    }).then(handleResult);
}

function escapeHtml(value) {
    if (value === null || value === undefined) {
        return '';
    }
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function handleJsonResponse(response) {
    return response.json().then(data => {
        if (!response.ok || !data.success) {
            const errorMessage = data.message || 'Request failed.';
            Swal.showValidationMessage(errorMessage);
            throw new Error(errorMessage);
        }
        return data;
    }).catch(error => {
        if (!error.message.includes('Swal')) {
            Swal.showValidationMessage(error.message || 'An error occurred.');
        }
        throw error;
    });
}

function handleResult(result) {
    if (result?.isConfirmed && result.value?.success) {
        Swal.fire('Success!', result.value.message, 'success').then(() => location.reload());
    } else if (result?.dismiss) {
        // User cancelled, do nothing
    }
}
</script>
@endpush
