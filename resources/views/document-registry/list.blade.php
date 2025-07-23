@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-folder-open'></i> Document Registry - Advanced List</h3>
                    @can('submit document for approval')
                        <a href="{{ route('document-registry.create') }}" class="btn btn-primary">
                            <i class='bx bx-plus'></i> Register New Document
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Advanced Filters -->
                    <form method="GET" action="{{ route('document-registry.list') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="search">Search</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                           value="{{ request('search') }}"
                                           placeholder="Document number, title, originator...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="customer">Customer</label>
                                    <input type="text" name="customer" id="customer" class="form-control"
                                           value="{{ request('customer') }}"
                                           placeholder="Customer name...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="device_name">Device Name</label>
                                    <input type="text" name="device_name" id="device_name" class="form-control"
                                           value="{{ request('device_name') }}"
                                           placeholder="Device name...">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="submitted_by">Submitted By</label>
                                    <select name="submitted_by" id="submitted_by" class="form-select">
                                        <option value="">All Submitters</option>
                                        @foreach($submitters as $submitter)
                                            <option value="{{ $submitter->id }}" {{ request('submitted_by') == $submitter->id ? 'selected' : '' }}>
                                                {{ $submitter->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="date_from">Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control"
                                           value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="date_to">Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control"
                                           value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="has_file">File Attachment</label>
                                    <select name="has_file" id="has_file" class="form-select">
                                        <option value="">All Entries</option>
                                        <option value="yes" {{ request('has_file') === 'yes' ? 'selected' : '' }}>With File</option>
                                        <option value="no" {{ request('has_file') === 'no' ? 'selected' : '' }}>No File</option>
                                    </select>
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
                                Showing {{ $entries->firstItem() ?? 0 }} to {{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }} entries
                                @if(request()->hasAny(['status', 'search', 'customer', 'device_name', 'submitted_by', 'date_from', 'date_to', 'has_file']))
                                    (filtered)
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <span class="badge bg-warning">{{ $pendingCount }} Pending</span>
                                <span class="badge bg-success">{{ $approvedCount }} Approved</span>
                                <span class="badge bg-danger">{{ $rejectedCount }} Rejected</span>
                            </div>
                        </div>
                    </div>
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
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                    <tr>
                                        <td>
                                            <strong>{{ $entry->document_title }}</strong>
                                            @if($entry->device_name)
                                                <br><small class="text-muted">{{ $entry->device_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $entry->document_no }}</td>
                                        <td>{{ $entry->revision_no }}</td>
                                        <td>{{ $entry->originator_name }}</td>
                                        <td>{{ $entry->customer ?? '-' }}</td>
                                        <td>
                                            @if($entry->status === 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class='bx bx-time'></i> {{ $entry->status_name }}
                                                </span>
                                            @elseif($entry->status === 'approved')
                                                <span class="badge bg-success text-white">
                                                    <i class='bx bx-check'></i> {{ $entry->status_name }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white">
                                                    <i class='bx bx-x'></i> {{ $entry->status_name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                <i class='bx bx-user'></i> {{ $entry->submittedBy->name }}<br>
                                                <i class='bx bx-calendar'></i> {{ $entry->submitted_at->format('M d, Y') }}
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
                                                    @if($entry->status === 'pending' &&
                                                        $entry->submitted_by === auth()->id() &&
                                                        auth()->user()->can('edit document registration details'))
                                                        <a class="dropdown-item" href="{{ route('document-registry.edit', $entry) }}">
                                                            <i class="bx bx-edit-alt me-2"></i> Edit
                                                        </a>
                                                    @endif
                                                    @if($entry->status === 'pending' &&
                                                        $entry->submitted_by === auth()->id() &&
                                                        auth()->user()->can('withdraw document submission'))
                                                        <form action="{{ route('document-registry.withdraw', $entry) }}"
                                                              method="POST" onsubmit="return confirm('Are you sure you want to withdraw this submission?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bx bx-trash me-2"></i> Withdraw
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class='bx bx-info-circle'></i> No document registrations found.
                                            @if(request()->hasAny(['status', 'search', 'customer', 'device_name', 'submitted_by', 'date_from', 'date_to', 'has_file']))
                                                <br><small class="text-muted">Try adjusting your filters.</small>
                                            @endif
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
