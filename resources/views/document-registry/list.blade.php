@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-folder-open'></i> Document Registry Management</h3>
                </div>
                <div class="card-body">
                    <!-- Advanced Filters -->
                    <form method="GET" action="{{ route('document-registry.list') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->name }}" {{ request('status') == $status->name ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="search">Search</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                           value="{{ request('search') }}"
                                           placeholder="Document number, title, originator...">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="submitted_by">Submitted By</label>
                                    <select name="submitted_by" id="submitted_by" class="form-select">
                                        <option value="">All Originators</option>
                                        @foreach($submitters as $submitter)
                                            <option value="{{ $submitter->id }}" {{ request('submitted_by') == $submitter->id ? 'selected' : '' }}>
                                                {{ $submitter->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="date_from">Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control"
                                           value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="date_to">Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control"
                                           value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class='bx bx-search'></i> Apply Filters
                                    </button>
                                    <a href="{{ route('document-registry.list') }}" class="btn btn-secondary">
                                        <i class='bx bx-x'></i> Clear Filters
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Results Summary -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <p class="text-muted mb-0">
                                <i class='bx bx-info-circle'></i>
                                Showing {{ $entries->count() }} entries
                                @if(request()->hasAny(['status', 'search', 'submitted_by', 'date_from', 'date_to']))
                                    (filtered)
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <span class="badge bg-warning text-dark">{{ $pendingCount }} Pending</span>
                                <span class="badge bg-success">{{ $approvedCount }} Implemented</span>
                                <span class="badge bg-danger">{{ $rejectedCount }} Cancelled</span>
                            </div>
                        </div>
                    </div>

                    <!-- Entries Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="documentRegistry">
                            <thead>
                                <tr>
                                    <th>Control No.</th>
                                    <th>Document Title</th>
                                    <th>Device Part Number</th>
                                    <th>Document No.</th>
                                    <th>Rev.</th>
                                    <th>Originator</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                    <tr>
                                        <td>
                                            <strong>{{$entry->control_no ?? '-'}}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $entry->document_title ?? '-'}}</strong>
                                        </td>
                                        <td>
                                            {{ $entry->device_name ?? '-'}}
                                        </td>
                                        <td>{{ $entry->document_no ?? '-'}}</td>
                                        <td>{{ $entry->revision_no ?? '-'}}</td>
                                        <td>{{ $entry->originator_name ?? '-'}}</td>
                                        <td>{{ $entry->customer ?? '-' }}</td>
                                        <td>
                                            @if($entry->status->name === 'Pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class='bx bx-time'></i> {{ $entry->status->name }}
                                                </span>
                                            @elseif($entry->status->name === 'Implemented')
                                                <span class="badge bg-success text-white">
                                                    <i class='bx bx-check'></i> {{ $entry->status->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white">
                                                    <i class='bx bx-x'></i> {{ $entry->status->name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                <i class='bx bx-calendar'></i> {{ $entry->submitted_at->format('m/d/Y') }}
                                                <br>
                                                <small class="text-muted">{{ $entry->submitted_at->format('g:i A') }}</small>
                                            </small>
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
                                                    @if($entry->status->name === 'Pending' &&
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
                                            @if(request()->hasAny(['status', 'search', 'submitted_by', 'date_from', 'date_to']))
                                                <br><small class="text-muted">Try adjusting your filters.</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
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
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#documentRegistry').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        text: '<i class="bx bx-download"></i> Export to Excel',
                        className: 'btn btn-success btn-sm',
                        action: function(e, dt, node, config) {
                            const form = document.querySelector('form[action="{{ route('document-registry.list') }}"]');
                            const formData = new FormData(form);
                            const params = new URLSearchParams(formData);
                            const exportUrl = '{{ route("document-excel.export") }}?' + params.toString();
                            window.location.href = exportUrl;
                        }
                    }
                ],
                responsive: true,
                order: [[7, 'desc']],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [0, 8] }
                ],
                language: {
                    search: "Search entries:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries"
                }
            });
        });
    </script>
@endpush
