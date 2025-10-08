@php($showHelpTour = true)
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-folder-open'></i> My Registrations</h3>
                    @can('submit document for approval')
                        <a href="{{ route('document-registry.create') }}" class="btn btn-primary" id="create-registration-btn">
                            <i class='bx bx-plus'></i> Register New Document
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('document-registry.index') }}" class="mb-4" id="registry-filter-form">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-select" id="filter-status">
                                    <option value="">All Statuses</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Implemented" {{ request('status') == 'Implemented' ? 'selected' : '' }}>Implemented</option>
                                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="category_id" class="form-select" id="filter-category">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary" id="filter-btn">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Entries Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="documentRegistry">
                            <thead>
                                <tr>
                                    <th>Control No.</th>
                                    <th>Document Title</th>
                                    <th>Category</th>
                                    <th>Device Part Number</th>
                                    <th>Document No.</th>
                                    <th>Rev.</th>
                                    <th>Originator</th>
                                    <th>Customer</th>
                                    <th>Submitted At</th>
                                    <th>Implemented By</th>
                                    <th>Status</th>
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
                                            <strong>{{ $entry->document_title ?? '-' }}</strong>
                                        </td>
                                        <td>
                                            @if($entry->category)
{{--                                                <span class="badge bg-info">{{ $entry->category->code }}</span>--}}
{{--                                                <br>--}}
                                                <small>{{ $entry->category->name }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            {{ $entry->device_name ?? '-'}}
                                        </td>
                                        <td>{{ $entry->document_no ?? '-' }}</td>
                                        <td>{{ $entry->revision_no ?? '-' }}</td>
                                        <td>{{ $entry->originator_name ?? '-' }}</td>
                                        <td>{{ $entry->customer->name ?? '-' }}</td>
                                        <td>
                                            <small>
                                                <i class='bx bx-calendar'></i> {{ $entry->submitted_at?->format('m/d/Y') ?? '-' }}
                                                <br>
                                                <small class="text-muted">{{ $entry->submitted_at->format('g:i A') }}</small>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <i class='bx bx-user'></i> {{ $entry->approvedBy?->name ?? '-' }}<br>
                                                <i class='bx bx-calendar'></i> {{ $entry->implemented_at?->format('m/d/Y') ?? '-' }}
                                                <small class="text-muted">{{ $entry->implemented_at?->format('g:i A') }}</small>
                                            </small>
                                        </td>
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
                                        <td colspan="12" class="text-center py-4">
                                            <i class='bx bx-info-circle'></i> No document registrations found.
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

@push('driverjs')
<script>
window.addEventListener('start-driverjs-tour', function() {
    const driver = window.driver.js.driver;
    driver({
        showProgress: true,
        steps: [
            {
                element: '#create-registration-btn',
                popover: {
                    title: 'Register New Document',
                    description: 'Click here to register a new document.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#filter-status',
                popover: {
                    title: 'Status Filter',
                    description: 'Filter registrations by their status.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#filter-category',
                popover: {
                    title: 'Category Filter',
                    description: 'Filter registrations by category.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#filter-btn',
                popover: {
                    title: 'Apply Filters',
                    description: 'Click to apply the selected filters.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#documentRegistry',
                popover: {
                    title: 'Registrations Table',
                    description: 'View and manage all your document registrations here.',
                    side: 'top',
                    align: 'center'
                }
            }
        ]
    }).drive();
});
</script>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#documentRegistry').DataTable({
                responsive: true,
                order: [[8, 'desc']],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [0, 11] }
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
