@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-folder-open'></i> Document Registry</h3>
                    @can('submit document for approval')
                        <a href="{{ route('document-registry.create') }}" class="btn btn-primary">
                            <i class='bx bx-plus'></i> Register New Document
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                    <!-- Entries Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="documentRegistry">
                            <thead>
                                <tr>
                                    <th>Document Title</th>
                                    <th>Document No.</th>
                                    <th>Rev.</th>
                                    <th>Originator</th>
                                    <th>Customer</th>
                                    <th>Submitted</th>
                                    <th>Implementation Info</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                    <tr>
                                        <td>
                                            <strong>{{ $entry->document_title ?? '-' }}</strong>
                                            @if($entry->device_name)
                                                <br><small class="text-muted">{{ $entry->device_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $entry->document_no ?? '-' }}</td>
                                        <td>{{ $entry->revision_no ?? '-' }}</td>
                                        <td>{{ $entry->originator_name ?? '-' }}</td>
                                        <td>{{ $entry->customer ?? '-' }}</td>

                                        <td>
                                            <small>
                                                <i class='bx bx-user'></i> {{ $entry->submittedBy?->name ?? '-' }}<br>
                                                <i class='bx bx-calendar'></i> {{ $entry->submitted_at?->format('M d, Y') ?? '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <i class='bx bx-user'></i> {{ $entry->approvedBy?->name ?? '-' }}<br>
                                                <i class='bx bx-calendar'></i> {{ $entry->implemented_at?->format('M d, Y') ?? '-' }}
                                            </small>
                                        </td>

                                        <td>
                                            @if($entry->status === 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class='bx bx-time'></i> {{ $entry->status_name ?? 'Pending' }}
                                                </span>
                                            @elseif($entry->status === 'approved')
                                                <span class="badge bg-success text-white">
                                                    <i class='bx bx-check'></i> {{ $entry->status_name ?? 'Approved' }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white">
                                                    <i class='bx bx-x'></i> {{ $entry->status_name ?? 'Rejected' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="bx bx-cog"></i> Manage
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('document-registry.show', $entry) }}">
                                                        <i class="bx bx-show me-2"></i> View Details
                                                    </a>
                                                    @if($entry->status === 'pending' &&
                                                        $entry->submitted_by === auth()->id() &&
                                                        auth()->user()->can('edit document registration details'))
                                                        <a class="dropdown-item" href="{{ route('document-registry.edit', $entry) }}">
                                                            <i class="bx bx-edit-alt me-2"></i> Edit
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class='bx bx-info-circle'></i> No document registrations found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($entries->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $entries->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.badge {
    font-size: 0.85em;
    padding: 0.375rem 0.75rem;
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
</style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#documentRegistry').DataTable({
                responsive: true,
                order: [[5, 'desc']],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [0, 6] }
                ],
                language: {
                    search: "Search users:",
                    lengthMenu: "Show _MENU_ users per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ users"
                }
            });
        });
    </script>
@endpush
