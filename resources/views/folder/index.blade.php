@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
        <div class="d-flex align-items-center">
            <h4 class="mb-0 me-3">
                <i class="bx bx-folder text-warning me-2"></i>
                Document Management
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
            @can('manage base folders')
                <button class="btn btn-sm btn-primary" id="add-base-folder-btn">
                    <i class="bx bx-folder-plus"></i> Add Base Folder
                </button>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Icon View -->
    <div class="icon-view" id="icon-view">
        @foreach($baseFolders as $baseFolder)
            <div class="base-folder-section mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bx bxs-folder-open text-primary me-2"></i>
                        {{ $baseFolder->name }}
                    </h5>
                    @can('manage base folders')
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary edit-base-folder-btn"
                                    data-id="{{ $baseFolder->id }}"
                                    data-name="{{ $baseFolder->name }}"
                                    data-description="{{ $baseFolder->description }}">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger delete-base-folder-btn"
                                    data-id="{{ $baseFolder->id }}"
                                    data-name="{{ $baseFolder->name }}"
                                    data-folders-count="{{ $baseFolder->folders->count() }}">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    @endcan
                </div>

                @if($baseFolder->description)
                    <p class="text-muted small mb-3">{{ $baseFolder->description }}</p>
                @endif

                <div class="row g-3 mb-4">
                    @if($baseFolder->folders->count() > 0)
                        @foreach($baseFolder->folders as $folder)
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
                                                <button type="button" class="btn btn-light btn-sm rounded-circle delete-folder-btn"
                                                       data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" title="Delete">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="empty-state text-center p-4 rounded drop-zone" style="background-color: #f8f9fa;" data-folder-id="">
                                <i class="bx bx-folder-open text-muted" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-2 text-muted">No folders in {{ $baseFolder->name }}</p>
                                <p class="small text-muted mb-3">Drag folders here to move them or create new ones</p>
                                @can('create ' . $baseFolder->name . ' documents')
                                    <button class="btn btn-sm btn-outline-primary add-folder-btn" data-base-folder="{{ $baseFolder->id }}">
                                        <i class="bx bx-folder-plus"></i> Add Folder
                                    </button>
                                @endcan
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($orphanedFolders->count() > 0)
            <div class="orphaned-folders-section mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 d-flex align-items-center text-muted">
                        <i class="bx bx-folder-open me-2"></i>
                        Unassigned Folders
                    </h5>
                </div>

                <div class="row g-3">
                    @foreach($orphanedFolders as $folder)
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <div class="folder-item position-relative rounded shadow-sm"
                                 draggable="true"
                                 data-type="folder"
                                 data-id="{{ $folder->id }}"
                                 data-name="{{ $folder->name }}">
                                <a href="{{ route('folders.show', $folder) }}" class="text-decoration-none">
                                    <div class="p-3 d-flex flex-column align-items-center" style="background-color: rgba(108, 117, 125, 0.05); border: 1px solid rgba(108, 117, 125, 0.2); border-radius: 0.375rem;">
                                        <i class="bx bxs-folder text-secondary" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0 text-truncate w-100 text-center small">{{ $folder->name }}</p>
                                    </div>
                                </a>
                                <div class="item-actions position-absolute top-0 end-0 p-1 d-none">
                                    <div class="btn-group btn-group-sm">
                                        @can('edit folders')
                                            <a href="{{ route('folders.edit', $folder) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete folders')
                                            <button type="button" class="btn btn-light btn-sm rounded-circle delete-folder-btn"
                                                   data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($baseFolders->isEmpty() && $orphanedFolders->isEmpty())
            <div class="text-center py-5">
                <i class="bx bx-folder-open text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-3 text-muted">No folders found</h5>
                <p class="text-muted">Create your first base folder to get started</p>
                @can('manage base folders')
                    <button class="btn btn-primary" id="add-base-folder-btn">
                        <i class="bx bx-folder-plus"></i> Create Base Folder
                    </button>
                @endcan
            </div>
        @endif
    </div>

    <!-- List View -->
    <div class="list-view d-none" id="list-view">
        @foreach($baseFolders as $baseFolder)
            <div class="base-folder-section mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bx bxs-folder-open text-primary me-2"></i>
                        {{ $baseFolder->name }}
                    </h5>
                    @can('manage base folders')
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary edit-base-folder-btn"
                                    data-id="{{ $baseFolder->id }}"
                                    data-name="{{ $baseFolder->name }}"
                                    data-description="{{ $baseFolder->description }}">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger delete-base-folder-btn"
                                    data-id="{{ $baseFolder->id }}"
                                    data-name="{{ $baseFolder->name }}"
                                    data-folders-count="{{ $baseFolder->folders->count() }}">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    @endcan
                </div>

                @if($baseFolder->description)
                    <p class="text-muted small mb-3">{{ $baseFolder->description }}</p>
                @endif

                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Items</th>
                                <th>Modified</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($baseFolder->folders->count() > 0)
                                @foreach($baseFolder->folders as $folder)
                                    <tr class="drop-zone"
                                        data-folder-id="{{ $folder->id }}"
                                        draggable="true"
                                        data-type="folder"
                                        data-id="{{ $folder->id }}"
                                        data-name="{{ $folder->name }}">
                                        <td style="width: 40%">
                                            <a href="{{ route('folders.show', $folder) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                                <i class="bx bxs-folder text-warning me-2" style="font-size: 1.25rem;"></i>
                                                <span>{{ $folder->name }}</span>
                                            </a>
                                        </td>
                                        <td><span class="badge bg-light text-dark">Folder</span></td>
                                        <td><small>{{ $folder->children->count() + $folder->documents->count() }} items</small></td>
                                        <td><small>{{ $folder->updated_at->format('M d, Y') }}</small></td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @can('edit ' . $folder->baseFolder->name . ' documents')
                                                    <a href="{{ route('folders.edit', $folder) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete ' . $folder->baseFolder->name . ' documents')
                                                    <button type="button" class="btn btn-outline-danger btn-sm delete-folder-btn"
                                                           data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" title="Delete">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-4 drop-zone" data-folder-id="">
                                        <i class="bx bx-folder-open text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0 text-muted">No folders in {{ $baseFolder->name }}</p>
                                        <p class="small text-muted">Drag folders here to move them</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if($orphanedFolders->count() > 0)
            <div class="orphaned-folders-section mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 d-flex align-items-center text-muted">
                        <i class="bx bx-folder-open me-2"></i>
                        Unassigned Folders
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Items</th>
                                <th>Modified</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orphanedFolders as $folder)
                                <tr draggable="true"
                                    data-type="folder"
                                    data-id="{{ $folder->id }}"
                                    data-name="{{ $folder->name }}">
                                    <td style="width: 40%">
                                        <a href="{{ route('folders.show', $folder) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <i class="bx bxs-folder text-secondary me-2" style="font-size: 1.25rem;"></i>
                                            <span>{{ $folder->name }}</span>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark">Folder</span></td>
                                    <td><small>{{ $folder->children->count() + $folder->documents->count() }} items</small></td>
                                    <td><small>{{ $folder->updated_at->format('M d, Y') }}</small></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('edit folders')
                                                <a href="{{ route('folders.edit', $folder) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete folders')
                                                <button type="button" class="btn btn-outline-danger btn-sm delete-folder-btn"
                                                       data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" title="Delete">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Global Drop Overlay -->
<div id="global-drop-overlay" class="global-drop-overlay d-none">
    <div class="drop-message">
        <i class="bx bx-move" style="font-size: 4rem;"></i>
        <h3 id="drop-message-text">Move folder here</h3>
        <p class="mb-0">Drop to move to this location</p>
    </div>
</div>

<!-- Delete Folder Modal -->
<div class="modal fade" id="deleteFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Folder</h5>
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTables for list view tables
        $('.table').DataTable({
            pageLength: 25,
            responsive: true,
            ordering: true,
            searching: false,
            paging: false,
            info: false
        });

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

        // Drag and drop functionality
        let draggedItem = null;
        let isDraggingItem = false;

        // Set up draggable items
        document.querySelectorAll('[draggable="true"]').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                isDraggingItem = true;
                draggedItem = {
                    id: this.dataset.id,
                    type: this.dataset.type,
                    name: this.dataset.name
                };

                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('application/json', JSON.stringify(draggedItem));

                // Add overlay to drop zones after a short delay
                setTimeout(() => {
                    document.querySelectorAll('.drop-zone').forEach(zone => {
                        const overlay = document.createElement('div');
                        overlay.className = 'drag-move-overlay d-none';
                        overlay.innerHTML = `
                            <div class="move-message">
                                <i class="bx bx-move"></i>
                                Drop to move here
                            </div>
                        `;
                        zone.style.position = 'relative';
                        zone.appendChild(overlay);
                    });
                }, 50);
            });

            item.addEventListener('dragend', function() {
                isDraggingItem = false;
                this.classList.remove('dragging');
                draggedItem = null;

                // Remove all overlays
                document.querySelectorAll('.drag-move-overlay').forEach(overlay => {
                    overlay.remove();
                });

                // Remove drag-over class from all drop zones
                document.querySelectorAll('.drop-zone').forEach(zone => {
                    zone.classList.remove('drag-over');
                });
            });
        });

        // Set up drop zones
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();

                if (isDraggingItem) {
                    e.dataTransfer.dropEffect = 'move';
                    this.classList.add('drag-over');

                    const overlay = this.querySelector('.drag-move-overlay');
                    if (overlay) {
                        overlay.classList.remove('d-none');
                    }
                }
            });

            zone.addEventListener('dragleave', function(e) {
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');

                    const overlay = this.querySelector('.drag-move-overlay');
                    if (overlay) {
                        overlay.classList.add('d-none');
                    }
                }
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                const overlay = this.querySelector('.drag-move-overlay');
                if (overlay) {
                    overlay.remove();
                }

                if (isDraggingItem) {
                    const targetFolderId = this.dataset.folderId || null;

                    // Prevent dropping folder into itself
                    if (draggedItem.type === 'folder' && targetFolderId === draggedItem.id) {
                        Swal.fire('Error', 'Cannot move folder into itself', 'error');
                        return;
                    }

                    moveFolder(draggedItem.id, targetFolderId, draggedItem.name);
                }
            });
        });

        function moveFolder(folderId, targetFolderId, folderName) {
            const targetName = targetFolderId ? 'selected folder' : 'root level';

            Swal.fire({
                title: 'Move Folder',
                text: `Move "${folderName}" to ${targetName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Move',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/folders/${folderId}/move`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            parent_id: targetFolderId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Moved!', data.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Move error:', error);
                            Swal.fire('Error', 'Failed to move folder.', 'error');
                        });
                }
            });
        }

        // Delete folder functionality
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteFolderModal'));
        document.querySelectorAll('.delete-folder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('folder-name').textContent = this.dataset.name;
                document.getElementById('delete-folder-form').action = `/folders/${this.dataset.id}`;
                deleteModal.show();
            });
        });

        // Add folder functionality
        document.querySelectorAll('.add-folder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const baseFolderId = this.dataset.baseFolder;
                showCreateFolderModal(null, baseFolderId);
            });
        });

        function showCreateFolderModal(parentId = null, baseFolderId = null) {
            Swal.fire({
                title: 'Create New Folder',
                html: `
                    <div class="mb-3 text-start">
                        <label for="swal-folder-name" class="form-label fw-bold">Folder Name</label>
                        <input id="swal-folder-name" class="form-control" placeholder="Enter folder name" required>
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
                    if (!name.trim()) {
                        Swal.showValidationMessage('Folder name is required');
                        return false;
                    }
                    return {
                        name: name.trim(),
                        description: document.getElementById('swal-folder-desc').value.trim(),
                        parent_id: parentId,
                        base_folder_id: baseFolderId
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

        // Base Folder Management
        document.querySelectorAll('#add-base-folder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Create Base Folder',
                    html: `
                        <div class="mb-3 text-start">
                            <label for="swal-base-name" class="form-label fw-bold">Name</label>
                            <input id="swal-base-name" class="form-control" placeholder="Base folder name" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="swal-base-desc" class="form-label fw-bold">Description</label>
                            <textarea id="swal-base-desc" class="form-control" placeholder="Description" rows="3"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Create',
                    preConfirm: () => {
                        const name = document.getElementById('swal-base-name').value;
                        if (!name.trim()) {
                            Swal.showValidationMessage('Name is required');
                            return false;
                        }
                        return {
                            name: name.trim(),
                            description: document.getElementById('swal-base-desc').value.trim()
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        createBaseFolder(result.value.name, result.value.description);
                    }
                });
            });
        });

        document.querySelectorAll('.edit-base-folder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const description = this.dataset.description;

                Swal.fire({
                    title: 'Edit Base Folder',
                    html: `
                        <div class="mb-3 text-start">
                            <label for="swal-base-name" class="form-label fw-bold">Name</label>
                            <input id="swal-base-name" class="form-control" placeholder="Base folder name" value="${name}" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="swal-base-desc" class="form-label fw-bold">Description</label>
                            <textarea id="swal-base-desc" class="form-control" placeholder="Description" rows="3">${description || ''}</textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    preConfirm: () => {
                        const newName = document.getElementById('swal-base-name').value;
                        if (!newName.trim()) {
                            Swal.showValidationMessage('Name is required');
                            return false;
                        }
                        return {
                            name: newName.trim(),
                            description: document.getElementById('swal-base-desc').value.trim()
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateBaseFolder(id, result.value.name, result.value.description);
                    }
                });
            });
        });

        document.querySelectorAll('.delete-base-folder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const baseFolderId = this.dataset.id;
                const baseFolderName = this.dataset.name;
                const foldersCount = parseInt(this.dataset.foldersCount);

                let warningMessage = '';
                if (foldersCount > 0) {
                    warningMessage = `
                        <div class="alert alert-warning mt-3">
                            <i class="bx bx-error-circle me-2"></i>
                            This base folder contains ${foldersCount} folder${foldersCount !== 1 ? 's' : ''}. You must move or delete them first.
                        </div>`;
                }

                Swal.fire({
                    title: 'Delete Base Folder',
                    html: `
                        <div class="text-center mb-3">
                            <i class="bx bx-error-circle text-danger display-4 mb-3"></i>
                            <p>Are you sure you want to delete the base folder <strong>"${baseFolderName}"</strong>?</p>
                            ${warningMessage}
                            <div class="alert alert-danger mt-3">
                                <small><strong>This action cannot be undone.</strong></small>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: foldersCount > 0 ? 'Cannot Delete' : 'Delete Forever',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: foldersCount > 0 ? '#6c757d' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    allowOutsideClick: false,
                    didOpen: () => {
                        if (foldersCount > 0) {
                            Swal.getConfirmButton().disabled = true;
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && foldersCount === 0) {
                        deleteBaseFolder(baseFolderId);
                    }
                });
            });
        });

        // Base Folder CRUD Functions
        function createBaseFolder(name, description) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('name', name);
            formData.append('description', description);

            fetch('{{ route("base-folder.store") }}', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    return response.json().then(data => Promise.reject(data));
                })
                .then(data => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Base folder created successfully',
                        icon: 'success',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to create base folder.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        function updateBaseFolder(id, name, description) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            formData.append('name', name);
            formData.append('description', description);

            fetch(`/base-folder/${id}`, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    return response.json().then(data => Promise.reject(data));
                })
                .then(data => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Base folder updated successfully',
                        icon: 'success',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to update base folder.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        function deleteBaseFolder(id) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');

            fetch(`/base-folder/${id}`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Base folder deleted successfully',
                            icon: 'success',
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to delete base folder.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }
    });
</script>

<style>
    .drag-over {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border: 2px dashed #0d6efd !important;
        transition: all 0.2s ease;
    }

    .dragging {
        opacity: 0.5;
        transform: scale(0.95);
    }

    .drag-move-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(13, 110, 253, 0.9);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        z-index: 10;
    }

    .move-message {
        text-align: center;
        font-weight: 500;
    }

    .move-message i {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
        display: block;
    }

    .global-drop-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .drop-message {
        text-align: center;
        padding: 2rem;
        border: 3px dashed rgba(255, 255, 255, 0.5);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
    }

    [draggable="true"] {
        cursor: move;
    }

    [draggable="true"]:hover {
        opacity: 0.9;
    }
</style>
@endpush
@endsection
