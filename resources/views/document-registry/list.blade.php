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
                            <h3 class="card-title mb-0"><i class='bx bx-folder-open'></i>Registration Tracking Records</h3>
                        </div>
                        <div class="d-flex align-items-center">
                            {{-- Visible export button that uses the same export route as the DataTable button --}}
                            <button id="exportVisibleBtn" type="button" class="btn btn-success btn-sm me-2">
                                <i class="bx bx-download"></i> Export Excel
                            </button>
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
                                            <button type="submit" id="applyFiltersBtn" class="btn btn-primary">
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
                            {!! $dataTable->table(['class' => 'table table-striped table-hover'], true) !!}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

{!! $dataTable->scripts() !!}

<script>
(() => {
    const pendingCount = {{ $pendingCount }};
    const approvedCount = {{ $approvedCount }};
    const rejectedCount = {{ $rejectedCount }};

    // Export route used by the visible export button
    const visibleExportUrl = "{{ route('document-excel.export') }}";

    function getFormQueryString() {
        const form = document.getElementById('filterForm');
        if (!form) return '';
        const fd = new FormData(form);
        // Ensure checkbox unchecked state doesn't get omitted when unchecked:
        // FormData only includes checked checkboxes; but our advancedToggle is a checkbox with value '1'.
        // We'll explicitly add advanced flag value based on the checkbox state.
        const advancedEl = form.querySelector('#advancedToggle');
        if (advancedEl) {
            fd.set('advanced', advancedEl.checked ? '1' : '');
        }
        const params = new URLSearchParams();
        for (const [k, v] of fd.entries()) {
            // skip empty values so URLs remain compact
            if (v === null || String(v).trim() === '') continue;
            params.append(k, v);
        }
        return params.toString();
    }

    $('#exportVisibleBtn').on('click', function (e) {
        e.preventDefault();
        let url = visibleExportUrl;
        const qs = getFormQueryString();
        if (qs) {
            url += (url.indexOf('?') === -1 ? '?' : '&') + qs;
        }
        // navigate to export URL (triggers Excel download)
        window.location.href = url;
    });

    function getRegistryTable() {
        if (window.LaravelDataTables && window.LaravelDataTables['documentRegistry']) {
            return window.LaravelDataTables['documentRegistry'];
        }
        if ($.fn.DataTable.isDataTable('#documentRegistry')) {
            return $('#documentRegistry').DataTable();
        }
        return null;
    }

    function reloadRegistryTable() {
        const dt = getRegistryTable();
        if (dt?.ajax?.reload) {
            dt.ajax.reload(null, false);
        } else if (dt?.draw) {
            dt.draw();
        } else {
            document.getElementById('filterForm').submit();
        }
    }

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        reloadRegistryTable();
    });

    $('#applyFiltersBtn').on('click', function (e) {
        e.preventDefault();
        reloadRegistryTable();
    });

    function syncAdvancedState() {
        const checked = $('#advancedToggle').is(':checked');
        $('#advancedFilters').find('select, input, button').not('#advancedToggle').prop('disabled', !checked);
        if (!checked) {
            $('#advancedFilters').slideUp();
        } else {
            $('#advancedFilters').slideDown();
        }
    }

    $('#advancedToggle').on('change', function () {
        syncAdvancedState();
        const $dtBtn = $('#dtAdvancedToggle').find('button');
        if ($dtBtn.length) {
            const checked = $(this).is(':checked');
            $dtBtn.toggleClass('btn-active', checked)
                 .toggleClass('btn-inactive', !checked)
                 .attr('aria-pressed', checked ? 'true' : 'false');
        }
        reloadRegistryTable();
    });

    syncAdvancedState();

    function attachProcessingFix() {
        const $table = $('#documentRegistry');
        const $wrapper = $table.closest('.dataTables_wrapper');
        $table.off('.processingFix');

        $table.on('processing.dt.processingFix', (_, __, processing) => {
            const $proc = $wrapper.find('.dataTables_processing');
            processing ? $proc.show() : $proc.hide();
        });

        $table.on('xhr.dt.processingFix', () => {
            const $proc = $wrapper.find('.dataTables_processing');
            $proc.hide().css('display', '');
            if (window.__dtProcessingTimeout) {
                clearTimeout(window.__dtProcessingTimeout);
                window.__dtProcessingTimeout = null;
            }
        });

        $table.on('preXhr.dt.processingFix', () => {
            if (window.__dtProcessingTimeout) clearTimeout(window.__dtProcessingTimeout);
            window.__dtProcessingTimeout = setTimeout(() => {
                const $proc = $wrapper.find('.dataTables_processing');
                $proc.hide();
                window.__dtProcessingTimeout = null;
            }, 10000);
        });
    }

    function enhanceToolbar(dtInstance) {
        const $wrapper = $('#documentRegistry').closest('.dataTables_wrapper');
        if (!$wrapper.length) return;

        if (!$wrapper.find('#dtAdvancedToggle').length) {
            const checked = $('#advancedToggle').is(':checked');
            const btnClass = checked ? 'btn-active' : 'btn-inactive';
            const toggleHtml = `
                <div id="dtAdvancedToggle" style="display:inline-block;margin-left:12px;vertical-align:middle;">
                    <button type="button" id="dtAdvancedVisibleToggle" class="btn btn-sm ${btnClass}" aria-pressed="${checked ? 'true' : 'false'}">
                        <i class="bx bx-filter"></i>
                        <span class="d-none d-sm-inline">Advanced</span>
                    </button>
                </div>
            `;
            $wrapper.find('.dataTables_filter').append(toggleHtml);
            $('#dtAdvancedVisibleToggle').on('click', function () {
                const nextState = !$('#advancedToggle').is(':checked');
                $('#advancedToggle').prop('checked', nextState).trigger('change');
            });
        }

        if (!$wrapper.find('#statusToolbarCounts').length) {
            const badgeHtml = `
                <div id="statusToolbarCounts" aria-hidden="true">
                    <span class="badge bg-warning text-dark">${pendingCount} Pending</span>
                    <span class="badge bg-success">${approvedCount} Implemented</span>
                    <span class="badge bg-danger">${rejectedCount} Cancelled</span>
                </div>
            `;
            const $buttons = $wrapper.find('.dt-buttons').first();
            $buttons.length ? $buttons.after(badgeHtml) : $wrapper.find('.dataTables_length').after(badgeHtml);
        }
    }

    function onTableReady(callback) {
        const interval = setInterval(() => {
            const dt = getRegistryTable();
            if (dt) {
                clearInterval(interval);
                callback(dt);
            }
        }, 100);
    }

    onTableReady((dt) => {
        attachProcessingFix();
        enhanceToolbar(dt);
    });
})();
</script>
@endpush
