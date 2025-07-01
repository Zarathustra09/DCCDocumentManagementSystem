@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Upload Document</h5>
                        <a href="javascript:history.back()" class="btn btn-sm btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to Documents
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                        @csrf

                        <!-- Drag and Drop Zone -->
                        <div class="mb-3">
                            <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                            <div id="drop-zone" class="drop-zone">
                                <div class="drop-zone-content">
                                    <i class="bx bx-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                                    <h5 class="mt-3 mb-2">Drop files here or click to browse</h5>
                                    <p class="text-muted mb-3">Maximum file size: 10MB</p>
                                    <input type="file" class="form-control d-none @error('file') is-invalid @enderror"
                                           id="file" name="file" required accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.xls,.xlsx">
                                    <button type="button" class="btn btn-outline-primary" id="browse-btn">
                                        <i class="bx bx-folder-open"></i> Browse Files
                                    </button>
                                </div>
                                <div id="file-preview" class="file-preview d-none">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="bx bx-file me-2" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <div id="file-name" class="fw-medium"></div>
                                                <div id="file-size" class="text-muted small"></div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="remove-file">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="folder_id" class="form-label">Folder</label>
                            <select class="form-select @error('folder_id') is-invalid @enderror" id="folder_id" name="folder_id">
                                <option value="">No Folder (Root Level)</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ request()->get('folder_id') == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('folder_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                <i class="bx bx-upload"></i> Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bx bx-info-circle"></i> Supported File Types</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-pdf text-danger me-2"></i> PDF Documents
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-doc text-primary me-2"></i> Word Documents (.doc, .docx)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-txt text-secondary me-2"></i> Text Files (.txt)
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-image text-info me-2"></i> Images (.jpg, .png, .gif)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-spreadsheet text-success me-2"></i> Spreadsheets (.xls, .xlsx)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file text-warning me-2"></i> Other Common Formats
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Drop Overlay -->
<div id="global-drop-overlay" class="global-drop-overlay d-none">
    <div class="drop-message">
        <i class="bx bx-cloud-upload" style="font-size: 4rem;"></i>
        <h3>Drop files anywhere to upload</h3>
    </div>
</div>

@push('styles')
<style>
.drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
    cursor: pointer;
}

.drop-zone:hover {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.drop-zone.drag-over {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.1);
    transform: scale(1.02);
}

.drop-zone.has-file {
    border-color: #198754;
    background-color: rgba(25, 135, 84, 0.05);
}

.file-preview {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background-color: white;
}

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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file');
    const browseBtn = document.getElementById('browse-btn');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeFileBtn = document.getElementById('remove-file');
    const submitBtn = document.getElementById('submit-btn');
    const globalOverlay = document.getElementById('global-drop-overlay');
    const uploadForm = document.getElementById('upload-form');

    let dragCounter = 0;

    // File type icons mapping
    const fileIcons = {
        'pdf': 'bxs-file-pdf text-danger',
        'doc': 'bxs-file-doc text-primary',
        'docx': 'bxs-file-doc text-primary',
        'txt': 'bxs-file-txt text-secondary',
        'jpg': 'bxs-file-image text-info',
        'jpeg': 'bxs-file-image text-info',
        'png': 'bxs-file-image text-info',
        'gif': 'bxs-file-image text-info',
        'xls': 'bxs-file-spreadsheet text-success',
        'xlsx': 'bxs-file-spreadsheet text-success'
    };

    // Browse button click
    browseBtn.addEventListener('click', () => {
        fileInput.click();
    });

    // Drop zone click
    dropZone.addEventListener('click', (e) => {
        if (e.target === dropZone || e.target.closest('.drop-zone-content')) {
            fileInput.click();
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    // Remove file button
    removeFileBtn.addEventListener('click', () => {
        clearFile();
    });

    // Global drag events
    document.addEventListener('dragenter', (e) => {
        e.preventDefault();
        dragCounter++;
        if (dragCounter === 1) {
            globalOverlay.classList.remove('d-none');
        }
    });

    document.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dragCounter--;
        if (dragCounter === 0) {
            globalOverlay.classList.add('d-none');
        }
    });

    document.addEventListener('dragover', (e) => {
        e.preventDefault();
    });

    document.addEventListener('drop', (e) => {
        e.preventDefault();
        dragCounter = 0;
        globalOverlay.classList.add('d-none');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFiles(files);
        }
    });

    // Drop zone specific events
    dropZone.addEventListener('dragenter', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (!dropZone.contains(e.relatedTarget)) {
            dropZone.classList.remove('drag-over');
        }
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('drag-over');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFiles(files);
        }
    });

    function handleFiles(files) {
        if (files.length === 0) return;

        const file = files[0];

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB');
            return;
        }

        // Validate file type
        const allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx'];
        const fileExtension = file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(fileExtension)) {
            alert('File type not supported. Please select a valid file type.');
            return;
        }

        // Update file input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;

        // Show file preview
        showFilePreview(file);
    }

    function showFilePreview(file) {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const iconClass = fileIcons[fileExtension] || 'bxs-file text-secondary';

        // Update preview content
        filePreview.querySelector('i').className = `bx ${iconClass} me-2`;
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);

        // Show preview and hide drop zone content
        dropZone.querySelector('.drop-zone-content').classList.add('d-none');
        filePreview.classList.remove('d-none');
        dropZone.classList.add('has-file');

        // Enable submit button
        submitBtn.disabled = false;
    }

    function clearFile() {
        // Clear file input
        fileInput.value = '';

        // Hide preview and show drop zone content
        filePreview.classList.add('d-none');
        dropZone.querySelector('.drop-zone-content').classList.remove('d-none');
        dropZone.classList.remove('has-file');

        // Disable submit button
        submitBtn.disabled = true;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission with loading state
    uploadForm.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Uploading...';
        submitBtn.disabled = true;
    });
});
</script>
@endpush
@endsection
