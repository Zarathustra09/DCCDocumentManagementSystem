@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class='bx bx-barcode'></i>Document Control Number (DCN) Management</h3>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-info btn-sm" onclick="showDcnFormatInfo()">
                                <i class='bx bx-info-circle'></i> DCN Format Info
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Advanced Filters -->
                        <form method="GET" action="{{ route('dcn.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="dcn_status">DCN Status</label>
                                        <select name="dcn_status" id="dcn_status" class="form-select">
                                            <option value="">All Entries</option>
                                            <option value="with_dcn" {{ request('dcn_status') == 'with_dcn' ? 'selected' : '' }}>
                                                With DCN Assigned
                                            </option>
                                            <option value="without_dcn" {{ request('dcn_status') == 'without_dcn' ? 'selected' : '' }}>
                                                Without DCN
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="customer_id">Customer</label>
                                        <select name="customer_id" id="customer_id" class="form-select">
                                            <option value="">All Customers</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} ({{ $customer->code }})
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
                                                    {{ $category->name }} ({{ $category->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="search">Search</label>
                                        <input type="text" name="search" id="search" class="form-control"
                                               value="{{ request('search') }}"
                                               placeholder="Document title, number, DCN number...">
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
                                        <a href="{{ route('dcn.index') }}" class="btn btn-secondary">
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
                                    @if(request()->hasAny(['dcn_status', 'customer_id', 'category_id', 'search', 'date_from', 'date_to']))
                                        (filtered)
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @php
                                        $withDcnCount = $entries->whereNotNull('dcn_no')->count();
                                        $withoutDcnCount = $entries->whereNull('dcn_no')->count();
                                    @endphp
                                    <span class="badge bg-success">{{ $withDcnCount }} With DCN</span>
                                    <span class="badge bg-warning text-dark">{{ $withoutDcnCount }} Without DCN</span>
                                </div>
                            </div>
                        </div>

                        <!-- Entries Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="dcnTable">
                                <thead>
                                <tr>
                                    <th>DCN No.</th>
                                    <th>Status</th>
                                    <th>Originator</th>
                                    <th>Registration Date</th>
                                    <th>Effective Date</th>
                                    <th>Document No.</th>
                                    <th>Revision No.</th>
                                    <th>Device Name / Part Number</th>
                                    <th>Title</th>
                                    <th>Customer</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($entries as $entry)
                                    <tr>
                                        <td>
                                            @if($entry->dcn_no)
                                                <span class="badge bg-success">
                    <i class='bx bx-check'></i> {{ $entry->dcn_no }}
                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                    <i class='bx bx-time'></i> Not Assigned
                </span>
                                            @endif
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
                                            {{ $entry->submittedBy?->name ?? '-' }}
                                        </td>
                                        <td>
                                            @if($entry->submitted_at)
                                                <small>
                                                    <i class='bx bx-calendar'></i> {{ $entry->submitted_at->format('m/d/Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $entry->submitted_at->format('g:i A') }}</small>
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($entry->implemented_at)
                                                <small>
                                                    <i class='bx bx-calendar'></i> {{ $entry->implemented_at->format('m/d/Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $entry->implemented_at->format('g:i A') }}</small>
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $entry->document_no ?? '-' }}</td>
                                        <td>{{ $entry->revision_no ?? '-' }}</td>
                                        <td>{{ $entry->device_name ?? '-' }}</td>
                                        <td>
                                            <strong>{{ $entry->document_title ?? '-' }}</strong>
                                        </td>
                                        <td>
                                            @if($entry->customer)
                                                {{ $entry->customer->name }}
                                                <br><small class="text-muted">Code: {{ $entry->customer->code }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="bx bx-cog"></i> Manage
                                                </button>
                                                <div class="dropdown-menu">
                                                    {{--                                                    <a class="dropdown-item" href="{{ route('dcn.show', $entry) }}">--}}
                                                    {{--                                                        <i class="bx bx-show me-2"></i> View Details--}}
                                                    {{--                                                    </a>--}}
                                                    @if(!$entry->dcn_no)
                                                        <button type="button" class="dropdown-item" onclick="openDcnModal({{ $entry->id }})">
                                                            <i class="bx bx-plus me-2"></i> Assign DCN
                                                        </button>
                                                    @else
                                                        <button type="button" class="dropdown-item" onclick="openDcnModal({{ $entry->id }})">
                                                            <i class="bx bx-edit-alt me-2"></i> Update DCN
                                                        </button>
                                                        <button type="button" class="dropdown-item text-danger" onclick="clearDcn({{ $entry->id }})">
                                                            <i class="bx bx-x me-2"></i> Clear DCN
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class='bx bx-info-circle'></i> No document registrations found.
                                            @if(request()->hasAny(['dcn_status', 'customer_id', 'category_id', 'search', 'date_from', 'date_to']))
                                                <br><small class="text-muted">Try adjusting your filters.</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>

                            </table>
                        </div>

                        {{-- Removed Laravel pagination: DataTables handles pagination --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- DCN Update Modal -->
    <div class="modal fade" id="dcnModal" tabindex="-1" aria-labelledby="dcnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dcnModalLabel">
                        <i class='bx bx-barcode'></i> Update DCN Number
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="dcnForm">
                        <input type="hidden" id="entryId" name="entry_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modalCustomer" class="form-label">Customer</label>
                                    <select class="form-select" id="modalCustomer" name="customer_id" disabled>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-code="{{ $customer->code }}">
                                                {{ $customer->name }} ({{ $customer->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <span id="customerStatus" class="text-muted">Customer is set from document registry entry</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modalCategory" class="form-label">Category</label>
                                    <select class="form-select" id="modalCategory" name="category_id" disabled>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" data-code="{{ $category->code }}">
                                                {{ $category->name }} ({{ $category->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <span id="categoryStatus" class="text-muted">Category is set from document registry entry</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Read-Only Details -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Originator</label>
                                <input type="text" class="form-control" id="modalOriginator" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Registration Date</label>
                                <input type="text" class="form-control" id="modalRegistrationDate" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Effective Date</label>
                                <input type="text" class="form-control" id="modalEffectiveDate" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Document No.</label>
                                <input type="text" class="form-control" id="modalDocumentNo" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Revision No.</label>
                                <input type="text" class="form-control" id="modalRevisionNo" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Device Name</label>
                                <input type="text" class="form-control" id="modalDeviceName" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="modalTitle" readonly>
                        </div>
                        <!-- End Additional Details -->

                        <!-- DCN Mode Selection -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="manualDcnMode">
                                <label class="form-check-label" for="manualDcnMode">
                                    <i class='bx bx-edit'></i> Manual DCN Override (bypass customer/category requirements)
                                </label>
                            </div>
                            <small class="text-muted">Enable this to manually enter a complete DCN number without restrictions</small>
                        </div>

                        <!-- Auto-generated DCN Section -->
                        <div id="autoGeneratedSection">
                            <div class="mb-3">
                                <label for="dcnSuffix" class="form-label">DCN Suffix (3-digit number) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dcnSuffix" name="dcn_suffix"
                                       maxlength="3" placeholder="e.g., 001">
                                <div class="form-text">
                                    <span id="suffixStatus" class="text-muted">Enter a unique 3-digit number (001-999)</span>
                                </div>
                                <div class="invalid-feedback" id="suffixError">
                                    Please enter a valid 3-digit number.
                                </div>
                            </div>
                        </div>

                        <!-- Manual DCN Override Section -->
                        <div id="manualDcnSection" style="display: none;">
                            <div class="mb-3">
                                <label for="manualDcnInput" class="form-label">Manual DCN Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="manualDcnInput" name="manual_dcn"
                                       placeholder="e.g., CNA25-ALL-001 or any custom format">
                                <div class="form-text">
                                    <span id="manualDcnStatus" class="text-muted">Enter any DCN format you need</span>
                                </div>
                                <div class="invalid-feedback" id="manualDcnError">
                                    Please enter a DCN number.
                                </div>
                            </div>
                            <div class="alert alert-warning alert-sm">
                                <i class='bx bx-info-circle'></i>
                                <strong>Note:</strong> Manual DCN override allows you to bypass customer and category requirements.
                                Ensure the DCN number follows your organization's naming convention.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">DCN Preview</label>
                            <div class="p-3 bg-light rounded border">
                                <div id="dcnPreview" class="fw-bold text-primary fs-4 text-center">
                                    <i class='bx bx-barcode'></i> Select category, customer, and suffix to preview DCN
                                </div>
                                <div id="dcnStatus" class="mt-2 text-center"></div>
                            </div>
                        </div>

                        <!-- Format Breakdown -->
                        <div class="bg-white border border-info p-3 rounded" id="formatBreakdown">
                            <h6 class="mb-2 text-info"><i class='bx bx-info-circle'></i> DCN Format Structure:</h6>
                            <div class="row text-sm">
                                <div class="col-3 text-center">
                                    <div class="fw-bold text-primary">Category Code</div>
                                    <small class="text-muted">e.g., CNA</small>
                                </div>
                                <div class="col-2 text-center">
                                    <div class="fw-bold text-primary">Year</div>
                                    <small class="text-muted">e.g., 25</small>
                                </div>
                                <div class="col-1 text-center">
                                    <div class="fw-bold text-info">-</div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="fw-bold text-primary">Customer Code</div>
                                    <small class="text-muted">e.g., ALL</small>
                                </div>
                                <div class="col-1 text-center">
                                    <div class="fw-bold text-info">-</div>
                                </div>
                                <div class="col-2 text-center">
                                    <div class="fw-bold text-primary">Suffix</div>
                                    <small class="text-muted">e.g., 001</small>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <strong class="text-info">Result: CNA25-ALL-001</strong>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class='bx bx-x'></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveDcnBtn">
                        <i class='bx bx-check'></i> Update DCN
                    </button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#dcnTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        text: '<i class="bx bx-download"></i> Export to Excel',
                        className: 'btn btn-success btn-sm',
                        action: function(e, dt, node, config) {
                            // Get current query string
                            const query = window.location.search;
                            // Redirect to export route with filters
                            window.location.href = 'dcn/export' + query;
                        }
                    }
                ],
                responsive: true,
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [9] }
                ],
                language: {
                    search: "Search entries:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries"
                }
            });

            // Initialize Bootstrap Modal
            let dcnModal = new bootstrap.Modal(document.getElementById('dcnModal'));
            let currentEntryId = null;

            // Manual DCN mode toggle
            document.getElementById('manualDcnMode').addEventListener('change', function(e) {
                const isManual = e.target.checked;

                if (isManual) {
                    // Show manual section, hide auto-generated section
                    document.getElementById('autoGeneratedSection').style.display = 'none';
                    document.getElementById('manualDcnSection').style.display = 'block';
                    document.getElementById('formatBreakdown').style.display = 'none';

                    // Clear and disable auto-generated fields
                    document.getElementById('dcnSuffix').value = '';
                    document.getElementById('dcnSuffix').disabled = true;

                    // Enable manual input
                    document.getElementById('manualDcnInput').disabled = false;
                    document.getElementById('manualDcnInput').focus();

                    // Update preview
                    updateManualDcnPreview();
                } else {
                    // Show auto-generated section, hide manual section
                    document.getElementById('autoGeneratedSection').style.display = 'block';
                    document.getElementById('manualDcnSection').style.display = 'none';
                    document.getElementById('formatBreakdown').style.display = 'block';

                    // Clear and disable manual field
                    document.getElementById('manualDcnInput').value = '';
                    document.getElementById('manualDcnInput').disabled = true;

                    // Enable auto-generated fields
                    document.getElementById('dcnSuffix').disabled = false;

                    // Update preview
                    updateDcnPreview();
                }
            });

            // Manual DCN input preview
            document.getElementById('manualDcnInput').addEventListener('input', function(e) {
                updateManualDcnPreview();
            });

            function updateManualDcnPreview() {
                const manualDcn = document.getElementById('manualDcnInput').value.trim();

                if (manualDcn) {
                    document.getElementById('dcnPreview').innerHTML = `<i class='bx bx-barcode'></i> ${manualDcn}`;
                    document.getElementById('dcnStatus').innerHTML = '<div class="alert alert-info alert-sm mb-0"><i class="bx bx-info-circle"></i> Manual DCN mode - no format validation</div>';
                    document.getElementById('saveDcnBtn').disabled = false;

                    // Add valid state
                    document.getElementById('manualDcnInput').classList.remove('is-invalid');
                    document.getElementById('manualDcnInput').classList.add('is-valid');
                    document.getElementById('manualDcnStatus').innerHTML = '<i class="bx bx-check-circle text-success"></i> DCN ready to save';
                } else {
                    document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-barcode"></i> Enter DCN number...';
                    document.getElementById('dcnStatus').innerHTML = '';
                    document.getElementById('saveDcnBtn').disabled = true;

                    // Reset validation state
                    document.getElementById('manualDcnInput').classList.remove('is-invalid', 'is-valid');
                    document.getElementById('manualDcnStatus').innerHTML = '<span class="text-muted">Enter any DCN format you need</span>';
                }
            }

            // Preview DCN number as user types - now with manual suffix input
            function updateDcnPreview() {
                const categorySelect = document.getElementById('modalCategory');
                const customerSelect = document.getElementById('modalCustomer');
                const suffixInput = document.getElementById('dcnSuffix');

                const categoryCode = categorySelect.options[categorySelect.selectedIndex]?.dataset.code;
                const customerCode = customerSelect.options[customerSelect.selectedIndex]?.dataset.code;
                const suffix = suffixInput.value;

                // Validate suffix is exactly 3 digits
                const isValidSuffix = /^\d{3}$/.test(suffix);

                if (categoryCode && customerCode && suffix) {
                    if (isValidSuffix) {
                        const currentYear = new Date().getFullYear().toString().slice(-2);
                        const dcnPreview = `${categoryCode}${currentYear}-${customerCode}-${suffix}`;

                        document.getElementById('dcnPreview').innerHTML = `<i class='bx bx-barcode'></i> ${dcnPreview}`;
                        document.getElementById('dcnStatus').innerHTML = '<div class="alert alert-success alert-sm mb-0"><i class="bx bx-check-circle"></i> DCN ready to assign</div>';
                        document.getElementById('saveDcnBtn').disabled = false;

                        // Remove invalid state
                        suffixInput.classList.remove('is-invalid');
                        suffixInput.classList.add('is-valid');
                        document.getElementById('suffixStatus').innerHTML = '<i class="bx bx-check-circle text-success"></i> Valid 3-digit suffix';
                    } else {
                        document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-error-circle"></i> Invalid suffix format';
                        document.getElementById('dcnStatus').innerHTML = '<div class="alert alert-warning alert-sm mb-0"><i class="bx bx-exclamation-triangle"></i> Suffix must be exactly 3 digits (e.g., 001, 025, 999)</div>';
                        document.getElementById('saveDcnBtn').disabled = true;

                        // Show invalid state
                        suffixInput.classList.add('is-invalid');
                        suffixInput.classList.remove('is-valid');
                        document.getElementById('suffixStatus').innerHTML = '<i class="bx bx-error-circle text-danger"></i> Suffix must be exactly 3 digits';
                    }
                } else {
                    document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-barcode"></i> Waiting for complete data...';
                    document.getElementById('dcnStatus').innerHTML = '';
                    document.getElementById('saveDcnBtn').disabled = true;

                    // Reset validation state
                    suffixInput.classList.remove('is-invalid', 'is-valid');
                }
            }

            // Add event listener for suffix input
            document.getElementById('dcnSuffix').addEventListener('input', function(e) {
                // Only allow digits
                this.value = this.value.replace(/\D/g, '');

                // Limit to 3 characters
                if (this.value.length > 3) {
                    this.value = this.value.slice(0, 3);
                }

                updateDcnPreview();
            });

            // Save DCN - updated to handle both modes
            document.getElementById('saveDcnBtn').addEventListener('click', function() {
                const isManualMode = document.getElementById('manualDcnMode').checked;

                let dcnNumber;
                let formData = new FormData();
                formData.append('entry_id', currentEntryId);

                if (isManualMode) {
                    // Manual DCN mode
                    dcnNumber = document.getElementById('manualDcnInput').value.trim();

                    if (!dcnNumber) {
                        Swal.fire({
                            title: 'Missing DCN!',
                            text: 'Please enter a DCN number.',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    formData.append('manual_dcn', dcnNumber);
                    formData.append('is_manual', '1');
                } else {
                    // Auto-generated mode
                    const customerId = document.getElementById('modalCustomer').value;
                    const categoryId = document.getElementById('modalCategory').value;
                    const suffix = document.getElementById('dcnSuffix').value;

                    if (!customerId || !categoryId || !suffix) {
                        Swal.fire({
                            title: 'Incomplete Data!',
                            text: 'Customer, category, and suffix are required. Please ensure the document registry entry has complete information.',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    formData.append('customer_id', customerId);
                    formData.append('category_id', categoryId);
                    formData.append('dcn_suffix', suffix);
                    formData.append('is_manual', '0');
                }

                const entryId = currentEntryId;

                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Saving DCN number',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/dcn/${entryId}/update-dcn`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            dcnModal.hide();

                            Swal.fire({
                                title: 'Success!',
                                html: `DCN number saved successfully!<br><strong>${response.dcn_number}</strong>`,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload(); // Reload to show updated DCN
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        let errorMessage = 'An error occurred while saving the DCN number.';

                        if (response.errors) {
                            errorMessage = Object.values(response.errors).flat().join('<br>');
                        } else if (response.message) {
                            errorMessage = response.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            html: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Global functions
            window.openDcnModal = function(entryId) {
                currentEntryId = entryId;
                document.getElementById('entryId').value = entryId;

                // Reset form first
                document.getElementById('dcnForm').reset();
                document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-barcode"></i> Loading entry data...';
                document.getElementById('dcnStatus').innerHTML = '';
                document.getElementById('saveDcnBtn').disabled = true;

                // Reset to auto-generated mode by default
                document.getElementById('manualDcnMode').checked = false;
                document.getElementById('autoGeneratedSection').style.display = 'block';
                document.getElementById('manualDcnSection').style.display = 'none';
                document.getElementById('formatBreakdown').style.display = 'block';
                document.getElementById('dcnSuffix').disabled = false;
                document.getElementById('manualDcnInput').disabled = true;

                // Reset validation states
                document.getElementById('dcnSuffix').classList.remove('is-invalid', 'is-valid');
                document.getElementById('manualDcnInput').classList.remove('is-invalid', 'is-valid');

                // Reset status messages
                document.getElementById('customerStatus').innerHTML = 'Loading...';
                document.getElementById('categoryStatus').innerHTML = 'Loading...';
                document.getElementById('suffixStatus').innerHTML = 'Enter a unique 3-digit number (001-999)';
                document.getElementById('manualDcnStatus').innerHTML = '<span class="text-muted">Enter any DCN format you need</span>';

                // Load existing entry data
                $.ajax({
                    url: `/dcn/${entryId}/data`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.entry) {
                            const entry = response.entry;

                            // Populate read-only fields
                            $('#modalOriginator').val(entry.originator_name || '-');

                            // Format and populate dates
                            if (entry.submitted_at) {
                                const submittedDate = new Date(entry.submitted_at);
                                const formattedSubmitted = submittedDate.toLocaleDateString('en-US', {
                                    month: '2-digit',
                                    day: '2-digit',
                                    year: 'numeric'
                                }) + ' ' + submittedDate.toLocaleTimeString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });
                                $('#modalRegistrationDate').val(formattedSubmitted);
                            } else {
                                $('#modalRegistrationDate').val('-');
                            }

                            if (entry.implemented_at) {
                                const implementedDate = new Date(entry.implemented_at);
                                const formattedImplemented = implementedDate.toLocaleDateString('en-US', {
                                    month: '2-digit',
                                    day: '2-digit',
                                    year: 'numeric'
                                }) + ' ' + implementedDate.toLocaleTimeString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });
                                $('#modalEffectiveDate').val(formattedImplemented);
                            } else {
                                $('#modalEffectiveDate').val('-');
                            }

                            $('#modalDocumentNo').val(entry.document_no || '-');
                            $('#modalRevisionNo').val(entry.revision_no || '-');
                            $('#modalDeviceName').val(entry.device_name || '-');
                            $('#modalTitle').val(entry.document_title || '-');

                            // Check if both customer and category exist
                            if (entry.customer_id && entry.category_id && entry.customer && entry.category) {
                                // Populate customer (read-only)
                                $('#modalCustomer').val(entry.customer_id);
                                $('#customerStatus').html('<i class="bx bx-check-circle text-success"></i> ' + entry.customer.name + ' (' + entry.customer.code + ')');

                                // Populate category (read-only)
                                $('#modalCategory').val(entry.category_id);
                                $('#categoryStatus').html('<i class="bx bx-check-circle text-success"></i> ' + entry.category.name + ' (' + entry.category.code + ')');

                                // Show suggested suffix as help text (but don't auto-fill)
                                if (entry.suggested_suffix) {
                                    $('#suffixStatus').html('<i class="bx bx-info-circle text-info"></i> Suggested next available: ' + entry.suggested_suffix + ' (or enter your own)');
                                }

                                // If current DCN exists, extract and show its suffix OR populate manual field
                                if (entry.current_dcn) {
                                    const suffixMatch = entry.current_dcn.match(/-(\d{3})$/);
                                    if (suffixMatch) {
                                        // Standard format - populate suffix field
                                        $('#dcnSuffix').val(suffixMatch[1]);
                                    } else {
                                        // Non-standard format - switch to manual mode and populate
                                        document.getElementById('manualDcnMode').checked = true;
                                        document.getElementById('autoGeneratedSection').style.display = 'none';
                                        document.getElementById('manualDcnSection').style.display = 'block';
                                        document.getElementById('formatBreakdown').style.display = 'none';
                                        document.getElementById('dcnSuffix').disabled = true;
                                        document.getElementById('manualDcnInput').disabled = false;
                                        document.getElementById('manualDcnInput').value = entry.current_dcn;
                                    }

                                    const currentDcnHtml = `
                                        <div class="alert alert-info alert-sm mb-2">
                                            <i class="bx bx-info-circle"></i>
                                            <strong>Current DCN:</strong> ${entry.current_dcn}
                                        </div>
                                    `;
                                    document.getElementById('dcnStatus').innerHTML = currentDcnHtml;
                                }

                                // Update preview with loaded data
                                updateDcnPreview();

                            } else {
                                // Missing customer or category - show warning but allow manual override
                                let missingFields = [];

                                if (!entry.customer_id || !entry.customer) {
                                    $('#customerStatus').html('<i class="bx bx-error-circle text-danger"></i> No customer assigned');
                                    missingFields.push('Customer');
                                } else {
                                    $('#modalCustomer').val(entry.customer_id);
                                    $('#customerStatus').html('<i class="bx bx-check-circle text-success"></i> ' + entry.customer.name + ' (' + entry.customer.code + ')');
                                }

                                if (!entry.category_id || !entry.category) {
                                    $('#categoryStatus').html('<i class="bx bx-error-circle text-danger"></i> No category assigned');
                                    missingFields.push('Category');
                                } else {
                                    $('#modalCategory').val(entry.category_id);
                                    $('#categoryStatus').html('<i class="bx bx-check-circle text-success"></i> ' + entry.category.name + ' (' + entry.category.code + ')');
                                }

                                $('#suffixStatus').html('<i class="bx bx-info-circle text-warning"></i> Customer/Category missing - enable Manual DCN Override to proceed');

                                document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-info-circle"></i> Enable Manual DCN Override to assign DCN';
                                document.getElementById('dcnStatus').innerHTML = `
                            <div class="alert alert-warning alert-sm mb-0">
                                <i class="bx bx-exclamation-triangle"></i>
                                ${missingFields.join(' and ')} not assigned.
                                <strong>Enable "Manual DCN Override"</strong> above to enter a DCN number anyway.
                            </div>
                        `;
                            }
                        } else {
                            // Error loading data
                            document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-error-circle"></i> Error loading entry data';
                            $('#customerStatus').html('<i class="bx bx-error-circle text-danger"></i> Error loading data');
                            $('#categoryStatus').html('<i class="bx bx-error-circle text-danger"></i> Error loading data');
                            $('#suffixStatus').html('<i class="bx bx-error-circle text-danger"></i> Error loading data');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading entry data:', xhr);
                        document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-error-circle"></i> Error loading entry data';
                        $('#customerStatus').html('<i class="bx bx-error-circle text-danger"></i> Error loading data');
                        $('#categoryStatus').html('<i class="bx bx-error-circle text-danger"></i> Error loading data');
                        $('#suffixStatus').html('<i class="bx bx-error-circle text-danger"></i> Error loading data');
                    }
                });

                // Show modal
                dcnModal.show();
            };

            window.clearDcn = function(entryId) {
                Swal.fire({
                    title: 'Clear DCN Number?',
                    text: 'Are you sure you want to clear the DCN number for this entry?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<i class="bx bx-check"></i> Yes, clear it!',
                    cancelButtonText: '<i class="bx bx-x"></i> Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Clearing DCN number',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/dcn/${entryId}/clear-dcn`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Cleared!',
                                        text: 'DCN number has been cleared successfully.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An error occurred while clearing the DCN number.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            };

            window.showDcnFormatInfo = function() {
                Swal.fire({
                    title: '<i class="bx bx-info-circle"></i> DCN Format Information',
                    html: `
                <div class="text-start">
                    <h6>DCN Format Structure:</h6>
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>Format:</strong> [CategoryCode][Year]-[CustomerCode]-[3DigitSuffix]
                    </div>

                    <h6>Example:</h6>
                    <div class="bg-info bg-opacity-10 p-3 rounded mb-3">
                        <strong>CNA25-ALL-001</strong>
                        <ul class="mt-2 mb-0 text-start">
                            <li><strong>CNA</strong> = Category Code</li>
                            <li><strong>25</strong> = Current Year (2025)</li>
                            <li><strong>ALL</strong> = Customer Code</li>
                            <li><strong>001</strong> = User-defined 3-digit suffix</li>
                        </ul>
                    </div>

                    <h6>Notes:</h6>
                    <ul class="text-start">
                        <li>Year is automatically generated based on current year (2-digit format)</li>
                        <li>Category and Customer codes are taken from their respective master data</li>
                        <li>The 3-digit suffix must be unique for each DCN combination</li>
                        <li>System will check for duplicate DCN numbers before saving</li>
                    </ul>
                </div>
            `,
                    width: '600px',
                    confirmButtonText: '<i class="bx bx-check"></i> Got it!',
                    customClass: {
                        popup: 'text-start'
                    }
                });
            };
        });
    </script>
@endpush
