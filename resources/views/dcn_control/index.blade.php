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
                                    <th>Control No.</th>
                                    <th>DCN Number</th>
                                    <th>Document Title</th>
                                    <th>Document No.</th>
                                    <th>Rev.</th>
                                    <th>Customer</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($entries as $entry)
                                    <tr>
                                        <td>
                                            <strong>{{ $entry->control_no ?? '-' }}</strong>
                                        </td>
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
                                            <strong>{{ $entry->document_title ?? '-' }}</strong>
                                            @if($entry->device_name)
                                                <br><small class="text-muted">{{ $entry->device_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $entry->document_no ?? '-' }}</td>
                                        <td>{{ $entry->revision_no ?? '-' }}</td>
                                        <td>
                                            @if($entry->customer)
                                                {{ $entry->customer->name }}
                                                <br><small class="text-muted">Code: {{ $entry->customer->code }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($entry->category)
                                                {{ $entry->category->name }}
                                                <br><small class="text-muted">Code: {{ $entry->category->code }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($entry->status)
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
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
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
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="bx bx-cog"></i> Manage
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('dcn.show', $entry) }}">
                                                        <i class="bx bx-show me-2"></i> View Details
                                                    </a>
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

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $entries->links() }}
                        </div>
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

                        <div class="mb-3">
                            <label for="dcnSuffix" class="form-label">DCN Suffix (Auto-generated)</label>
                            <input type="text" class="form-control" id="dcnSuffix" name="dcn_suffix"
                                   maxlength="3" readonly>
                            <div class="form-text">
                                <span id="suffixStatus" class="text-muted">Next available suffix will be generated automatically</span>
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
                       <div class="bg-white border border-info p-3 rounded">
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
                            // Add export functionality here if needed
                            Swal.fire({
                                title: 'Export Feature',
                                text: 'Export functionality will be implemented soon.',
                                icon: 'info'
                            });
                        }
                    }
                ],
                responsive: true,
                order: [[8, 'desc']],
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

            // Preview DCN number as user types - now simplified since fields are read-only
            function updateDcnPreview() {
                const categorySelect = document.getElementById('modalCategory');
                const customerSelect = document.getElementById('modalCustomer');
                const suffixInput = document.getElementById('dcnSuffix');

                const categoryCode = categorySelect.options[categorySelect.selectedIndex]?.dataset.code;
                const customerCode = customerSelect.options[customerSelect.selectedIndex]?.dataset.code;
                const suffix = suffixInput.value;

                if (categoryCode && customerCode && suffix && suffix.length === 3) {
                    const currentYear = new Date().getFullYear().toString().slice(-2);
                    const dcnPreview = `${categoryCode}${currentYear}-${customerCode}-${suffix}`;

                    document.getElementById('dcnPreview').innerHTML = `<i class='bx bx-barcode'></i> ${dcnPreview}`;
                    document.getElementById('dcnStatus').innerHTML = '<div class="alert alert-success alert-sm mb-0"><i class="bx bx-check-circle"></i> DCN ready to assign</div>';
                    document.getElementById('saveDcnBtn').disabled = false;
                } else {
                    document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-barcode"></i> Waiting for complete data...';
                    document.getElementById('dcnStatus').innerHTML = '';
                    document.getElementById('saveDcnBtn').disabled = true;
                }
            }

            // Remove the old event listeners since fields are now read-only
            // Event listeners are no longer needed for manual input

            // Save DCN - updated validation since fields are auto-populated
            document.getElementById('saveDcnBtn').addEventListener('click', function() {
                // Basic validation
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

                const formData = new FormData(document.getElementById('dcnForm'));
                const entryId = currentEntryId;

                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Generating DCN number',
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
                                html: `DCN number generated successfully!<br><strong>${response.dcn_number}</strong>`,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload(); // Reload to show updated DCN
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        let errorMessage = 'An error occurred while generating the DCN number.';

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

                // Reset status messages
                document.getElementById('customerStatus').innerHTML = 'Loading...';
                document.getElementById('categoryStatus').innerHTML = 'Loading...';
                document.getElementById('suffixStatus').innerHTML = 'Loading...';

                // Load existing entry data
                $.ajax({
                    url: `/dcn/${entryId}/data`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.entry) {
                            const entry = response.entry;

                            // Check if both customer and category exist
                            if (entry.customer_id && entry.category_id && entry.customer && entry.category) {
                                // Populate customer (read-only)
                                $('#modalCustomer').val(entry.customer_id);
                                $('#customerStatus').html('<i class="bx bx-check-circle text-success"></i> ' + entry.customer.name + ' (' + entry.customer.code + ')');

                                // Populate category (read-only)
                                $('#modalCategory').val(entry.category_id);
                                $('#categoryStatus').html('<i class="bx bx-check-circle text-success"></i> ' + entry.category.name + ' (' + entry.category.code + ')');

                                // Set suggested suffix
                                if (entry.suggested_suffix) {
                                    $('#dcnSuffix').val(entry.suggested_suffix);
                                    $('#suffixStatus').html('<i class="bx bx-check-circle text-success"></i> Next available: ' + entry.suggested_suffix);
                                } else {
                                    $('#suffixStatus').html('<i class="bx bx-error-circle text-danger"></i> No available suffix found');
                                }

                                // Update preview immediately
                                updateDcnPreview();

                                // Show current DCN if exists
                                if (entry.current_dcn) {
                                    const currentDcnHtml = `
                                <div class="alert alert-info alert-sm mb-2">
                                    <i class="bx bx-info-circle"></i>
                                    <strong>Current DCN:</strong> ${entry.current_dcn}
                                </div>
                            `;
                                    document.getElementById('dcnStatus').innerHTML = currentDcnHtml + document.getElementById('dcnStatus').innerHTML;
                                }

                            } else {
                                // Missing customer or category - show error
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

                                $('#suffixStatus').html('<i class="bx bx-info-circle text-warning"></i> Complete customer and category assignment first');

                                document.getElementById('dcnPreview').innerHTML = '<i class="bx bx-error-circle"></i> Cannot generate DCN - missing required data';
                                document.getElementById('dcnStatus').innerHTML = `
                            <div class="alert alert-warning alert-sm mb-0">
                                <i class="bx bx-exclamation-triangle"></i>
                                Please assign ${missingFields.join(' and ')} in the document registry entry first.
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
