@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
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
                        <form id="filterForm" method="GET" action="{{ route('dcn.index') }}" class="mb-4">
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

                                <!-- Apply / Clear placed inside advancedFilters so they hide with the rest -->
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" id="applyFiltersBtn" class="btn btn-primary">
                                                <i class='bx bx-search'></i> Apply Filters
                                            </button>
                                            <a href="{{ route('dcn.index') }}" id="clearFiltersBtn" class="btn btn-secondary">
                                                <i class='bx bx-x'></i> Clear Filters
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

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
                                        <td>{{ $entry->device_name ?? 'N/A' }}</td>
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
                                            @if(request('advanced') && request()->hasAny(['dcn_status', 'customer_id', 'category_id', 'search', 'date_from', 'date_to']))
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
                                        <select class="form-select" id="modalCustomer" name="customer_id">
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
                                        <select class="form-select" id="modalCategory" name="category_id">
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

                            <!-- Save Category/Customer Button (hidden by default) -->
                            <div class="row" id="saveCategoryCustomerRow" style="display: none;">
                                <div class="col-12">
                                    <button type="button" class="btn btn-info btn-sm mb-3" id="saveCategoryCustomerBtn">
                                        <i class='bx bx-save'></i> Save Category & Customer Changes
                                    </button>
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
                                    <!-- Live auto-completion line (purely frontend) -->
                                    <div class="small mt-1 text-muted" id="suffixAutoCompletion" style="display:none;">
                                        <i class="bx bx-barcode"></i>
                                        <span>Auto-complete preview: </span>
                                        <span class="fw-bold text-primary" id="suffixAutoCompletionValue"></span>
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
        #dcnToolbarCounts .badge {
            font-size: 0.78rem;
            padding: 0.22rem 0.45rem;
            line-height: 1;
        }
        /* keep small gap and vertical alignment with dt buttons */
        #dcnToolbarCounts {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 10px;
            vertical-align: middle;
        }
    </style>
@endpush

@push('scripts')
    @include('dcn_control.scripts.advanceToggle')
    @include('dcn_control.scripts.dataTable')
    @include('dcn_control.scripts.dcnCrud')
@endpush
