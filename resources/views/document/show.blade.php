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
                        @can('download ' . $document->baseFolder->name . ' documents')
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-success">
                                <i class="bx bx-download"></i> Download
                            </a>
                        @endcan
                        @can('share ' . $document->baseFolder->name . ' documents')
                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <i class="bx bx-share"></i> Share
                            </button>
                        @endcan
                        @can('edit ' . $document->baseFolder->name . ' documents')
                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                        @endcan
                        @can('delete ' . $document->baseFolder->name . ' documents')
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

                <!-- Document Preview Area - Always Visible -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card" id="preview-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class='bx bx-show'></i> Document Preview</h5>
                                <button class="btn btn-sm btn-outline-secondary" onclick="hidePreview()">
                                    <i class='bx bx-x'></i> Close Preview
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="document-preview">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Loading preview...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keep all your existing modals here -->
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
                                <div><span class="badge bg-primary">{{ $document->baseFolder->name }}</span></div>
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
                    <p class="small text-muted">Anyone with access to {{ $document->baseFolder->name_name }} documents can view this file.</p>
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

        .word-preview .card {
            margin-bottom: 0;
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
            // Auto-load preview when page loads
            document.addEventListener('DOMContentLoaded', function() {
                previewDocument('{{ $document->mime_type }}', '{{ addslashes($document->original_filename) }}');
            });

            function previewDocument(mimeType, fileName) {
                const previewContent = document.getElementById('document-preview');

                const previewUrl = '{{ route("documents.preview", $document) }}';
                const previewApiUrl = '{{ route("documents.preview-api", $document) }}';
                const downloadUrl = '{{ route("documents.download", $document) }}';

                // Show loading spinner
                previewContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading preview...</p>
        </div>
    `;

                if (mimeType.includes('pdf')) {
                    previewContent.innerHTML = `
            <div class="pdf-preview">
                <div class="embed-responsive" style="height: 70vh;">
                    <iframe src="${previewUrl}"
                            class="w-100 h-100 border rounded"
                            style="min-height: 500px;"
                            type="application/pdf">
                        <p>Your browser does not support PDFs.
                           <a href="${downloadUrl}">Download the PDF</a>.
                        </p>
                    </iframe>
                </div>
            </div>
        `;
                } else if (mimeType.includes('image')) {
                    previewContent.innerHTML = `
            <div class="text-center">
                <img src="${previewUrl}"
                     alt="${fileName}"
                     class="img-fluid rounded shadow"
                     style="max-height: 70vh;"
                     onerror="this.outerHTML='<div class=\\'text-center py-5\\'><i class=\\'bx bx-image-alt text-muted\\' style=\\'font-size: 4rem;\\'></i><h4 class=\\'mt-3\\'>Image Preview Unavailable</h4><p class=\\'text-muted\\'>Unable to preview this image file</p><a href=\\'${downloadUrl}\\' class=\\'btn btn-primary\\'><i class=\\'bx bx-download\\'></i> Download to View</a></div>'">
            </div>
        `;
                } else if (mimeType.includes('word') || mimeType.includes('document')) {
                    previewContent.innerHTML = `
            <div class="word-preview">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Document Preview</h6>
                    </div>
                    <div class="card-body">
                        <div id="word-preview-loading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Converting document...</p>
                        </div>
                        <div id="word-preview-content" class="d-none"></div>
                    </div>
                </div>
            </div>
        `;

                    // Auto-load Word preview
                    fetch(previewApiUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('word-preview-loading').classList.add('d-none');
                            const contentDiv = document.getElementById('word-preview-content');
                            if (data.success) {
                                contentDiv.innerHTML = `
                    <div class="word-content" style="text-align: left; max-height: 60vh; overflow-y: auto; padding: 1rem;">
                        ${data.content}
                    </div>
                `;
                            } else {
                                contentDiv.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bx bxs-file-doc text-danger" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-danger">Preview Failed</h5>
                        <p class="text-muted">${data.message || 'Unable to generate preview'}</p>
                        <a href="${downloadUrl}" class="btn btn-primary">
                            <i class="bx bx-download"></i> Download Document
                        </a>
                    </div>
                `;
                            }
                            contentDiv.classList.remove('d-none');
                        })
                        .catch(() => {
                            document.getElementById('word-preview-loading').classList.add('d-none');
                            const contentDiv = document.getElementById('word-preview-content');
                            contentDiv.innerHTML = `
                <div class="text-center py-4">
                    <i class="bx bxs-file-doc text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-danger">Preview Error</h5>
                    <p class="text-muted">An error occurred while loading the preview</p>
                    <a href="${downloadUrl}" class="btn btn-primary">
                        <i class="bx bx-download"></i> Download Document
                    </a>
                </div>
            `;
                            contentDiv.classList.remove('d-none');
                        });
                } else if (mimeType.includes('text')) {
                    fetch(previewUrl)
                        .then(response => response.text())
                        .then(text => {
                            previewContent.innerHTML = `
                    <div class="text-preview">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">File Contents</h6>
                            </div>
                            <div class="card-body">
                                <pre class="mb-0" style="white-space: pre-wrap; max-height: 60vh; overflow-y: auto;">${text}</pre>
                            </div>
                        </div>
                    </div>
                `;
                        })
                        .catch(() => {
                            showGenericPreview();
                        });
                } else {
                    showGenericPreview();
                }

                function showGenericPreview() {
                    let iconClass = 'bxs-file text-secondary';
                    if (mimeType.includes('word') || mimeType.includes('document')) {
                        iconClass = 'bxs-file-doc text-primary';
                    } else if (mimeType.includes('sheet') || mimeType.includes('excel')) {
                        iconClass = 'bxs-file-spreadsheet text-success';
                    } else if (mimeType.includes('presentation') || mimeType.includes('powerpoint')) {
                        iconClass = 'bxs-file-presentation text-warning';
                    }

                    previewContent.innerHTML = `
            <div class="text-center py-5">
                <i class="bx ${iconClass}" style="font-size: 4rem;"></i>
                <h4 class="mt-3">${fileName}</h4>
                <p class="text-muted">Preview not available for this file type</p>
                <a href="${downloadUrl}" class="btn btn-primary">
                    <i class="bx bx-download"></i> Download to View
                </a>
            </div>
        `;
                }
            }

            function hidePreview() {
                document.getElementById('preview-card').style.display = 'none';
            }

            function copyShareLink() {
                const shareLink = document.getElementById('share-link');
                shareLink.select();
                shareLink.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(shareLink.value);
            }
        </script>
    @endpush

@endsection
