@extends('layouts.app')

@section('content')
    @php $showHelpTour = true; @endphp
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom" data-driver="breadcrumb">
                    <div>
                        <nav aria-label="breadcrumb" class="d-none d-md-block">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('document-registry.index') }}" class="text-decoration-none">
                                        <i class="bx bx-file-find"></i> My Registrations
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $documentRegistrationEntry->document_title ?? '-' }}
                                </li>
                            </ol>
                        </nav>
                        <h4 class="mb-0 mt-2">
{{--                            <i class="bx bx-file-find me-2"></i>--}}
                            Registration: {{ $documentRegistrationEntry->control_no ?? '-'}}
                        </h4>
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
                 <div class="card-header d-flex justify-content-between align-items-center" data-driver="card-header">
                     <h3 class="card-title">
                         @if(auth()->id() === $documentRegistrationEntry->submitted_by)
                             My Registration
                         @else
                             Document Registry
                         @endif
                     </h3>
                     <a href="{{ route('document-registry.index') }}" class="btn btn-secondary">
                         <i class='bx bx-arrow-back'></i> Back to Registry
                     </a>
                 </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Left Column - Document Information -->
                            <div class="col-md-8">
                                <div class="row mb-4" data-driver="document-info">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Document Number</label>
                                            <p class="mb-0 fw-medium">{{ $documentRegistrationEntry->document_no ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Revision</label>
                                            <p class="mb-0">{{ $documentRegistrationEntry->revision_no ?? '-'}}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Originator</label>
                                            <p class="mb-0">{{ $documentRegistrationEntry->originator_name ?? '-'}}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @if($documentRegistrationEntry->device_name)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Device Part Number</label>
                                                <p class="mb-0">{{ $documentRegistrationEntry->device_name }}</p>
                                            </div>
                                        @endif
                                        @if($documentRegistrationEntry->customer)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Customer</label>
                                                <p class="mb-0">{{ $documentRegistrationEntry->customer->name }}</p>
                                            </div>
                                        @endif

                                            @if($documentRegistrationEntry->category)
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Category</label>
                                                    <p class="mb-0">{{ $documentRegistrationEntry->category->name }}</p>
                                                </div>
                                            @endif

                                    </div>
                                </div>

                                @if($documentRegistrationEntry->remarks)
                                    <div class="mb-3" data-driver="remarks">
                                        <label class="form-label text-muted">Remarks</label>
                                        <div class="p-3 bg-light rounded">
                                            {{ $documentRegistrationEntry->remarks }}
                                        </div>
                                    </div>
                                @endif

                                <!-- File Information and Preview -->
                                @php
                                    $file = $documentRegistrationEntry->files->first();
                                @endphp
                                @if($documentRegistrationEntry->files->count())
                                    <div class="mb-3" data-driver="file-table">
                                        <label class="form-label text-muted">Attached Files</label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm align-middle" id="fileTable">
                                                <thead>
                                                <tr>
                                                    <th>File Name</th>
                                                    <th>Size</th>
                                                    <th>Status</th>
                                                    <th>Time Submitted</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($documentRegistrationEntry->files as $file)
                                                    <tr>
                                                        <td>{{ $file->original_filename ?? '-' }}</td>
                                                        <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                                                        <td>
                                                            <span class="badge
                                                                @if($file->status->name === 'Pending') bg-warning text-dark
                                                                @elseif($file->status->name === 'Implemented') bg-success
                                                                @else bg-danger
                                                                @endif">
                                                                {{ $file->status->name }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            {{ $file->created_at?->format('m/d/Y g:ia') ?? '-' }}
                                                        </td>
                                                        <td>
                                                            @if($file->status->name === 'Pending' && auth()->user()->can('approve document registration'))
                                                                <form action="{{ route('document-registry.files.approve', $file->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success"
                                                                            onclick="return confirm('Implement this file?')">
                                                                        <i class="bx bx-check"></i> Implement
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            @if($file->status->name === 'Pending' && auth()->user()->can('reject document registration'))
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                        data-bs-toggle="modal" data-bs-target="#rejectFileModal{{$file->id}}">
                                                                    <i class="bx bx-x"></i> Return
                                                                </button>
                                                                <!-- Modal for rejection reason -->
                                                                <div class="modal fade" id="rejectFileModal{{$file->id}}" tabindex="-1">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <form action="{{ route('document-registry.files.reject', $file->id) }}" method="POST">
                                                                                @csrf
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Reject File</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <label for="rejection_reason_{{$file->id}}" class="form-label">Reason <span class="text-danger">*</span></label>
                                                                                    <textarea class="form-control" id="rejection_reason_{{$file->id}}" name="rejection_reason" required></textarea>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                                    <button type="submit" class="btn btn-danger">Return</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                                @if($file->status->name === 'Returned' && $file->rejection_reason)
                                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                                            data-bs-toggle="modal" data-bs-target="#viewFileRejectionModal{{$file->id}}">
                                                                        <i class="bx bx-info-circle"></i> Details
                                                                    </button>
                                                                    <!-- Modal for viewing file rejection reason -->
                                                                    <div class="modal fade" id="viewFileRejectionModal{{$file->id}}" tabindex="-1">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title text-warning">
                                                                                        <i class="bx bx-undo"></i> File Returned
                                                                                    </h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="alert alert-warning">
                                                                                        <h6 class="alert-heading">Return Reason:</h6>
                                                                                        <p class="mb-0">{{ $file->rejection_reason }}</p>
                                                                                    </div>
                                                                                    @if($file->implemented_at && $file->approvedBy)
                                                                                        <div class="text-muted">
                                                                                            <small>
                                                                                                <strong>Returned by:</strong> {{ $file->approvedBy->name }}<br>
                                                                                                <strong>Date:</strong> {{ $file->implemented_at->format('m/d/Y') }}
                                                                                            </small>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            <!-- Existing Preview and Download buttons -->
                                                            <button type="button" class="btn btn-sm btn-outline-primary me-2"
                                                                    onclick="previewDocument({{ $file->id }}, '{{ addslashes($file->mime_type) }}', '{{ addslashes($file->original_filename) }}')">
                                                                <i class="bx bx-show"></i> Preview
                                                            </button>
                                                            <a href="{{ route('document-registry.download', $documentRegistrationEntry) }}?file_id={{ $file->id }}" class="btn btn-sm btn-outline-success">
                                                                <i class="bx bx-download"></i> Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column - Status and Actions -->
                            <div class="col-md-4">
                                <!-- Status Card -->
                                <div class="card mb-3" data-driver="status-card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class='bx bx-info-circle'></i> Status Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Current Status</label><br>
                                            @if($documentRegistrationEntry->status->name === 'Pending')
                                                <span class="badge bg-warning text-dark fs-6">
                                                    <i class='bx bx-time'></i> {{ $documentRegistrationEntry->status->name }}
                                                </span>
                                            @elseif($documentRegistrationEntry->status->name === 'Implemented')
                                                <span class="badge bg-success text-white fs-6">
                                                    <i class='bx bx-check'></i> {{ $documentRegistrationEntry->status->name }}
                                                </span>
                                            @elseif($documentRegistrationEntry->status->name === 'Cancelled')
                                                <span class="badge bg-danger text-white fs-6">
                                                    <i class='bx bx-x'></i> {{ $documentRegistrationEntry->status->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white fs-6">
                                                    <i class='bx bx-x'></i> {{ $documentRegistrationEntry->status->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-muted">Originator Name</label>
                                            <p class="mb-0">{{ $documentRegistrationEntry->submittedBy->name }}</p>
                                            <small class="text-muted">{{ $documentRegistrationEntry->submitted_at->format('m/d/Y') }}</small>
                                            <small class="text-muted">{{ $documentRegistrationEntry->submitted_at->format('g:i A') }}</small>

                                        </div>

                                        @if($documentRegistrationEntry->implemented_by)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">
                                                    @if($documentRegistrationEntry->status->name === 'Implemented')
                                                        Implemented By
                                                    @else
                                                        Cancelled By
                                                    @endif
                                                </label>
                                                <p class="mb-0">{{ $documentRegistrationEntry->approvedBy->name }}</p>
                                                <small class="text-muted">{{ $documentRegistrationEntry->implemented_at->format('m/d/Y') }}</small>
                                                <small class="text-muted">{{ $documentRegistrationEntry->implemented_at->format('g:i A') }}</small>

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

                                        @if($documentRegistrationEntry->status->name === 'Pending' && auth()->user()->can('reject document registration'))
                                            <div class="d-flex justify-content-end mt-3">
                                                <button type="button"
                                                        class="btn btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal">
                                                    <i class="bx bx-x"></i> Cancel Registration
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($documentRegistrationEntry->status->name === 'Pending' && auth()->user()->can('submit document for approval'))
                                    <div class="card mb-3" data-driver="upload-card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bx bx-upload"></i> Upload File</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('document-registry.upload-file', $documentRegistrationEntry) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="document_file" class="form-label">Choose File</label>
                                                    <input type="file" class="form-control" id="document_file" name="document_file"
                                                           accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.csv" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bx bx-upload"></i> Upload File
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif

                                @if($documentRegistrationEntry->status->name !== 'Pending')
                                    <div class="card mb-3" data-driver="upload-disabled">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bx bx-block"></i> Upload Disabled</h5>
                                        </div>
                                        <div class="card-body">
                                            @if($documentRegistrationEntry->status->name === 'Implemented')
                                                <div class="alert alert-success mb-0">
                                                    <i class="bx bx-check"></i>
                                                    This document has been implemented. You cannot upload new files.
                                                </div>
                                            @elseif($documentRegistrationEntry->status->name === 'Cancelled')
                                                <div class="alert alert-danger mb-0">
                                                    <i class="bx bx-x"></i>
                                                    This document registration has been cancelled. You cannot upload new files.
                                                </div>
                                            @endif
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
        @if($file)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card" id="preview-card" style="display: none;" data-driver="preview-section">
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

        <!-- Referenced Documents Section -->
        @if($documentRegistrationEntry->status->name === 'Implemented' && $documentRegistrationEntry->documents->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card" data-driver="referenced-docs">
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
                                                    {{ $document->created_at->format('m/d/Y g:ia') }}
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

    <!-- View Cancellation Reason Modal -->
    <div class="modal fade" id="viewCancelledModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class='bx bx-x-circle'></i> Registration Cancelled
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Cancellation Reason:</h6>
                        <p class="mb-0">{{ $documentRegistrationEntry->rejection_reason }}</p>
                    </div>
                    @if($documentRegistrationEntry->implemented_at && $documentRegistrationEntry->approvedBy)
                        <div class="text-muted">
                            <small>
                                <strong>Cancelled by:</strong> {{ $documentRegistrationEntry->approvedBy->name }}<br>
                                <strong>Date:</strong> {{ $documentRegistrationEntry->implemented_at->format('m/d/Y') }}
                            </small>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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
    <!-- JavaScript remains the same -->
    <script>
        $(document).ready(function() {
            $('#fileTable').DataTable({
                responsive: true,
                order: [[1, 'desc']],
                pageLength: 10,
                language: {
                  search: "Search files:",
                  lengthMenu: "Show _MENU_ files per page",
                  info: "Showing _START_ to _END_ of _TOTAL_ files"
                }
            });
        });

        function previewDocument(fileId, mimeType, fileName) {
            const previewCard = document.getElementById('preview-card');
            const previewContent = document.getElementById('document-preview');
            previewCard.style.display = 'block';
            previewCard.scrollIntoView({ behavior: 'smooth' });

            const entryId = '{{ $documentRegistrationEntry->id }}';
            const previewUrl = '{{ route("document-registry.preview", $documentRegistrationEntry) }}' + '?file_id=' + fileId;
            const previewApiUrl = '{{ route("document-registry.preview-api", $documentRegistrationEntry) }}' + '?file_id=' + fileId;
            const downloadUrl = '{{ route("document-registry.download", $documentRegistrationEntry) }}' + '?file_id=' + fileId;

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
    </script>
@endpush


@push('driverjs')
<script>
window.addEventListener('start-driverjs-tour', function() {
    const driver = window.driver.js.driver;
    driver({
        showProgress: true,
        steps: [
            {
                element: '[data-driver="breadcrumb"]',
                popover: {
                    title: 'Navigation Breadcrumb',
                    description: 'This shows your current location in the system. You can click on "My Registrations" to go back to the main list.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '[data-driver="card-header"]',
                popover: {
                    title: 'Document Header',
                    description: 'This displays the document registration title and provides a back button to return to the registry list.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '[data-driver="document-info"]',
                popover: {
                    title: 'Document Information',
                    description: 'Here you can view all the details of the document registration including document number, revision, originator, and other metadata.',
                    side: 'top',
                    align: 'start'
                }
            },
            @if($documentRegistrationEntry->remarks)
            {
                element: '[data-driver="remarks"]',
                popover: {
                    title: 'Document Remarks',
                    description: 'Any additional notes or comments about this document registration are displayed here.',
                    side: 'top',
                    align: 'start'
                }
            },
            @endif
            @if($documentRegistrationEntry->files->count())
            {
                element: '[data-driver="file-table"]',
                popover: {
                    title: 'Attached Files',
                    description: 'This table shows all files attached to this registration. You can preview, download, and see the status of each file. Authorized users can approve or return files.',
                    side: 'top',
                    align: 'start'
                }
            },
            @endif
            {
                element: '[data-driver="status-card"]',
                popover: {
                    title: 'Status Information',
                    description: 'This card displays the current status of the registration, who submitted it, when it was submitted, and any approval/rejection information.',
                    side: 'left',
                    align: 'start'
                }
            },
            @if($documentRegistrationEntry->status->name === 'Pending' && auth()->user()->can('submit document for approval'))
            {
                element: '[data-driver="upload-card"]',
                popover: {
                    title: 'File Upload',
                    description: 'While the registration is pending, you can upload additional files here. Only certain file types are accepted.',
                    side: 'left',
                    align: 'start'
                }
            },
            @endif
            @if($documentRegistrationEntry->status->name !== 'Pending')
            {
                element: '[data-driver="upload-disabled"]',
                popover: {
                    title: 'Upload Status',
                    description: 'File uploads are disabled because this registration has been {{ $documentRegistrationEntry->status->name }}. No further files can be added.',
                    side: 'left',
                    align: 'start'
                }
            },
            @endif
            @if($file)
            {
                element: '[data-driver="preview-section"]',
                popover: {
                    title: 'Document Preview',
                    description: 'When you click the preview button on any file, the document preview will appear here. You can view PDFs, images, and text files directly in the browser.',
                    side: 'top',
                    align: 'start'
                }
            },
            @endif
            @if($documentRegistrationEntry->status->name === 'Implemented' && $documentRegistrationEntry->documents->count() > 0)
            {
                element: '[data-driver="referenced-docs"]',
                popover: {
                    title: 'Referenced Documents',
                    description: 'This section shows any documents that reference this registration. These appear after the registration is implemented.',
                    side: 'top',
                    align: 'start'
                }
            }
            @endif
        ]
    }).drive();
});
</script>
@endpush
