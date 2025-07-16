@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-file-find'></i> Document Registration Details</h3>
                    <div>
                        @if($documentRegistrationEntry->status === 'pending' &&
                            $documentRegistrationEntry->submitted_by === auth()->id() &&
                            auth()->user()->can('edit document registration details'))
                            <a href="{{ route('document-registry.edit', $documentRegistrationEntry) }}" class="btn btn-warning">
                                <i class='bx bx-edit'></i> Edit
                            </a>
                        @endif
                            <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class='bx bx-arrow-back'></i> Back to Registry
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column - Document Details -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label text-muted">Document Title</label>
                                    <h4>{{ $documentRegistrationEntry->document_title }}</h4>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Document Number</label>
                                    <p class="form-control-static"><strong>{{ $documentRegistrationEntry->document_no }}</strong></p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Revision Number</label>
                                    <p class="form-control-static"><strong>{{ $documentRegistrationEntry->revision_no }}</strong></p>
                                </div>

                                @if($documentRegistrationEntry->device_name)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Device Name</label>
                                        <p class="form-control-static">{{ $documentRegistrationEntry->device_name }}</p>
                                    </div>
                                @endif

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Originator</label>
                                    <p class="form-control-static">{{ $documentRegistrationEntry->originator_name }}</p>
                                </div>

                                @if($documentRegistrationEntry->customer)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-muted">Customer</label>
                                        <p class="form-control-static">{{ $documentRegistrationEntry->customer }}</p>
                                    </div>
                                @endif

                                @if($documentRegistrationEntry->remarks)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-muted">Remarks</label>
                                        <div class="border rounded p-3 bg-light">
                                            {{ $documentRegistrationEntry->remarks }}
                                        </div>
                                    </div>
                                @endif
                            </div>
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

                            <!-- Actions Card -->
                            @if($documentRegistrationEntry->status === 'pending')
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class='bx bx-cog'></i> Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        @can('approve document registration')
                                            <form action="{{ route('document-registry.approve', $documentRegistrationEntry) }}" method="POST" class="mb-2">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100"
                                                        onclick="return confirm('Are you sure you want to approve this document registration?')">
                                                    <i class='bx bx-check'></i> Approve
                                                </button>
                                            </form>
                                        @endcan

                                        @can('reject document registration')
                                            <button type="button" class="btn btn-danger btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                <i class='bx bx-x'></i> Reject
                                            </button>
                                        @endcan

                                        @can('require revision for document')
                                            <button type="button" class="btn btn-warning btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#revisionModal">
                                                <i class='bx bx-edit'></i> Require Revision
                                            </button>
                                        @endcan

                                        @if($documentRegistrationEntry->submitted_by === auth()->id())
                                            @can('withdraw document submission')
                                                <form action="{{ route('document-registry.withdraw', $documentRegistrationEntry) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                                            onclick="return confirm('Are you sure you want to withdraw this submission?')">
                                                        <i class='bx bx-trash'></i> Withdraw
                                                    </button>
                                                </form>
                                            @endcan
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

    @if($documentRegistrationEntry->status === 'approved' && $documentRegistrationEntry->documents->count() > 0)
        <!-- Referenced Documents Section -->
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
</style>
@endsection
