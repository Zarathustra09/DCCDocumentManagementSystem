@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar / Folder Tree -->
        <div class="col-md-3 col-lg-2 d-none d-md-block sidebar" style="min-height: calc(100vh - 60px); background-color: #f8f9fa; border-right: 1px solid #e9ecef;">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('folders.index') ? 'active text-primary' : 'text-dark' }} drop-zone"
                           href="{{ route('folders.index') }}" data-folder-id="">
                            <i class="bx bxs-home me-2"></i> All Documents
                        </a>
                    </li>
                </ul>

                <div class="px-3 mb-2 d-flex justify-content-between align-items-center">
                    <span class="small text-muted text-uppercase" style="letter-spacing: 0.5px;">Categories</span>
                    @can('create folders')
                    <button class="btn btn-link btn-sm p-0" onclick="showCreateCategorySwal()" title="Add Category">
                        <i class="bx bx-plus text-primary"></i>
                    </button>
                    @endcan
                </div>

                <ul class="nav flex-column">
                    @foreach($baseFolders as $baseFolder)
                    <li class="nav-item position-relative">
                        <div class="d-flex align-items-center justify-content-between category-item">
                            <a class="nav-link d-flex align-items-center text-dark drop-zone flex-grow-1"
                               href="{{ route('folders.index', ['base_folder' => $baseFolder->id]) }}"
                               data-folder-id="">
                                <i class="bx bx-folder me-2 text-warning"></i>
                                <span class="flex-grow-1">{{ $baseFolder->name }}</span>
                            </a>
                            @can('edit folders')
                            <div class="category-actions d-none">
                                <button class="btn btn-link btn-sm p-0 me-1"
                                        onclick="showEditCategorySwal({{ $baseFolder->id }}, '{{ addslashes($baseFolder->name) }}', '{{ addslashes($baseFolder->description ?? '') }}')"
                                        title="Edit">
                                    <i class="bx bx-edit text-secondary"></i>
                                </button>
                                <button class="btn btn-link btn-sm p-0"
                                        onclick="deleteCategory({{ $baseFolder->id }}, '{{ addslashes($baseFolder->name) }}')"
                                        title="Delete">
                                    <i class="bx bx-trash text-danger"></i>
                                </button>
                            </div>
                            @endcan
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4" id="main-content">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0">
                        @if($selectedBaseFolder)
                            {{ $selectedBaseFolder->name }}
                        @else
                            All Documents
                        @endif
                    </h4>
                </div>
                <div class="d-flex align-items-center">
                    <div class="btn-group btn-group-sm me-2">
                        <button type="button" class="btn btn-outline-secondary active" data-view="icons" id="icon-view-btn">
                            <i class="bx bx-grid-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-view="list" id="list-view-btn">
                            <i class="bx bx-list-ul"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="my-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bx bxs-folder-open text-primary me-2" style="font-size: 1.5rem;"></i>
                    <span class="text-muted">
                        @if($selectedBaseFolder)
                            {{ $selectedBaseFolder->description ?? 'No description' }}
                        @else
                            Browse all documents and folders
                        @endif
                    </span>
                </div>

                <div class="action-buttons">
                    @can('create folders')
                        <button class="btn btn-sm btn-outline-primary" onclick="showCreateFolderSwal()">
                            <i class="bx bx-folder-plus"></i> Add Folder
                        </button>
                    @endcan
{{--                    @if($selectedBaseFolder)--}}
{{--                        @can('create ' . $selectedBaseFolder->name . ' documents')--}}
{{--                            <button id="swal-upload-btn" class="btn btn-sm btn-outline-success">--}}
{{--                                <i class="bx bx-upload"></i> Upload--}}
{{--                            </button>--}}
{{--                        @endcan--}}
{{--                    @endif--}}
                </div>
            </div>

            <!-- Icon View -->
            <div class="icon-view" id="icon-view">
                <div class="row g-3">
                    @foreach($folders as $folder)
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <div class="folder-item position-relative rounded shadow-sm drop-zone"
                                 data-folder-id="{{ $folder->id }}"
                                 draggable="true"
                                 data-type="folder"
                                 data-id="{{ $folder->id }}"
                                 data-name="{{ $folder->name }}">
                                <a href="{{ route('folders.show', $folder) }}" class="text-decoration-none">
                                    <div class="p-3 d-flex flex-column align-items-center" style="background-color: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.2); border-radius: 0.375rem;">
                                        <i class="bx bxs-folder text-warning" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0 text-truncate w-100 text-center small">{{ $folder->name }}</p>
                                    </div>
                                </a>
                                <div class="item-actions position-absolute top-0 end-0 p-1 d-none">
                                    <div class="btn-group btn-group-sm">
                                        @can('edit ' . $folder->baseFolder->name . ' documents')
                                        <a href="{{ route('folders.edit', $folder) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete ' . $folder->baseFolder->name . ' documents')
                                        <button type="button" class="btn btn-light btn-sm rounded-circle delete-btn"
                                               data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($folders->isEmpty())
                        <div class="col-12 text-center py-5">
                            <div class="empty-state p-4 rounded drop-zone" style="background-color: #f8f9fa;" data-folder-id="">
                                <i class="bx bx-folder-open text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-3 text-muted">No folders found</p>
                                <p class="small text-muted mb-3">Create your first folder to organize documents</p>
                                <div>
                                    @can('create folders')
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="showCreateFolderSwal()">
                                        <i class="bx bx-folder-plus"></i> Add Folder
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- List View -->
            <div class="list-view d-none" id="list-view">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Modified</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($folders as $folder)
                                <tr class="drop-zone"
                                    data-folder-id="{{ $folder->id }}"
                                    draggable="true"
                                    data-type="folder"
                                    data-id="{{ $folder->id }}"
                                    data-name="{{ $folder->name }}">
                                    <td>
                                        <a href="{{ route('folders.show', $folder) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <i class="bx bxs-folder text-warning me-2" style="font-size: 1.25rem;"></i>
                                            <span>{{ $folder->name }}</span>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ $folder->baseFolder->name }}</span></td>
                                    <td><small>{{ $folder->updated_at->format('M d, Y') }}</small></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('edit ' . $folder->baseFolder->name . ' documents')
                                            <a href="{{ route('folders.edit', $folder) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete ' . $folder->baseFolder->name . ' documents')
                                            <button type="button" class="btn btn-outline-danger btn-sm delete-btn"
                                                   data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if($folders->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="bx bx-folder-open text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0 text-muted">No folders found</p>
                                        <p class="small text-muted">Create your first folder to get started</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const iconViewBtn = document.getElementById('icon-view-btn');
    const listViewBtn = document.getElementById('list-view-btn');
    const iconView = document.getElementById('icon-view');
    const listView = document.getElementById('list-view');

    iconViewBtn.addEventListener('click', function() {
        iconView.classList.remove('d-none');
        listView.classList.add('d-none');
        iconViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
        localStorage.setItem('folder-view', 'icons');
    });

    listViewBtn.addEventListener('click', function() {
        listView.classList.remove('d-none');
        iconView.classList.add('d-none');
        listViewBtn.classList.add('active');
        iconViewBtn.classList.remove('active');
        localStorage.setItem('folder-view', 'list');
    });

    // Load saved preference
    if (localStorage.getItem('folder-view') === 'list') {
        listViewBtn.click();
    }

    // Hover effects for items
    document.querySelectorAll('.folder-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.querySelector('.item-actions').classList.remove('d-none');
        });

        item.addEventListener('mouseleave', function() {
            this.querySelector('.item-actions').classList.add('d-none');
        });
    });

    // Category hover effects
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            const actions = this.querySelector('.category-actions');
            if (actions) actions.classList.remove('d-none');
        });

        item.addEventListener('mouseleave', function() {
            const actions = this.querySelector('.category-actions');
            if (actions) actions.classList.add('d-none');
        });
    });

    // Delete folder modal functionality
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteFolderModal'));
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('folder-name').textContent = this.dataset.name;
            document.getElementById('delete-folder-form').action = `/folders/${this.dataset.id}`;
            deleteModal.show();
        });
    });
});

// Category management functions
function showCreateCategorySwal() {
    Swal.fire({
        title: 'Create New Category',
        html: `
            <div class="mb-3 text-start">
                <label for="swal-category-name" class="form-label fw-bold">Category Name</label>
                <input id="swal-category-name" class="form-control" placeholder="Enter category name" required>
            </div>
            <div class="mb-3 text-start">
                <label for="swal-category-desc" class="form-label fw-bold">Description (Optional)</label>
                <textarea id="swal-category-desc" class="form-control" placeholder="Enter description" rows="3"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create Category',
        preConfirm: () => {
            const name = document.getElementById('swal-category-name').value;
            if (!name.trim()) {
                Swal.showValidationMessage('Category name is required');
                return false;
            }
            return {
                name: name.trim(),
                description: document.getElementById('swal-category-desc').value.trim()
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("base-folder.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Created!', 'Category created successfully.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to create category.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to create category.', 'error');
            });
        }
    });
}

function showEditCategorySwal(id, name, description) {
    Swal.fire({
        title: 'Edit Category',
        html: `
            <div class="mb-3 text-start">
                <label for="swal-category-name" class="form-label fw-bold">Category Name</label>
                <input id="swal-category-name" class="form-control" placeholder="Enter category name" value="${name}" required>
            </div>
            <div class="mb-3 text-start">
                <label for="swal-category-desc" class="form-label fw-bold">Description (Optional)</label>
                <textarea id="swal-category-desc" class="form-control" placeholder="Enter description" rows="3">${description}</textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Category',
        preConfirm: () => {
            const name = document.getElementById('swal-category-name').value;
            if (!name.trim()) {
                Swal.showValidationMessage('Category name is required');
                return false;
            }
            return {
                name: name.trim(),
                description: document.getElementById('swal-category-desc').value.trim()
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/base-folder/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Updated!', 'Category updated successfully.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to update category.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to update category.', 'error');
            });
        }
    });
}

function deleteCategory(id, name) {
    Swal.fire({
        title: 'Delete Category',
        text: `Are you sure you want to delete "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/base-folder/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'Category deleted successfully.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to delete category.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to delete category.', 'error');
            });
        }
    });
}

function showCreateFolderSwal() {
    const baseFolders = @json($baseFolders);
    const selectedBaseFolderId = {{ $selectedBaseFolder->id ?? 'null' }};

    let baseFolderOptions = '';
    baseFolders.forEach(folder => {
        const selected = selectedBaseFolderId === folder.id ? 'selected' : '';
        baseFolderOptions += `<option value="${folder.id}" ${selected}>${folder.name}</option>`;
    });

    Swal.fire({
        title: 'Create New Folder',
        html: `
            <div class="mb-3 text-start">
                <label for="swal-folder-name" class="form-label fw-bold">Folder Name</label>
                <input id="swal-folder-name" class="form-control" placeholder="Enter folder name" required>
            </div>
            <div class="mb-3 text-start">
                <label for="swal-base-folder" class="form-label fw-bold">Category</label>
                <select id="swal-base-folder" class="form-select" required>
                    <option value="">Select a category</option>
                    ${baseFolderOptions}
                </select>
            </div>
            <div class="mb-3 text-start">
                <label for="swal-folder-desc" class="form-label fw-bold">Description (Optional)</label>
                <textarea id="swal-folder-desc" class="form-control" placeholder="Enter description" rows="3"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create Folder',
        preConfirm: () => {
            const name = document.getElementById('swal-folder-name').value;
            const baseFolderId = document.getElementById('swal-base-folder').value;

            if (!name.trim()) {
                Swal.showValidationMessage('Folder name is required');
                return false;
            }
            if (!baseFolderId) {
                Swal.showValidationMessage('Please select a category');
                return false;
            }

            return {
                name: name.trim(),
                base_folder_id: baseFolderId,
                description: document.getElementById('swal-folder-desc').value.trim(),
                parent_id: null
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("folders.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Created!', 'Folder created successfully.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to create folder.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to create folder.', 'error');
            });
        }
    });
}
</script>

<!-- Delete Folder Modal -->
<div class="modal fade" id="deleteFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Delete folder "<span id="folder-name" class="fw-medium"></span>"?</p>
                <p class="mb-0 small text-danger">This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-folder-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

<style>
.category-item:hover {
    background-color: rgba(0,0,0,0.05);
    border-radius: 0.375rem;
}

.sidebar .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 0.375rem;
}

.sidebar .nav-link.active {
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 0.375rem;
}
</style>
@endsection
