@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content Area (Full Width) -->
        <div class="col-12 px-md-4">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb" class="d-none d-md-block me-3">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('folders.index') }}" class="text-decoration-none">
                                    <i class="bx bxs-home"></i>
                                </a>
                            </li>
                            @if($document->folder)
                                <?php $parents = collect([]); $parent = $document->folder->parent; ?>
                                @while($parent)
                                    <?php $parents->prepend($parent); $parent = $parent->parent; ?>
                                @endwhile

                                @foreach($parents as $parent)
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('folders.show', $parent) }}" class="text-decoration-none">
                                            {{ $parent->name }}
                                        </a>
                                    </li>
                                @endforeach

                                <li class="breadcrumb-item">
                                    <a href="{{ route('folders.show', $document->folder) }}" class="text-decoration-none">
                                        {{ $document->folder->name }}
                                    </a>
                                </li>
                            @endif
                            <li class="breadcrumb-item active" aria-current="page">{{ $document->original_filename }}</li>
                        </ol>
                    </nav>
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
                    <i class="bx
                        @if(in_array($document->file_type, ['pdf'])) bxs-file-pdf text-danger
                        @elseif(in_array($document->file_type, ['doc', 'docx'])) bxs-file-doc text-primary
                        @elseif(in_array($document->file_type, ['xls', 'xlsx'])) bxs-file-spreadsheet text-success
                        @elseif(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif'])) bxs-file-image text-info
                        @else bxs-file text-secondary
                        @endif
                    me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0">{{ $document->original_filename }}</h5>
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#detailsModal">
                        <i class="bx bx-info-circle"></i> More Details
                    </button>
                    @can('download ' . $document->department . ' documents')
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-success">
                        <i class="bx bx-download"></i> Download
                    </a>
                    @endcan
                    @can('share ' . $document->department . ' documents')
                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                        <i class="bx bx-share"></i> Share
                    </button>
                    @endcan
                    @can('edit ' . $document->department . ' documents')
                    <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                    @endcan
                    @can('delete ' . $document->department . ' documents')
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bx bx-trash"></i> Delete
                    </button>
                    @endcan
                </div>
            </div>

            @if($document->description)
                <div class="mb-4">
                    <h6>Description</h6>
                    <p class="text-muted">{{ $document->description }}</p>
                </div>
            @endif

            <!-- Document Preview Area -->
            <div class="document-preview">
                @if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                    <!-- Image Preview -->
                    <div class="text-center">
                        <img src="{{ Storage::url($document->file_path) }}"
                             alt="{{ $document->original_filename }}"
                             class="img-fluid rounded shadow"
                             style="max-height: 70vh;">
                    </div>
                @elseif($document->file_type === 'pdf')
                    <!-- PDF Preview -->
                    <div class="pdf-preview">
                        <div class="embed-responsive" style="height: 70vh;">
                            <iframe src="{{ Storage::url($document->file_path) }}"
                                    class="w-100 h-100 border rounded"
                                    style="min-height: 500px;"
                                    type="application/pdf">
                                <p>Your browser does not support PDFs.
                                   <a href="{{ route('documents.download', $document) }}">Download the PDF</a>.
                                </p>
                            </iframe>
                        </div>
                    </div>
                @elseif(in_array($document->file_type, ['doc', 'docx']))
                    <!-- Word Document Preview -->
                    <div class="word-preview">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Document Preview</h6>
                                <button class="btn btn-sm btn-outline-primary" onclick="loadWordPreview()">
                                    <i class="bx bx-refresh"></i> Load Preview
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="word-preview-content" class="text-center py-4">
                                    <i class="bx bxs-file-doc text-primary" style="font-size: 3rem;"></i>
                                    <p class="mt-2 text-muted">Click "Load Preview" to view document content</p>
                                </div>
                                <div id="word-preview-loading" class="text-center py-4 d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Converting document...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(in_array($document->file_type, ['txt']))
                    <!-- Text File Preview -->
                    <div class="text-preview">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">File Contents</h6>
                            </div>
                            <div class="card-body">
                                <pre class="mb-0" style="white-space: pre-wrap; max-height: 60vh; overflow-y: auto;">{{ Storage::get(str_replace('storage/', '', $document->file_path)) }}</pre>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Generic File Preview -->
                    <div class="text-center py-5">
                        <i class="bx
                            @if(in_array($document->file_type, ['doc', 'docx'])) bxs-file-doc text-primary
                            @elseif(in_array($document->file_type, ['xls', 'xlsx'])) bxs-file-spreadsheet text-success
                            @else bxs-file text-secondary
                            @endif
                        " style="font-size: 4rem;"></i>
                        <h4 class="mt-3">{{ $document->original_filename }}</h4>
                        <p class="text-muted">Preview not available for this file type</p>
                        @can('download ' . $document->department . ' documents')
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                            <i class="bx bx-download"></i> Download to View
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Document Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-info-circle me-2"></i>Document Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="small text-muted">File Name</label>
                            <div class="fw-medium">{{ $document->original_filename }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Department</label>
                            <div><span class="badge bg-primary">{{ $document->department_name }}</span></div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">File Type</label>
                            <div><span class="badge bg-secondary">{{ strtoupper($document->file_type) }}</span></div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">File Size</label>
                            <div>{{ number_format($document->file_size / 1024, 1) }} KB</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="small text-muted">Uploaded By</label>
                            <div class="fw-medium">{{ $document->user->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Created</label>
                            <div>{{ $document->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">Last Modified</label>
                            <div>{{ $document->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                        @if($document->folder)
                        <div class="mb-3">
                            <label class="small text-muted">Folder</label>
                            <div>
                                <a href="{{ route('folders.show', $document->folder) }}" class="text-decoration-none">
                                    <i class="bx bxs-folder me-1"></i>{{ $document->folder->name }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($document->description)
                <div class="border-top pt-3 mt-3">
                    <label class="small text-muted">Description</label>
                    <div class="mt-1">{{ $document->description }}</div>
                </div>
                @endif

                <!-- Document Metadata -->
                @if($document->meta_data && count($document->meta_data) > 0)
                <div class="border-top pt-3 mt-3">
                    <label class="small text-muted">Metadata</label>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($document->meta_data as $key => $value)
                                <tr>
                                    <td class="fw-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                    <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="share-link" class="form-label">Share Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="share-link" value="{{ route('documents.show', $document) }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyShareLink()">
                            <i class="bx bx-copy"></i> Copy
                        </button>
                    </div>
                </div>
                <p class="small text-muted">Anyone with access to {{ $document->department_name }} documents can view this file.</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Document</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Delete "<span class="fw-medium">{{ $document->original_filename }}</span>"?</p>
                <p class="mb-0 small text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bx bx-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .document-preview {
        background: white;
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        overflow: hidden;
    }

    .text-preview pre {
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        line-height: 1.4;
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.25rem;
    }

    .pdf-preview iframe {
        border: none;
    }

    @media (max-width: 768px) {
        .document-preview {
            margin: 0 -15px;
        }

        .pdf-preview iframe {
            height: 50vh !important;
            min-height: 300px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy share link functionality
    window.copyShareLink = function() {
        const shareLinkInput = document.getElementById('share-link');
        shareLinkInput.select();
        shareLinkInput.setSelectionRange(0, 99999);

        try {
            document.execCommand('copy');
            const copyBtn = shareLinkInput.nextElementSibling;
            const originalText = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="bx bx-check"></i> Copied!';
            copyBtn.classList.remove('btn-outline-secondary');
            copyBtn.classList.add('btn-success');

            setTimeout(() => {
                copyBtn.innerHTML = originalText;
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-secondary');
            }, 2000);
        } catch (err) {
            console.error('Failed to copy text: ', err);
        }
    };

    // Word document preview functionality
    window.loadWordPreview = function() {
        const contentDiv = document.getElementById('word-preview-content');
        const loadingDiv = document.getElementById('word-preview-loading');

        contentDiv.classList.add('d-none');
        loadingDiv.classList.remove('d-none');

        fetch(`{{ route('documents.preview', $document) }}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.classList.add('d-none');

            if (data.success) {
                contentDiv.innerHTML = data.content;
                contentDiv.classList.remove('d-none');
            } else {
                contentDiv.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bx bx-error text-danger" style="font-size: 3rem;"></i>
                        <p class="mt-2 text-danger">${data.message}</p>
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                            <i class="bx bx-download"></i> Download Document
                        </a>
                    </div>
                `;
                contentDiv.classList.remove('d-none');
            }
        })
        .catch(error => {
            loadingDiv.classList.add('d-none');
            contentDiv.innerHTML = `
                <div class="text-center py-4">
                    <i class="bx bx-error text-danger" style="font-size: 3rem;"></i>
                    <p class="mt-2 text-danger">Error loading preview</p>
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                        <i class="bx bx-download"></i> Download Document
                    </a>
                </div>
            `;
            contentDiv.classList.remove('d-none');
        });
    };

    // Handle PDF loading errors
    const pdfIframe = document.querySelector('.pdf-preview iframe');
    if (pdfIframe) {
        pdfIframe.addEventListener('error', function() {
            this.outerHTML = `
                <div class="text-center py-5">
                    <i class="bx bxs-file-pdf text-danger" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">PDF Preview Unavailable</h4>
                    <p class="text-muted">Unable to preview this PDF file</p>
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                        <i class="bx bx-download"></i> Download PDF
                    </a>
                </div>
            `;
        });
    }

    // Handle image loading errors
    const previewImage = document.querySelector('.document-preview img');
    if (previewImage) {
        previewImage.addEventListener('error', function() {
            this.outerHTML = `
                <div class="text-center py-5">
                    <i class="bx bx-image-alt text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Image Preview Unavailable</h4>
                    <p class="text-muted">Unable to preview this image file</p>
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                        <i class="bx bx-download"></i> Download Image
                    </a>
                </div>
            `;
        });
    }
});
</script>
@endpush

@endsection
