@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <nav aria-label="breadcrumb" class="d-none d-md-block">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('document-registry.index') }}" class="text-decoration-none">
                                        <i class="bx bx-file-find"></i> Document Registry
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $documentRegistrationEntry->document_no }}
                                </li>
                            </ol>
                        </nav>
                        <h4 class="mb-0 mt-2">
                            <i class="bx bx-file-find me-2"></i>
                            {{ $documentRegistrationEntry->document_title }}
                        </h4>
                        <p class="text-muted mb-0">{{ $documentRegistrationEntry->full_document_number }}</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class='bx bx-plus'></i> Submit New Document Registration</h3>
                      <a href="{{route('document-registry.index')}}" class="btn btn-secondary">
                            <i class='bx bx-arrow-back'></i> Back to Registry
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Left Column - Document Information -->
                            <div class="col-md-8">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Document Number</label>
                                            <p class="mb-0 fw-medium">{{ $documentRegistrationEntry->document_no }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Revision</label>
                                            <p class="mb-0">{{ $documentRegistrationEntry->revision_no }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Originator</label>
                                            <p class="mb-0">{{ $documentRegistrationEntry->originator_name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @if($documentRegistrationEntry->device_name)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Device/Equipment</label>
                                                <p class="mb-0">{{ $documentRegistrationEntry->device_name }}</p>
                                            </div>
                                        @endif
                                        @if($documentRegistrationEntry->customer)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Customer</label>
                                                <p class="mb-0">{{ $documentRegistrationEntry->customer }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($documentRegistrationEntry->remarks)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Remarks</label>
                                        <div class="p-3 bg-light rounded">
                                            {{ $documentRegistrationEntry->remarks }}
                                        </div>
                                    </div>
                                @endif

                                <!-- File Information and Preview -->
                                @if($documentRegistrationEntry->hasFile())
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Attached File</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="file-info d-flex align-items-center flex-grow-1">
                                                <i class="bx
                                                @if(str_contains($documentRegistrationEntry->mime_type, 'pdf')) bxs-file-pdf text-danger
                                                @elseif(str_contains($documentRegistrationEntry->mime_type, 'word') || str_contains($documentRegistrationEntry->mime_type, 'document')) bxs-file-doc text-primary
                                                @elseif(str_contains($documentRegistrationEntry->mime_type, 'sheet') || str_contains($documentRegistrationEntry->mime_type, 'excel')) bxs-file-spreadsheet text-success
                                                @elseif(str_contains($documentRegistrationEntry->mime_type, 'image')) bxs-file-image text-info
                                                @else bxs-file text-secondary
                                                @endif
                                                me-2" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <div class="fw-medium">{{ $documentRegistrationEntry->original_filename }}</div>
                                                    <small class="text-muted">{{ $documentRegistrationEntry->formatted_file_size }}</small>
                                                </div>
                                            </div>
                                            <div class="file-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="previewDocument()">
                                                    <i class="bx bx-show"></i> Preview
                                                </button>
                                                <a href="{{ route('document-registry.download', $documentRegistrationEntry) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="bx bx-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column - Status and Actions -->
                            <div class="col-md-4">
                                <!-- Status Card -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class='bx bx-info-circle'></i> Status Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Current Status</label><br>
                                            @if($documentRegistrationEntry->status === 'pending')
                                                <span class="badge bg-warning text-dark fs-6">
                                                <i class='bx bx-time'></i> {{ $documentRegistrationEntry->status_name }}
                                            </span>
                                            @elseif($documentRegistrationEntry->status === 'approved')
                                                <span class="badge bg-success text-white fs-6">
                                                <i class='bx bx-check'></i> {{ $documentRegistrationEntry->status_name }}
                                            </span>
                                            @else
                                                <span class="badge bg-danger text-white fs-6">
                                                <i class='bx bx-x'></i> {{ $documentRegistrationEntry->status_name }}
                                            </span>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-muted">Submitted By</label>
                                            <p class="mb-0">{{ $documentRegistrationEntry->submittedBy->name }}</p>
                                            <small class="text-muted">{{ $documentRegistrationEntry->submitted_at->format('M d, Y \a\t g:i A') }}</small>
                                        </div>

                                        @if($documentRegistrationEntry->approved_by)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">
                                                    @if($documentRegistrationEntry->status === 'approved')
                                                        Approved By
                                                    @else
                                                        Rejected By
                                                    @endif
                                                </label>
                                                <p class="mb-0">{{ $documentRegistrationEntry->approvedBy->name }}</p>
                                                <small class="text-muted">{{ $documentRegistrationEntry->approved_at->format('M d, Y \a\t g:i A') }}</small>
                                            </div>
                                        @endif

                                        @if($documentRegistrationEntry->rejection_reason)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Rejection Reason</label>
                                                <div class="alert alert-danger p-2">
                                                    {{ $documentRegistrationEntry->rejection_reason }}
                                                </div>
                                            </div>
                                        @endif

                                        @if($documentRegistrationEntry->revision_notes)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Revision Notes</label>
                                                <div class="alert alert-info p-2">
                                                    {{ $documentRegistrationEntry->revision_notes }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions Card for Approvers -->
                                @if($documentRegistrationEntry->status === 'pending' && (auth()->user()->can('approve document registration') || auth()->user()->can('reject document registration')))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class='bx bx-cog'></i> Actions</h5>
                                        </div>
                                        <div class="card-body">
                                            @can('approve document registration')
                                                <form action="{{ route('document-registry.approve', $documentRegistrationEntry) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm w-100 mb-2"
                                                            onclick="return confirm('Are you sure you want to approve this document registration?')">
                                                        <i class='bx bx-check'></i> Approve
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('reject document registration')
                                                <button type="button" class="btn btn-danger btn-sm w-100 mb-2"
                                                        data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                    <i class='bx bx-x'></i> Reject
                                                </button>
                                            @endcan

{{--                                            @can('require revision for document')--}}
{{--                                                <button type="button" class="btn btn-warning btn-sm w-100"--}}
{{--                                                        data-bs-toggle="modal" data-bs-target="#revisionModal">--}}
{{--                                                    <i class='bx bx-edit'></i> Require Revision--}}
{{--                                                </button>--}}
{{--                                            @endcan--}}
                                        </div>
                                    </div>
                                @endif

                                <!-- Withdraw Action for Submitter -->
                                @if($documentRegistrationEntry->status === 'pending' &&
                                    $documentRegistrationEntry->submitted_by === auth()->id() &&
                                    auth()->user()->can('withdraw document submission'))
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <form action="{{ route('document-registry.withdraw', $documentRegistrationEntry) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                                        onclick="return confirm('Are you sure you want to withdraw this submission? This action cannot be undone.')">
                                                    <i class='bx bx-trash'></i> Withdraw Submission
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Preview Section -->
        @if($documentRegistrationEntry->hasFile())
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card" id="preview-card" style="display: none;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class='bx bx-show'></i> Document Preview</h5>
                            <button class="btn btn-sm btn-outline-secondary" onclick="hidePreview()">
                                <i class='bx bx-x'></i> Close Preview
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="document-preview">
                                <!-- Preview content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Referenced Documents Section (only show for approved entries) -->
        @if($documentRegistrationEntry->status === 'approved' && $documentRegistrationEntry->documents->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class='bx bx-file'></i> Referenced Documents</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Description</th>
                                        <th>Department</th>
                                        <th>File Size</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($documentRegistrationEntry->documents as $document)
                                        <tr>
                                            <td>
                                                <i class='bx bx-file me-1'></i>
                                                {{ $document->original_filename }}
                                            </td>
                                            <td>{{ $document->description ?? 'No description' }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $document->department_name }}</span>
                                            </td>
                                            <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $document->created_at->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if(auth()->user()->can("view {$document->department} documents"))
                                                    <a href="{{ route('documents.download', $document) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class='bx bx-download'></i> Download
                                                    </a>
                                                @endif
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
        @endif
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Document Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('document-registry.reject', $documentRegistrationEntry) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required
                                      placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Registration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Revision Modal -->
    <div class="modal fade" id="revisionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Require Revision</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('document-registry.require-revision', $documentRegistrationEntry) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="revision_notes" class="form-label">Revision Notes <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="revision_notes" name="revision_notes" rows="4" required
                                      placeholder="Please specify what needs to be revised..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Request Revision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection


@push('styles')
    <style>
        .badge {
            font-size: 0.9em;
            padding: 0.5rem 0.75rem;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .badge.bg-success {
            background-color: #198754 !important;
            color: #ffffff !important;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .fs-6 {
            font-size: 1rem !important;
        }

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
        function previewDocument() {
            const previewCard = document.getElementById('preview-card');
            const previewContent = document.getElementById('document-preview');

            previewCard.style.display = 'block';
            previewCard.scrollIntoView({ behavior: 'smooth' });

            const mimeType = '{{ $documentRegistrationEntry->mime_type }}';
            const fileName = '{{ $documentRegistrationEntry->original_filename }}';
            const previewUrl = '{{ route("document-registry.preview", $documentRegistrationEntry) }}';
            const downloadUrl = '{{ route("document-registry.download", $documentRegistrationEntry) }}';

            // Show loading
            previewContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading preview...</p>
        </div>
    `;

            // Handle different file types
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
                // Word Document Preview - Auto-load
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
                loadWordPreviewAuto();
            } else if (mimeType.includes('text')) {
                // Try to load text preview
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
                // Generic file preview
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

        // Auto-loading Word document preview functionality
        function loadWordPreviewAuto() {
            const contentDiv = document.getElementById('word-preview-content');
            const loadingDiv = document.getElementById('word-preview-loading');

            const previewApiUrl = '{{ route("document-registry.preview-api", $documentRegistrationEntry) }}';

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
                    loadingDiv.classList.add('d-none');

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
                    <a href="{{ route('document-registry.download', $documentRegistrationEntry) }}" class="btn btn-primary">
                        <i class="bx bx-download"></i> Download Document
                    </a>
                </div>
            `;
                    }

                    contentDiv.classList.remove('d-none');
                })
                .catch(error => {
                    loadingDiv.classList.add('d-none');
                    contentDiv.innerHTML = `
            <div class="text-center py-4">
                <i class="bx bxs-file-doc text-danger" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-danger">Preview Error</h5>
                <p class="text-muted">An error occurred while loading the preview</p>
                <a href="{{ route('document-registry.download', $documentRegistrationEntry) }}" class="btn btn-primary">
                    <i class="bx bx-download"></i> Download Document
                </a>
            </div>
        `;
                    contentDiv.classList.remove('d-none');
                });
        }

        function hidePreview() {
            document.getElementById('preview-card').style.display = 'none';
        }

        // Word document preview functionality
        window.loadWordPreview = function() {
            const contentDiv = document.getElementById('word-preview-content');
            const loadingDiv = document.getElementById('word-preview-loading');

            contentDiv.classList.add('d-none');
            loadingDiv.classList.remove('d-none');

            // Note: You'll need to add a preview route for document registry entries
            const previewApiUrl = '{{ route("document-registry.preview-api", $documentRegistrationEntry) }}';

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
                    loadingDiv.classList.add('d-none');

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
                    <a href="{{ route('document-registry.download', $documentRegistrationEntry) }}" class="btn btn-primary">
                        <i class="bx bx-download"></i> Download Document
                    </a>
                </div>
            `;
                    }

                    contentDiv.classList.remove('d-none');
                })
                .catch(error => {
                    loadingDiv.classList.add('d-none');
                    contentDiv.innerHTML = `
            <div class="text-center py-4">
                <i class="bx bxs-file-doc text-danger" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-danger">Preview Error</h5>
                <p class="text-muted">An error occurred while loading the preview</p>
                <a href="{{ route('document-registry.download', $documentRegistrationEntry) }}" class="btn btn-primary">
                    <i class="bx bx-download"></i> Download Document
                </a>
            </div>
        `;
                    contentDiv.classList.remove('d-none');
                });
        };
    </script>
@endpush
