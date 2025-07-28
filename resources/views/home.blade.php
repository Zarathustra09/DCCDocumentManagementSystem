@extends('layouts.app')

@section('content')
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class='bx bx-home'></i> Dashboard</h3>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <h4>Welcome back, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted">{{ __('You are logged in and ready to manage your documents.') }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group" aria-label="Document Actions">
                                <a href="{{ route('document-registry.create') }}" class="btn btn-primary">
                                    <i class='bx bx-file-find'></i> Create Registration
                                </a>
                                <a href="{{ route('document-registry.index') }}" class="btn btn-outline-primary">
                                    <i class='bx bx-file-find'></i> View My Registrations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($canApprove && $pendingRegistrations->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class='bx bx-time-five text-warning'></i>
                            Pending Document Registrations
                            <span class="badge bg-warning text-dark">{{ $pendingRegistrations->count() }}</span>
                        </h4>
                        <a href="{{ route('document-registry.index', ['status' => 'pending']) }}" class="btn btn-outline-primary btn-sm">
                            <i class='bx bx-show'></i> View All Pending
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Document Details</th>
                                        <th>Originator</th>
                                        <th>Submitted</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRegistrations as $entry)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $entry->document_title }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $entry->document_no }} Rev. {{ $entry->revision_no }}
                                                    </small>
                                                    @if($entry->customer)
                                                        <br>
                                                        <small class="text-info">Customer: {{ $entry->customer }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $entry->originator_name }}
                                                    @if($entry->device_name)
                                                        <br>
                                                        <small class="text-muted">{{ $entry->device_name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $entry->submittedBy->name }}
                                                    <br>
                                                    <small class="text-muted">{{ $entry->submitted_at->format('M d, Y') }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $entry->submitted_at->format('g:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <i class='bx bx-time'></i> {{ $entry->status_name }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="bx bx-cog"></i> Manage
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('document-registry.show', $entry) }}">
                                                            <i class="bx bx-show me-2"></i> View Details
                                                        </a>
{{--                                                        @if($entry->status === 'pending' &&--}}
{{--                                                            $entry->submitted_by === auth()->id() &&--}}
{{--                                                            auth()->user()->can('edit document registration details'))--}}
{{--                                                            <a class="dropdown-item" href="{{ route('document-registry.edit', $entry) }}">--}}
{{--                                                                <i class="bx bx-edit-alt me-2"></i> Edit--}}
{{--                                                            </a>--}}
{{--                                                        @endif--}}
{{--                                                        @if($entry->status === 'pending' &&--}}
{{--                                                            $entry->submitted_by === auth()->id() &&--}}
{{--                                                            auth()->user()->can('withdraw document submission'))--}}
{{--                                                            <form action="{{ route('document-registry.withdraw', $entry) }}"--}}
{{--                                                                  method="POST" onsubmit="return confirm('Are you sure you want to withdraw this submission?')">--}}
{{--                                                                @csrf--}}
{{--                                                                @method('DELETE')--}}
{{--                                                                <button type="submit" class="dropdown-item text-danger">--}}
{{--                                                                    <i class="bx bx-trash me-2"></i> Withdraw--}}
{{--                                                                </button>--}}
{{--                                                            </form>--}}
{{--                                                        @endif--}}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($pendingRegistrations->count() >= 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('document-registry.index', ['status' => 'pending']) }}" class="btn btn-primary">
                                    <i class='bx bx-show'></i> View All Pending Registrations
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif($canApprove)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class='bx bx-check-circle text-success' style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No Pending Registrations</h4>
                        <p class="text-muted">All document registrations have been processed.</p>
{{--                        <a href="{{ route('document-registry.index') }}" class="btn btn-primary">--}}
{{--                            <i class='bx bx-file-find'></i> View My Registrations--}}
{{--                        </a>--}}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Quick Reject Modal -->
<div class="modal fade" id="quickRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Document Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickRejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>You are about to reject: <strong id="rejectDocumentTitle"></strong></p>
                    <div class="form-group">
                        <label for="quick_rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="quick_rejection_reason" name="rejection_reason" rows="4" required
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

<!-- Quick Revision Modal -->
<div class="modal fade" id="quickRevisionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Require Revision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickRevisionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>You are requesting revision for: <strong id="revisionDocumentTitle"></strong></p>
                    <div class="form-group">
                        <label for="quick_revision_notes" class="form-label">Revision Notes <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="quick_revision_notes" name="revision_notes" rows="4" required
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

<script>
function approveRegistration(entryId) {
    if (confirm('Are you sure you want to approve this document registration?')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/document-registry/${entryId}/approve`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function showRejectModal(entryId, documentTitle) {
    document.getElementById('rejectDocumentTitle').textContent = documentTitle;
    document.getElementById('quickRejectForm').action = `/document-registry/${entryId}/reject`;
    document.getElementById('quick_rejection_reason').value = '';

    const modal = new bootstrap.Modal(document.getElementById('quickRejectModal'));
    modal.show();
}

function showRevisionModal(entryId, documentTitle) {
    document.getElementById('revisionDocumentTitle').textContent = documentTitle;
    document.getElementById('quickRevisionForm').action = `/document-registry/${entryId}/require-revision`;
    document.getElementById('quick_revision_notes').value = '';

    const modal = new bootstrap.Modal(document.getElementById('quickRevisionModal'));
    modal.show();
}
</script>

<style>
.badge {
    font-size: 0.9em;
    padding: 0.5rem 0.75rem;
}
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}
.table td {
    vertical-align: middle;
}
.btn-group .btn {
    margin: 0 1px;
}
</style>
@endsection
