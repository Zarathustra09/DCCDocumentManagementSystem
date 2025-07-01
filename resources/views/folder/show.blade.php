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
                        <a class="nav-link d-flex align-items-center {{ !$folder->parent_id ? 'active text-primary' : 'text-dark' }}" href="{{ route('folders.index') }}">
                            <i class="bx bxs-home me-2"></i> Home
                        </a>
                    </li>
                    @if($folder->parent)
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center text-dark" href="{{ route('folders.show', $folder->parent) }}">
                            <i class="bx bx-arrow-back me-2"></i> Back
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
                                <a href="{{ route('folders.index') }}" class="text-decoration-none"><i class="bx bxs-home"></i></a>
                            </li>
                            @if($folder->parent)
                                <?php $parents = collect([]); $parent = $folder->parent; ?>
                                @while($parent)
                                    <?php $parents->prepend($parent); $parent = $parent->parent; ?>
                                @endwhile

                                @foreach($parents as $parent)
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('folders.show', $parent) }}" class="text-decoration-none">{{ $parent->name }}</a>
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
                    @can('create folders')
                    <a href="{{ route('folders.create') }}?parent_id={{ $folder->id }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-folder-plus"></i> Add
                    </a>
                    @endcan
                    @can('create documents')
                    <a href="{{ route('documents.create') }}?folder_id={{ $folder->id }}" class="btn btn-sm btn-outline-success">
                        <i class="bx bx-upload"></i> Upload
                    </a>
                    @endcan
                    @can('edit folders')
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
                            <div class="folder-item position-relative rounded shadow-sm">
                                <a href="{{ route('folders.show', $subfolder) }}" class="text-decoration-none">
                                    <div class="p-3 d-flex flex-column align-items-center" style="background-color: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.2); border-radius: 0.375rem;">
                                        <i class="bx bxs-folder text-warning" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0 text-truncate w-100 text-center small">{{ $subfolder->name }}</p>
                                    </div>
                                </a>
                                <div class="item-actions position-absolute top-0 end-0 p-1 d-none">
                                    <div class="btn-group btn-group-sm">
                                        @can('edit folders')
                                        <a href="{{ route('folders.edit', $subfolder) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete folders')
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
                            <div class="file-item position-relative rounded shadow-sm">
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
                                        @can('download documents')
                                        <a href="{{ route('documents.download', $document) }}" class="btn btn-light btn-sm rounded-circle" title="Download">
                                            <i class="bx bx-download"></i>
                                        </a>
                                        @endcan
                                        @can('edit documents')
                                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete documents')
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
                            <div class="empty-state p-4 rounded" style="background-color: #f8f9fa;">
                                <i class="bx bx-folder-open text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-3 text-muted">This folder is empty</p>
                                <p class="small text-muted mb-3">Drag files here to upload them to this folder</p>
                                <div>
                                    @can('create folders')
                                    <a href="{{ route('folders.create') }}?parent_id={{ $folder->id }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bx bx-folder-plus"></i> Add
                                    </a>
                                    @endcan
                                    @can('create documents')
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
                    <table class="table table-hover align-middle">
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
                                <tr>
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
                                            @can('edit folders')
                                            <a href="{{ route('folders.edit', $subfolder) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete folders')
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
                                <tr>
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
                                            @can('download documents')
                                            <a href="{{ route('documents.download', $document) }}" class="btn btn-outline-success btn-sm" title="Download">
                                                <i class="bx bx-download"></i>
                                            </a>
                                            @endcan
                                            @can('edit documents')
                                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete documents')
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
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bx bx-folder-open text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0 text-muted">This folder is empty</p>
                                        <p class="small text-muted">Drag files here to upload them to this folder</p>
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
        <h3>Drop files to upload to "{{ $folder->name }}"</h3>
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
.global-drop-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(13, 110, 253, 0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.drop-message {
    text-align: center;
    color: white;
}

.drop-message i {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.main-content.drag-over {
    background-color: rgba(13, 110, 253, 0.05);
    transition: background-color 0.3s ease;
}

.upload-progress-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background-color: #f8f9fa;
}

.upload-progress-item.success {
    border-color: #198754;
    background-color: rgba(25, 135, 84, 0.05);
}

.upload-progress-item.error {
    border-color: #dc3545;
    background-color: rgba(220, 53, 69, 0.05);
}
</style>
@endpush

@push('scripts')
<script>
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
                this.querySelector('.item-actions').classList.remove('d-none');
            });

            item.addEventListener('mouseleave', function() {
                this.querySelector('.item-actions').classList.add('d-none');
            });
        });

        // Global drag events for file upload
        document.addEventListener('dragenter', (e) => {
            e.preventDefault();
            dragCounter++;
            if (dragCounter === 1) {
                globalOverlay.classList.remove('d-none');
                mainContent.classList.add('drag-over');
            }
        });

        document.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dragCounter--;
            if (dragCounter === 0) {
                globalOverlay.classList.add('d-none');
                mainContent.classList.remove('drag-over');
            }
        });

        document.addEventListener('dragover', (e) => {
            e.preventDefault();
        });

        document.addEventListener('drop', (e) => {
            e.preventDefault();
            dragCounter = 0;
            globalOverlay.classList.add('d-none');
            mainContent.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileUploads(files);
            }
        });

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
