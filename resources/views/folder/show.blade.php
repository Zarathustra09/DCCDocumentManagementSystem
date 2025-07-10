@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar / Folder Tree -->
        <div class="col-md-3 col-lg-2 d-none d-md-block sidebar" style="min-height: calc(100vh - 60px); background-color: #f8f9fa; border-right: 1px solid #e9ecef;">
            <div class="position-sticky pt-3">
                <div class="px-3 mb-4 d-flex align-items-center">
                    <i class="bx bxs-folder-open text-primary" style="font-size: 1.25rem;"></i>
                    <span class="ms-2 fw-medium">Explorer</span>
                </div>

                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ !$folder->parent_id ? 'active text-primary' : 'text-dark' }} drop-zone"
                           href="{{ route('folders.index') }}" data-folder-id="">
                            <i class="bx bxs-home me-2"></i> Home
                        </a>
                    </li>
                    @if($folder->parent)
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center text-dark drop-zone"
                           href="{{ route('folders.show', $folder->parent) }}" data-folder-id="{{ $folder->parent->id }}">
                            <i class="bx bx-arrow-back me-2"></i> Back to {{ $folder->parent->name }}
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="px-3 mb-2 small text-muted text-uppercase" style="letter-spacing: 0.5px;">Stats</div>
                <div class="px-3 mb-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small">Files</span>
                                <span class="badge bg-secondary rounded-pill">{{ $documents->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">Folders</span>
                                <span class="badge bg-secondary rounded-pill">{{ $subfolders->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4" id="main-content">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb" class="d-none d-md-block me-3">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('folders.index') }}" class="text-decoration-none drop-zone" data-folder-id="">
                                    <i class="bx bxs-home"></i>
                                </a>
                            </li>
                            @if($folder->parent)
                                <?php $parents = collect([]); $parent = $folder->parent; ?>
                                @while($parent)
                                    <?php $parents->prepend($parent); $parent = $parent->parent; ?>
                                @endwhile

                                @foreach($parents as $parent)
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('folders.show', $parent) }}" class="text-decoration-none drop-zone" data-folder-id="{{ $parent->id }}">
                                            {{ $parent->name }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                            <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
                        </ol>
                    </nav>
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
                    <i class="bx bxs-folder text-warning me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0">{{ $folder->name }}</h5>
                </div>

                <div class="action-buttons">
                    @can('create ' . $folder->department . ' documents')
                    <a href="{{ route('folders.create') }}?parent_id={{ $folder->id }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-folder-plus"></i> Add
                    </a>
                    @endcan
                    @can('create ' . $folder->department . ' documents')
                    <a href="{{ route('documents.create') }}?folder_id={{ $folder->id }}" class="btn btn-sm btn-outline-success">
                        <i class="bx bx-upload"></i> Upload
                    </a>
                    @endcan
                    @can('edit ' . $folder->department . ' documents')
                    <a href="{{ route('folders.edit', $folder) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                    @endcan
                </div>
            </div>

            @if($folder->description)
                <p class="text-muted small mb-3">{{ $folder->description }}</p>
            @endif

            <!-- Icon View -->
            <div class="icon-view" id="icon-view">
                <div class="row g-3">
                    @foreach($subfolders as $subfolder)
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <div class="folder-item position-relative rounded shadow-sm drop-zone"
                                 data-folder-id="{{ $subfolder->id }}"
                                 draggable="true"
                                 data-type="folder"
                                 data-id="{{ $subfolder->id }}"
                                 data-name="{{ $subfolder->name }}">
                                <a href="{{ route('folders.show', $subfolder) }}" class="text-decoration-none">
                                    <div class="p-3 d-flex flex-column align-items-center" style="background-color: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.2); border-radius: 0.375rem;">
                                        <i class="bx bxs-folder text-warning" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0 text-truncate w-100 text-center small">{{ $subfolder->name }}</p>
                                    </div>
                                </a>
                                <div class="item-actions position-absolute top-0 end-0 p-1 d-none">
                                    <div class="btn-group btn-group-sm">
                                        @can('edit ' . $subfolder->department . ' documents')
                                        <a href="{{ route('folders.edit', $subfolder) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete ' . $subfolder->department . ' documents')
                                        <button type="button" class="btn btn-light btn-sm rounded-circle delete-btn"
                                               data-id="{{ $subfolder->id }}" data-name="{{ $subfolder->name }}" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @foreach($documents as $document)
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <div class="file-item position-relative rounded shadow-sm"
                                 draggable="true"
                                 data-type="document"
                                 data-id="{{ $document->id }}"
                                 data-name="{{ $document->original_filename }}">
                                <a href="{{ route('documents.show', $document) }}" class="text-decoration-none">
                                    <div class="p-3 d-flex flex-column align-items-center" style="background-color: #fff; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                                        <i class="bx
                                            @if(in_array($document->file_type, ['pdf'])) bxs-file-pdf text-danger
                                            @elseif(in_array($document->file_type, ['doc', 'docx'])) bxs-file-doc text-primary
                                            @elseif(in_array($document->file_type, ['xls', 'xlsx'])) bxs-file-spreadsheet text-success
                                            @elseif(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif'])) bxs-file-image text-info
                                            @else bxs-file text-secondary
                                            @endif
                                        " style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0 text-truncate w-100 text-center small">{{ $document->original_filename }}</p>
                                    </div>
                                </a>
                                <div class="item-actions position-absolute top-0 end-0 p-1 d-none">
                                    <div class="btn-group btn-group-sm">
                                        @can('download ' . $document->folder->department . ' documents')
                                        <a href="{{ route('documents.download', $document) }}" class="btn btn-light btn-sm rounded-circle" title="Download">
                                            <i class="bx bx-download"></i>
                                        </a>
                                        @endcan
                                        @can('edit ' . $document->folder->department . ' documents')
                                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete ' . $document->folder->department . ' documents')
                                        <button type="button" class="btn btn-light btn-sm rounded-circle delete-doc-btn"
                                               data-id="{{ $document->id }}" data-name="{{ $document->original_filename }}" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($subfolders->isEmpty() && $documents->isEmpty())
                        <div class="col-12 text-center py-5">
                            <div class="empty-state p-4 rounded drop-zone" style="background-color: #f8f9fa;" data-folder-id="{{ $folder->id }}">
                                <i class="bx bx-folder-open text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-3 text-muted">This folder is empty</p>
                                <p class="small text-muted mb-3">Drag files here to upload them or move items here</p>
                                <div>
                                    @can('create ' . $folder->department . ' documents')
                                    <a href="{{ route('folders.create') }}?parent_id={{ $folder->id }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bx bx-folder-plus"></i> Add
                                    </a>
                                    @endcan
                                    @can('create ' . $folder->department . ' documents')
                                    <a href="{{ route('documents.create') }}?folder_id={{ $folder->id }}" class="btn btn-sm btn-outline-success">
                                        <i class="bx bx-upload"></i> Upload
                                    </a>
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
                    <table class="table table-hover align-middle" id="folder-table">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Modified</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subfolders as $subfolder)
                                <tr class="drop-zone"
                                    data-folder-id="{{ $subfolder->id }}"
                                    draggable="true"
                                    data-type="folder"
                                    data-id="{{ $subfolder->id }}"
                                    data-name="{{ $subfolder->name }}">
                                    <td style="width: 40%">
                                        <a href="{{ route('folders.show', $subfolder) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <i class="bx bxs-folder text-warning me-2" style="font-size: 1.25rem;"></i>
                                            <span>{{ $subfolder->name }}</span>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark">Folder</span></td>
                                    <td>-</td>
                                    <td><small>{{ $subfolder->updated_at->format('M d, Y') }}</small></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('edit ' . $subfolder->department . ' documents')
                                            <a href="{{ route('folders.edit', $subfolder) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete ' . $subfolder->department . ' documents')
                                            <button type="button" class="btn btn-outline-danger btn-sm delete-btn"
                                                   data-id="{{ $subfolder->id }}" data-name="{{ $subfolder->name }}" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($documents as $document)
                                <tr draggable="true"
                                    data-type="document"
                                    data-id="{{ $document->id }}"
                                    data-name="{{ $document->original_filename }}">
                                    <td style="width: 40%">
                                        <a href="{{ route('documents.show', $document) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <i class="bx
                                                @if(in_array($document->file_type, ['pdf'])) bxs-file-pdf text-danger
                                                @elseif(in_array($document->file_type, ['doc', 'docx'])) bxs-file-doc text-primary
                                                @elseif(in_array($document->file_type, ['xls', 'xlsx'])) bxs-file-spreadsheet text-success
                                                @elseif(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif'])) bxs-file-image text-info
                                                @else bxs-file text-secondary
                                                @endif
                                            me-2" style="font-size: 1.25rem;"></i>
                                            <span>{{ $document->original_filename }}</span>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ strtoupper($document->file_type) }}</span></td>
                                    <td><small>{{ number_format($document->file_size / 1024, 0) }} KB</small></td>
                                    <td><small>{{ $document->updated_at->format('M d, Y') }}</small></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('download ' . $document->folder->department . ' documents')
                                            <a href="{{ route('documents.download', $document) }}" class="btn btn-outline-success btn-sm" title="Download">
                                                <i class="bx bx-download"></i>
                                            </a>
                                            @endcan
                                            @can('edit ' . $document->folder->department . ' documents')
                                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete ' . $document->folder->department . ' documents')
                                            <button type="button" class="btn btn-outline-danger btn-sm delete-doc-btn"
                                                   data-id="{{ $document->id }}" data-name="{{ $document->original_filename }}" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if($subfolders->isEmpty() && $documents->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center py-4 drop-zone" data-folder-id="{{ $folder->id }}">
                                        <i class="bx bx-folder-open text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0 text-muted">This folder is empty</p>
                                        <p class="small text-muted">Drag files here to upload them or move items here</p>
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

<!-- Global Drop Overlay -->
<div id="global-drop-overlay" class="global-drop-overlay d-none">
    <div class="drop-message">
        <i class="bx bx-cloud-upload" style="font-size: 4rem;"></i>
        <h3 id="drop-message-text">Drop files to upload to "{{ $folder->name }}"</h3>
        <p class="mb-0">Multiple files supported</p>
    </div>
</div>

<!-- Upload Progress Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uploading Files</h5>
            </div>
            <div class="modal-body">
                <div id="upload-progress-container"></div>
            </div>
            <div class="modal-footer d-none" id="upload-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
                    <button type="submit" class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Document Modal -->
<div class="modal fade" id="deleteDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Delete file "<span id="doc-name" class="fw-medium"></span>"?</p>
                <p class="mb-0 small text-danger">This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-doc-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Drag and drop styles */
    .dragging {
        opacity: 0.6;
        transform: rotate(3deg) scale(0.95);
        z-index: 1000;
        position: relative;
        transition: none;
        cursor: grabbing;
    }

    .drop-zone {
        transition: all 0.2s ease;
        position: relative;
    }

    .drop-zone.drag-over {
        background-color: rgba(25, 135, 84, 0.1) !important;
        border: 2px dashed #198754 !important;
        transform: scale(1.02);
    }

    .drag-move-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(25, 135, 84, 0.2);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        border-radius: 0.375rem;
    }

    .move-message {
        text-align: center;
        color: #198754;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.9);
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .move-message i {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
        display: block;
    }

    /* Drag preview */
    .drag-preview {
        position: fixed;
        pointer-events: none;
        z-index: 9999;
        opacity: 0.8;
        transform: rotate(5deg);
        background: white;
        border: 2px solid #007bff;
        border-radius: 0.375rem;
        padding: 0.5rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        max-width: 200px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#folder-table').DataTable();
    });

    document.addEventListener('DOMContentLoaded', function() {
        // View toggle functionality
        const iconViewBtn = document.getElementById('icon-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const iconView = document.getElementById('icon-view');
        const listView = document.getElementById('list-view');
        const mainContent = document.getElementById('main-content');
        const globalOverlay = document.getElementById('global-drop-overlay');
        const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));

        let dragCounter = 0;
        let isDraggingItem = false;
        let draggedItem = null;

        // File type validation
        const allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx'];
        const maxFileSize = 10 * 1024 * 1024; // 10MB

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
        document.querySelectorAll('.folder-item, .file-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                if (!isDraggingItem) {
                    this.querySelector('.item-actions').classList.remove('d-none');
                }
            });

            item.addEventListener('mouseleave', function() {
                this.querySelector('.item-actions').classList.add('d-none');
            });
        });

        // Drag and drop for moving items
        // Drag and drop for moving items
        document.querySelectorAll('[draggable="true"]').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                isDraggingItem = true;
                draggedItem = {
                    id: this.dataset.id,
                    type: this.dataset.type,
                    name: this.dataset.name || this.querySelector('.item-name')?.textContent || 'Unknown'
                };

                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('application/json', JSON.stringify(draggedItem));

                // Create custom drag preview
                const dragPreview = document.createElement('div');
                dragPreview.className = 'drag-preview';
                dragPreview.innerHTML = `
            <i class="bx ${draggedItem.type === 'folder' ? 'bx-folder' : 'bx-file'}" style="margin-right: 0.5rem; color: ${draggedItem.type === 'folder' ? '#ffc107' : '#6c757d'};"></i>
            <span>${draggedItem.name}</span>
        `;
                document.body.appendChild(dragPreview);

                // Set custom drag image
                e.dataTransfer.setDragImage(dragPreview, 20, 20);

                // Remove preview after drag starts
                setTimeout(() => {
                    if (document.body.contains(dragPreview)) {
                        document.body.removeChild(dragPreview);
                    }
                }, 0);

                // Add overlay to potential drop zones
                setTimeout(() => {
                    document.querySelectorAll('.drop-zone').forEach(zone => {
                        if (zone.dataset.id !== draggedItem.id) {
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
                        }
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

// Drop zone events for moving items
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();

                if (isDraggingItem && this.dataset.id !== draggedItem.id) {
                    e.dataTransfer.dropEffect = 'move';
                    this.classList.add('drag-over');

                    const overlay = this.querySelector('.drag-move-overlay');
                    if (overlay) {
                        overlay.classList.remove('d-none');
                    }
                } else if (!isDraggingItem) {
                    e.dataTransfer.dropEffect = 'copy';
                    this.classList.add('drag-over');
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

                if (isDraggingItem && this.dataset.id !== draggedItem.id) {
                    const targetFolderId = this.dataset.id;

                    // Prevent dropping folder into itself or its children
                    if (draggedItem.type === 'folder' && targetFolderId === draggedItem.id) {
                        alert('Cannot move folder into itself');
                        return;
                    }

                    moveItem(draggedItem, targetFolderId);
                } else if (!isDraggingItem) {
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleFileUploads(files);
                    }
                }
            });
        });

        // Drop zone events for moving items
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();

                if (isDraggingItem) {
                    e.dataTransfer.dropEffect = 'move';
                    this.classList.add('drag-over');
                } else {
                    e.dataTransfer.dropEffect = 'copy';
                }
            });

            zone.addEventListener('dragleave', function(e) {
                // Only remove drag-over if we're leaving the element and not entering a child
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');
                }
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                if (isDraggingItem) {
                    // Handle moving items
                    const targetFolderId = this.dataset.folderId || null;
                    const item = JSON.parse(e.dataTransfer.getData('application/json'));

                    // Don't allow dropping on self
                    if (item.type === 'folder' && item.id == targetFolderId) {
                        return;
                    }

                    moveItem(item, targetFolderId);
                } else {
                    // Handle file uploads
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleFileUploads(files);
                    }
                }
            });
        });

        // Global drag events for file upload (when not dragging items)
        document.addEventListener('dragenter', (e) => {
            if (!isDraggingItem) {
                e.preventDefault();
                dragCounter++;
                if (dragCounter === 1) {
                    globalOverlay.classList.remove('d-none');
                    mainContent.classList.add('drag-over');
                }
            }
        });

        document.addEventListener('dragleave', (e) => {
            if (!isDraggingItem) {
                e.preventDefault();
                dragCounter--;
                if (dragCounter === 0) {
                    globalOverlay.classList.add('d-none');
                    mainContent.classList.remove('drag-over');
                }
            }
        });

        document.addEventListener('dragover', (e) => {
            e.preventDefault();
        });

        document.addEventListener('drop', (e) => {
            if (!isDraggingItem) {
                e.preventDefault();
                dragCounter = 0;
                globalOverlay.classList.add('d-none');
                mainContent.classList.remove('drag-over');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileUploads(files);
                }
            }
        });

        function moveItem(item, targetFolderId) {
            const url = item.type === 'folder'
                ? `/folders/${item.id}/move`
                : `/documents/${item.id}/move`;

            const data = item.type === 'folder'
                ? { parent_id: targetFolderId }
                : { folder_id: targetFolderId };

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show mt-3';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;

                    const container = document.querySelector('.my-3');
                    container.parentNode.insertBefore(alert, container.nextSibling);

                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Move error:', error);
                alert('An error occurred while moving the item.');
            });
        }

        function handleFileUploads(files) {
            const validFiles = [];
            const errors = [];

            // Validate files
            Array.from(files).forEach(file => {
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (!allowedTypes.includes(fileExtension)) {
                    errors.push(`${file.name}: File type not supported`);
                    return;
                }

                if (file.size > maxFileSize) {
                    errors.push(`${file.name}: File size exceeds 10MB limit`);
                    return;
                }

                validFiles.push(file);
            });

            // Show errors if any
            if (errors.length > 0) {
                alert('Some files could not be uploaded:\n\n' + errors.join('\n'));
            }

            // Upload valid files
            if (validFiles.length > 0) {
                uploadFiles(validFiles);
            }
        }

        function uploadFiles(files) {
            const progressContainer = document.getElementById('upload-progress-container');
            const uploadFooter = document.getElementById('upload-footer');

            progressContainer.innerHTML = '';
            uploadFooter.classList.add('d-none');
            uploadModal.show();

            let completedUploads = 0;
            let successfulUploads = 0;

            Array.from(files).forEach((file, index) => {
                const progressItem = createProgressItem(file.name);
                progressContainer.appendChild(progressItem);

                uploadFile(file, progressItem, () => {
                    completedUploads++;
                    successfulUploads++;

                    if (completedUploads === files.length) {
                        uploadFooter.classList.remove('d-none');

                        if (successfulUploads > 0) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    }
                }, () => {
                    completedUploads++;

                    if (completedUploads === files.length) {
                        uploadFooter.classList.remove('d-none');
                    }
                });
            });
        }

        function createProgressItem(fileName) {
            const div = document.createElement('div');
            div.className = 'upload-progress-item';
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium">${fileName}</span>
                    <span class="status">Uploading...</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                </div>
            `;
            return div;
        }

        function uploadFile(file, progressItem, onSuccess, onError) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('folder_id', '{{ $folder->id }}');
            formData.append('_token', '{{ csrf_token() }}');

            const xhr = new XMLHttpRequest();
            const progressBar = progressItem.querySelector('.progress-bar');
            const statusSpan = progressItem.querySelector('.status');

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                }
            });

            xhr.addEventListener('load', () => {
                if (xhr.status === 200 || xhr.status === 302) {
                    progressItem.classList.add('success');
                    statusSpan.textContent = 'Uploaded';
                    statusSpan.className = 'status text-success';
                    progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                    progressBar.classList.add('bg-success');
                    progressBar.style.width = '100%';
                    onSuccess();
                } else {
                    progressItem.classList.add('error');
                    statusSpan.textContent = 'Failed';
                    statusSpan.className = 'status text-danger';
                    progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                    progressBar.classList.add('bg-danger');
                    onError();
                }
            });

            xhr.addEventListener('error', () => {
                progressItem.classList.add('error');
                statusSpan.textContent = 'Failed';
                statusSpan.className = 'status text-danger';
                progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                progressBar.classList.add('bg-danger');
                onError();
            });

            xhr.open('POST', '{{ route("documents.store") }}');
            xhr.send(formData);
        }

        // Delete folder modal functionality
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteFolderModal'));
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('folder-name').textContent = this.dataset.name;
                document.getElementById('delete-folder-form').action = `/folders/${this.dataset.id}`;
                deleteModal.show();
            });
        });

        // Delete document modal functionality
        const docModal = new bootstrap.Modal(document.getElementById('deleteDocModal'));
        document.querySelectorAll('.delete-doc-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('doc-name').textContent = this.dataset.name;
                document.getElementById('delete-doc-form').action = `/documents/${this.dataset.id}`;
                docModal.show();
            });
        });
    });
</script>
@endpush
@endsection
