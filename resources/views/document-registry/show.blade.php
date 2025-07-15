@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class='bx bx-file-blank'></i> Document Registration Details
                        </h3>
                        <div>
                            <a href="{{ route('document-registry.index') }}" class="btn btn-secondary">
                                <i class='bx bx-arrow-back'></i> Back to Registry
                            </a>
                            @if($documentRegistrationEntry->status === 'pending' &&
                                $documentRegistrationEntry->submitted_by === auth()->id() &&
                                auth()->user()->can('edit document registration details'))
                                <a href="{{ route('document-registry.edit', $documentRegistrationEntry) }}"
                                   class="btn btn-warning">
                                    <i class='bx bx-edit'></i> Edit
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Document Information -->
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%"><i class='bx bx-file'></i> Document Title</th>
                                            <td>{{ $documentRegistrationEntry->document_title }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class='bx bx-hash'></i> Document Number</th>
                                            <td>{{ $documentRegistrationEntry->full_document_number }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class='bx bx-chip'></i> Device Name</th>
                                            <td>{{ $documentRegistrationEntry->device_name ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class='bx bx-user'></i> Originator</th>
                                            <td>{{ $documentRegistrationEntry->originator_name }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class='bx bx-building'></i> Customer</th>
                                            <td>{{ $documentRegistrationEntry->customer ?: '-' }}</td>
                                        </tr>
                                        @if($documentRegistrationEntry->remarks)
                                            <tr>
                                                <th><i class='bx bx-note'></i> Remarks</th>
                                                <td>{{ $documentRegistrationEntry->remarks }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Status and Actions -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class='bx bx-info-circle'></i> Status Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong>Status:</strong><br>
                                            @if($documentRegistrationEntry->status === 'pending')
                                                <span class="badge badge-warning badge-lg">
                                                <i class='bx bx-time'></i> {{ $documentRegistrationEntry->status_name }}
                                            </span>
                                            @elseif($documentRegistrationEntry->status === 'approved')
                                                <span class="badge badge-success badge-lg">
                                                <i class='bx bx-check'></i> {{ $documentRegistrationEntry->status_name }}
                                            </span>
                                            @else
                                                <span class="badge badge-danger badge-lg">
                                                <i class='bx bx-x'></i> {{ $documentRegistrationEntry->status_name }}
                                            </span>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <strong>Submitted by:</strong><br>
                                            <i class='bx bx-user'></i> {{ $documentRegistrationEntry->submittedBy->name }}<br>
                                            <i class='bx bx-calendar'></i> {{ $documentRegistrationEntry->submitted_at->format('M d, Y g:i A') }}
                                        </div>

                                        @if($documentRegistrationEntry->approved_by)
                                            <div class="mb-3">
                                                <strong>{{ $documentRegistrationEntry->status === 'approved' ? 'Approved' : 'Processed' }} by:</strong><br>
                                                <i class='bx bx-user'></i> {{ $documentRegistrationEntry->approvedBy->name }}<br>
                                                <i class='bx bx-calendar'></i> {{ $documentRegistrationEntry->approved_at->format('M d, Y g:i A') }}
                                            </div>
                                        @endif

                                        @if($documentRegistrationEntry->rejection_reason)
                                            <div class="mb-3">
                                                <strong>Rejection Reason:</strong><br>
                                                <div class="alert alert-danger">
                                                    {{ $documentRegistrationEntry->rejection_reason }}
                                                </div>
                                            </div>
                                        @endif

                                        @if($documentRegistrationEntry->revision_notes)
                                            <div class="mb-3">
                                                <strong>Revision Notes:</strong><br>
                                                <div class="alert alert-warning">
                                                    {{ $documentRegistrationEntry->revision_notes }}
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Action Buttons -->
                                        @if($documentRegistrationEntry->status === 'pending')
                                            <div class="d-grid gap-2">
                                                @can('approve document registration')
                                                    <button type="button" class="btn btn-success" onclick="approveDocument()">
                                                        <i class='bx bx-check'></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-warning" onclick="requireRevision()">
                                                        <i class='bx bx-edit'></i> Require Revision
                                                    </button>
                                                    <button type="button" class="btn btn-danger" onclick="rejectDocument()">
                                                        <i class='bx bx-x'></i> Reject
                                                    </button>
                                                @endcan

                                                @if($documentRegistrationEntry->submitted_by === auth()->id() && auth()->user()->can('withdraw document submission'))
                                                    <button type="button" class="btn btn-outline-danger" onclick="withdrawDocument()">
                                                        <i class='bx bx-trash'></i> Withdraw
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Documents Section -->
                        @if($documentRegistrationEntry->documents->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class='bx bx-folder'></i> Related Documents</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Filename</th>
                                                        <th>Department</th>
                                                        <th>Size</th>
                                                        <th>Uploaded</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($documentRegistrationEntry->documents as $document)
                                                        <tr>
                                                            <td>{{ $document->original_filename }}</td>
                                                            <td>{{ $document->department_name }}</td>
                                                            <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('documents.show', $document) }}"
                                                                   class="btn btn-sm btn-info">
                                                                    <i class='bx bx-show'></i> View
                                                                </a>
                                                                <a href="{{ route('documents.download', $document) }}"
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class='bx bx-download'></i> Download
                                                                </a>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function approveDocument() {
            Swal.fire({
                title: 'Approve Document Registration',
                text: 'Are you sure you want to approve this document registration?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bx bx-check"></i> Yes, Approve',
                cancelButtonText: '<i class="bx bx-x"></i> Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("document-registry.approve", $documentRegistrationEntry) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function rejectDocument() {
            Swal.fire({
                title: 'Reject Document Registration',
                html: `
            <div class="text-left">
                <label for="rejection_reason" class="form-label">Rejection Reason:</label>
                <textarea id="rejection_reason" class="form-control" rows="4"
                          placeholder="Please provide a reason for rejection..."></textarea>
            </div>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bx bx-x"></i> Reject',
                cancelButtonText: '<i class="bx bx-arrow-back"></i> Cancel',
                preConfirm: () => {
                    const reason = document.getElementById('rejection_reason').value.trim();
                    if (!reason) {
                        Swal.showValidationMessage('Please provide a rejection reason');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("document-registry.reject", $documentRegistrationEntry) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'rejection_reason';
                    reasonInput.value = result.value;

                    form.appendChild(csrfToken);
                    form.appendChild(reasonInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function requireRevision() {
            Swal.fire({
                title: 'Require Revision',
                html: `
            <div class="text-left">
                <label for="revision_notes" class="form-label">Revision Notes:</label>
                <textarea id="revision_notes" class="form-control" rows="4"
                          placeholder="Please specify what needs to be revised..."></textarea>
            </div>
        `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bx bx-edit"></i> Require Revision',
                cancelButtonText: '<i class="bx bx-arrow-back"></i> Cancel',
                preConfirm: () => {
                    const notes = document.getElementById('revision_notes').value.trim();
                    if (!notes) {
                        Swal.showValidationMessage('Please provide revision notes');
                        return false;
                    }
                    return notes;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("document-registry.require-revision", $documentRegistrationEntry) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const notesInput = document.createElement('input');
                    notesInput.type = 'hidden';
                    notesInput.name = 'revision_notes';
                    notesInput.value = result.value;

                    form.appendChild(csrfToken);
                    form.appendChild(notesInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function withdrawDocument() {
            Swal.fire({
                title: 'Withdraw Document Registration',
                text: 'Are you sure you want to withdraw this document registration? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bx bx-trash"></i> Yes, Withdraw',
                cancelButtonText: '<i class="bx bx-x"></i> Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("document-registry.withdraw", $documentRegistrationEntry) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

@endsection
