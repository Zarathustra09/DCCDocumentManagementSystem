@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h3 class="card-title mb-0"><i class='bx bx-folder-open'></i>Tracking of Registered Documents</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Advanced Filters -->
                        <form id="filterForm" method="GET" action="{{ route('document-registry.list') }}" class="mb-4">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <!-- authoritative checkbox is kept for form submission but hidden;
                                         a visible toggle is injected into the DataTables search bar -->
                                    <div class="form-check form-switch d-none" id="advancedToggleContainer">
                                        <input class="form-check-input" type="checkbox" id="advancedToggle" name="advanced" value="1" {{ request('advanced') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="advancedToggle">
                                            <i class="bx bx-filter"></i> Show Advanced Filters
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="advancedFilters" style="{{ request('advanced') ? '' : 'display:none;' }}">
                                <div class="row">
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="category_id">Category</label>
                                            <select name="category_id" id="category_id" class="form-select">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
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

                                <!-- Apply / Clear placed inside advancedFilters so they hide with the rest -->
                                <div class="row mt-2">
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
                                        <th>Device Name / Part Number</th>
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
                                                @if($entry->category)
{{--                                                    <span class="badge bg-info">{{ $entry->category->code }}</span>--}}
{{--                                                    <br>--}}
                                                    <small>{{ $entry->category->name }}</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                {{ $entry->device_name ?? '-'}}
                                            </td>
                                            <td>{{ $entry->document_no ?? '-'}}</td>
                                            <td>{{ $entry->revision_no ?? '-'}}</td>
                                            <td>{{ $entry->originator_name ?? '-'}}</td>
                                            <td>{{ $entry->customer->name ?? '-' }}</td>
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
                                                        @if(
                                                                    $entry->submitted_by === auth()->id() ||
                                                                    auth()->user()->can('edit document registration details')
                                                                )
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
    <!-- / Content -->
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

/* DT advanced toggle: pill-style button to match Bootstrap look */
#dtAdvancedToggle .btn {
    padding: 0.275rem 0.55rem;
    font-size: 0.82rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
#dtAdvancedToggle .btn .bx {
    font-size: 0.95rem;
    line-height: 1;
}
#dtAdvancedToggle .btn-active {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.1rem rgba(13,110,253,0.12);
}
#dtAdvancedToggle .btn-inactive {
    color: #495057;
    background-color: transparent;
    border: 1px solid rgba(0,0,0,0.08);
}

/* compact toolbar badges beside Export button */
#statusToolbarCounts .badge {
    font-size: 0.78rem;
    padding: 0.22rem 0.45rem;
    line-height: 1;
}
/* keep small gap and vertical alignment with dt buttons */
#statusToolbarCounts {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-left: 10px;
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Expose status counts for toolbar badges (evaluated server-side)
            const pendingCount = {{ $entries->where('status.name', 'Pending')->count() }};
            const approvedCount = {{ $entries->where('status.name', 'Implemented')->count() }};
            const rejectedCount = {{ $entries->where('status.name', 'Cancelled')->count() }};

            // Advanced filters: sync UI and disable inputs/buttons when advanced is off
            const advancedChecked = $('#advancedToggle').is(':checked');
            if (!advancedChecked) {
                // disable selects/inputs/buttons inside advancedFilters to avoid submission
                $('#advancedFilters').find('select, input, button').prop('disabled', true);
            }
            // authoritative checkbox change handler toggles visibility & enabled state
            $('#advancedToggle').on('change', function() {
                const show = $(this).is(':checked');
                if (show) {
                    $('#advancedFilters').slideDown();
                } else {
                    $('#advancedFilters').slideUp();
                }
                $('#advancedFilters').find('select, input, button').prop('disabled', !show);

                // sync visible DT toggle button (if present)
                const $dtBtn = $('#dtAdvancedToggle').find('button');
                if ($dtBtn.length) {
                    if (show) {
                        $dtBtn.removeClass('btn-inactive').addClass('btn-active');
                        $dtBtn.attr('aria-pressed', 'true');
                    } else {
                        $dtBtn.removeClass('btn-active').addClass('btn-inactive');
                        $dtBtn.attr('aria-pressed', 'false');
                    }
                }
            });

            $('#documentRegistry').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        text: '<i class="bx bx-download"></i> Export to Excel',
                        className: 'btn btn-success btn-sm dt-export-btn',
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
                order: [],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [10] }
                ],
                language: {
                    search: "Search entries:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries"
                }
                ,
                initComplete: function() {
                    // inject a compact, pill-style button to the right of the DataTables search input
                    const isChecked = $('#advancedToggle').is(':checked');
                    const btnClass = isChecked ? 'btn-active' : 'btn-inactive';
                    const toggleHtml = `
                        <div id="dtAdvancedToggle" style="display:inline-block; margin-left:12px; vertical-align:middle;">
                            <button type="button" id="dtAdvancedVisibleToggle" class="btn btn-sm ${btnClass}" aria-pressed="${isChecked ? 'true' : 'false'}" title="Toggle advanced filters">
                                <i class="bx bx-filter"></i>
                                <span class="d-none d-sm-inline">Advanced</span>
                            </button>
                        </div>
                    `;

                    // append next to the search input (on the right)
                    $(this.api().table().container()).find('.dataTables_filter').append(toggleHtml);

                    // when visible toggle clicked, toggle authoritative checkbox and trigger its change
                    $('#dtAdvancedVisibleToggle').on('click', function() {
                        const currently = $('#advancedToggle').is(':checked');
                        $('#advancedToggle').prop('checked', !currently).trigger('change');
                        // visual toggle handled by authoritative checkbox change handler (keeps in sync)
                    });

                    // append status counts next to Export button
                    const badgeHtml = `
                        <div id="statusToolbarCounts" aria-hidden="true">
                            <span class="badge bg-warning text-dark">${pendingCount} Pending</span>
                            <span class="badge bg-success">${approvedCount} Implemented</span>
                            <span class="badge bg-danger">${rejectedCount} Cancelled</span>
                        </div>
                    `;
                    const $container = $(this.api().table().container());
                    const $exportBtn = $container.find('.dt-export-btn').first();
                    if ($exportBtn.length) {
                        $exportBtn.after(badgeHtml);
                    } else {
                        // fallback: append to dt-buttons group
                        $container.find('.dt-buttons').append(badgeHtml);
                    }
                }
            });
        });
    </script>
@endpush
