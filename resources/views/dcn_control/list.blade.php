@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class='bx bx-list-ul'></i>
                            <span id="logTitleLabel">Document Registration Logs</span>
                        </h3>
                        <button class="btn btn-secondary btn-sm" id="changeLogBtn">
                            <i class="bx bx-repeat"></i> Change Category
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3" id="logInfoAlert" style="display:none;"></div>

                        <!-- Customer Tabs -->
                        <div class="nav-align-top mb-4">
                            <ul class="nav nav-tabs" role="tablist" id="customerTabs">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab"
                                            data-bs-toggle="tab" data-bs-target="#tab-all-customers"
                                            aria-controls="tab-all-customers" aria-selected="true"
                                            data-customer-id="">
                                        <i class='bx bx-group'></i> All Customers
                                    </button>
                                </li>

                                <!-- No Customer tab (entries with customer_id IS NULL) -->
                                @if(isset($noCustomerCount) && $noCustomerCount > 0)
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab"
                                                data-bs-toggle="tab" data-bs-target="#tab-no-customer"
                                                aria-controls="tab-no-customer" aria-selected="false"
                                                data-customer-id="__no_customer__">
                                            <i class='bx bx-user-x'></i> No Customer
                                            <span class="badge bg-primary ms-1">{{ $noCustomerCount }}</span>
                                        </button>
                                    </li>
                                @endif

                                @forelse($customers as $customer)
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab"
                                                data-bs-toggle="tab" data-bs-target="#tab-customer-{{ $customer->id }}"
                                                aria-controls="tab-customer-{{ $customer->id }}" aria-selected="false"
                                                data-customer-id="{{ $customer->id }}">
                                            {{ $customer->code }}
                                            <span class="badge bg-primary ms-1">{{ $customer->document_registration_entries_count }}</span>
                                        </button>
                                    </li>
                                @empty
                                @endforelse
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-all-customers" role="tabpanel">
                                    @if($customers->isEmpty())
                                        <div class="alert alert-warning">
                                            <i class='bx bx-info-circle'></i>
                                            No customers have registrations in this category. Change the category to see customer tabs.
                                        </div>
                                    @endif
                                    <div class="table-responsive">
                                        {!! $dataTable->table(['class' => 'table table-striped table-hover'], true) !!}
                                    </div>
                                </div>

                                <!-- No Customer tab-pane -->
                                @if(isset($noCustomerCount) && $noCustomerCount > 0)
                                    <div class="tab-pane fade" id="tab-no-customer" role="tabpanel">
                                        <div class="table-responsive">
                                            <!-- Table will be moved here dynamically -->
                                        </div>
                                    </div>
                                @endif

                                @foreach($customers as $customer)
                                    <div class="tab-pane fade" id="tab-customer-{{ $customer->id }}" role="tabpanel">
                                        <div class="table-responsive">
                                            <!-- Table will be moved here dynamically -->
                                        </div>
                                    </div>
                                @endforeach
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

                            <!-- Save Category/Customer Button -->
                            <div class="row" id="saveCategoryCustomerRow" style="display: none;">
                                <div class="col-12">
                                    <button type="button" class="btn btn-info btn-sm mb-3" id="saveCategoryCustomerBtn">
                                        <i class='bx bx-save'></i> Save Category & Customer Changes
                                    </button>
                                </div>
                            </div>

                            <div class="row" id="categoryCustomerSuccessRow" style="display: none;">
                                <div class="col-12">
                                    <div class="alert alert-success mb-3" role="alert">
                                        <i class='bx bx-check-circle'></i>
                                        <strong>Success!</strong> Category and Customer saved. You can now assign a DCN number.
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

                            <!-- DCN Mode Selection -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="manualDcnMode">
                                    <label class="form-check-label" for="manualDcnMode">
                                        <i class='bx bx-edit'></i> Manual DCN Override
                                    </label>
                                </div>
                                <small class="text-muted">Enable to manually enter a complete DCN number</small>
                            </div>

                            <!-- Auto-generated DCN Section -->
                            <div id="autoGeneratedSection">
                                <div class="mb-3">
                                    <label for="dcnSuffix" class="form-label">DCN Suffix (3-digit) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="dcnSuffix" name="dcn_suffix"
                                           maxlength="3" placeholder="e.g., 001">
                                    <div class="form-text">
                                        <span id="suffixStatus" class="text-muted">Enter a unique 3-digit number (001-999)</span>
                                    </div>
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
                                           placeholder="e.g., CNA25-ALL-001">
                                    <div class="form-text">
                                        <span id="manualDcnStatus" class="text-muted">Enter any DCN format you need</span>
                                    </div>
                                    <div class="invalid-feedback" id="manualDcnError">
                                        Please enter a DCN number.
                                    </div>
                                </div>
                                <div class="alert alert-warning alert-sm">
                                    <i class='bx bx-info-circle'></i>
                                    <strong>Note:</strong> Manual DCN override bypasses customer/category requirements.
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
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
    .nav-tabs .nav-link {
        color: #6c757d;
        padding: 0.5rem 1rem;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }
    .nav-tabs .nav-link .badge {
        font-size: 0.7em;
        padding: 0.25rem 0.5rem;
    }

    /* DataTables styling fixes */
    #logTable_wrapper .dataTables_length {
        float: left;
        margin-bottom: 1rem;
    }

    #logTable_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 1rem;
    }

    #logTable_wrapper .dataTables_info {
        float: left;
        padding-top: 0.755em;
    }

    #logTable_wrapper .dataTables_paginate {
        float: right;
        padding-top: 0.25em;
    }

    #logTable_wrapper .dt-buttons {
        float: left;
        margin-bottom: 1rem;
        margin-right: 0.5rem;
    }

    #logTable_wrapper::after {
        content: "";
        display: table;
        clear: both;
    }
</style>
@endpush

@push('scripts')
<script>
    const urlParams = new URLSearchParams(window.location.search);
    window.selectedSubcategoryId = urlParams.get('subcategory_id') || '';
    window.selectedCustomerId = '';
    const mainCategories = @json($mainCategories ?? []);
    const subcategoryIndex = {};
    mainCategories.forEach(mc => {
        (mc.subcategories || []).forEach(sc => {
            subcategoryIndex[sc.id] = {...sc, main_category: {id: mc.id, name: mc.name}};
        });
    });
</script>
{!! $dataTable->scripts() !!}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleEl = document.getElementById('logTitleLabel');
    const alertEl = document.getElementById('logInfoAlert');
    const changeBtn = document.getElementById('changeLogBtn');
    const getDt = () => window.LaravelDataTables && window.LaravelDataTables['logTable'];
    const customerTabs = document.querySelectorAll('#customerTabs button[data-bs-toggle="tab"]');

    const hasSubcategory = (new URLSearchParams(window.location.search)).has('subcategory_id');
    if (!hasSubcategory) {
        showCategoryWizard();
    } else {
        updateLogUI();
    }

    changeBtn.addEventListener('click', showCategoryWizard);

    customerTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function () {
            window.selectedCustomerId = this.getAttribute('data-customer-id') || '';
            const targetPane = document.querySelector(this.getAttribute('data-bs-target'));
            const targetTableContainer = targetPane.querySelector('.table-responsive');
            const tableElement = document.querySelector('#logTable_wrapper');
            if (targetTableContainer && tableElement && !targetTableContainer.contains(tableElement)) {
                targetTableContainer.appendChild(tableElement);
            }
            const dt = getDt();
            if (dt) dt.ajax.reload();
        });
    });

    function escapeHtml(v){return (v??'').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');}
    function formatCategoryLabel(cat){return cat ? (cat.main_category?.name ? `${cat.name} (${cat.main_category.name})` : cat.name) : ''; }

    function updateLogUI() {
        const selected = subcategoryIndex[window.selectedSubcategoryId];
        if (!selected) return;
        const label = formatCategoryLabel(selected);
        titleEl.textContent = `${label} - Registration Logsheet`;
        alertEl.style.display = 'block';
        alertEl.innerHTML = `<i class='bx bx-info-circle'></i> Showing entries for: <strong>${escapeHtml(label)}</strong>`;
    }

    function showCategoryWizard() {
        let sectionsRendered = 0;
        let optionsHtml = '<div class="category-groups">';
        mainCategories.forEach(mc => {
            const subs = Array.isArray(mc.subcategories) ? mc.subcategories : [];
            if (!subs.length) return;
            sectionsRendered++;
            optionsHtml += `
                <div class="category-section mb-4">
                    <h5 class="section-title mb-3">
                        <i class="bx bx-folder text-primary"></i> ${escapeHtml(mc.name || 'Main Category')}
                    </h5>
                    <div class="row">
                        ${subs.map(sc => {
                            const codeLabel = sc.code ? `Code: ${escapeHtml(String(sc.code).toUpperCase())}` : 'Code not set';
                            return `
                                <div class="col-md-6 mb-3">
                                    <div class="category-option p-3 border rounded hover-shadow" data-subcategory-id="${sc.id}" style="cursor:pointer; transition:all 0.2s;">
                                        <div class="d-flex align-items-center">
                                            <div class="category-icon me-3">
                                                <i class="bx bx-category-alt fs-4 text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">${escapeHtml(sc.name || 'Subcategory')}</h6>
                                                <small class="text-muted">${codeLabel}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>`;
        });

        if (!sectionsRendered) {
            optionsHtml += `
                <div class="alert alert-warning mb-0">
                    <i class="bx bx-info-circle"></i> No active subcategories are available. Please contact your administrator.
                </div>`;
        }
        optionsHtml += `
            <div class="alert alert-warning mt-3 mb-0">
                <i class="bx bx-info-circle"></i>
                <strong>Required:</strong> Please choose a category to continue.
            </div>
        </div>`;

        Swal.fire({
            title: '<i class="bx bx-category"></i> Select Document Category',
            html: optionsHtml,
            width: '850px',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: { htmlContainer: 'text-start', popup: 'category-selection-popup' },
            didOpen: () => {
                const popup = Swal.getPopup();
                if (!popup) return;
                const opts = popup.querySelectorAll('.category-option');
                opts.forEach(opt => {
                    opt.addEventListener('mouseenter', function(){this.style.backgroundColor='#f8f9fa';this.style.borderColor='#007bff';this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)';});
                    opt.addEventListener('mouseleave', function(){this.style.backgroundColor='';this.style.borderColor='';this.style.transform='';this.style.boxShadow='';});
                    opt.addEventListener('click', function(){
                        const sid = this.getAttribute('data-subcategory-id');
                        const sel = subcategoryIndex[sid];
                        if (!sel) return;
                        opts.forEach(o=>{o.style.backgroundColor='';o.style.borderColor='';});
                        this.style.backgroundColor='#e3f2fd';
                        this.style.borderColor='#2196f3';
                        Swal.close();
                        onCategorySelected(sid);
                    });
                });
            }
        });
    }

    function onCategorySelected(subcategoryId) {
        window.selectedSubcategoryId = subcategoryId;
        window.selectedCustomerId = '';
        const url = new URL(window.location.href);
        url.searchParams.set('subcategory_id', subcategoryId);
        window.location.href = url.toString();
    }
});
</script>

@include('dcn_control.scripts.dcnCrud')
@endpush
