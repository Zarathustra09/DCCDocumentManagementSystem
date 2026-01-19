@php($showHelpTour = true)
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <style>
            .category-section {
                border-bottom: 1px solid #e9ecef;
                padding-bottom: 1rem;
            }

            .category-section:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }

            .section-title {
                color: #495057;
                font-weight: 600;
                border-bottom: 2px solid #e9ecef;
                padding-bottom: 0.5rem;
                margin-bottom: 1rem !important;
            }

            .category-groups {
                max-height: 500px;
                overflow-y: auto;
            }
        </style>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class='bx bx-plus'></i> New Document Registration</h3>
                        <a href="{{ route('document-registry.index') }}" class="btn btn-secondary" id="back-to-registry-btn">
                            <i class='bx bx-arrow-back'></i> Back to Registry
                        </a>
                    </div>

                    <div class="card-body" id="formContainer" style="display: none;">
                        <form id="documentForm" action="{{ route('document-registry.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Document Title -->
                                <div class="col-md-12 mb-3">
                                    <label for="document_title" class="form-label">
                                        <i class='bx bx-file'></i> Document Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('document_title') is-invalid @enderror"
                                           id="document_title"
                                           name="document_title"
                                           value="{{ old('document_title') }}"
                                           required
                                           placeholder="Enter document title">
                                    @error('document_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category (Hidden input) -->
                                <div class="col-md-6 mb-3">
                                    <label for="category_display" class="form-label">
                                        <i class='bx bx-category'></i> Category <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control"
                                               id="category_display"
                                               readonly
                                               placeholder="Category will be selected">
                                        <button type="button" class="btn btn-outline-secondary" id="changeCategoryBtn">
                                            <i class='bx bx-edit'></i> Change
                                        </button>
                                    </div>
                                    <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id') }}">
                                    @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Customer Field (add this after the category field) -->
                                <div class="col-md-6" id="customer_group">
                                     <div class="form-group">
                                         <label for="customer_id" class="form-label">
                                             <i class='bx bx-building'></i> Customer
                                         </label>
                                         <select class="form-control" id="customer_id" name="customer_id">
                                             <option value="">Select Customer (Optional)</option>
                                             @foreach($customers as $customer)
                                                 <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                     {{ $customer->name }}
                                                 </option>
                                             @endforeach
                                         </select>
                                         @error('customer_id')
                                         <div class="text-danger">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                <!-- Document Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="document_no" class="form-label">
                                        <i class='bx bx-hash'></i> Document Number (Optional)
                                    </label>
                                    <input type="text"
                                           class="form-control @error('document_no') is-invalid @enderror"
                                           id="document_no"
                                           name="document_no"
                                           value="{{ old('document_no') }}"
                                           placeholder="e.g., DOC-2024-001">
                                    @error('document_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Revision Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="revision_no" class="form-label">
                                        <i class='bx bx-revision'></i> Revision Number (Optional)
                                    </label>
                                    <input type="text"
                                           class="form-control @error('revision_no') is-invalid @enderror"
                                           id="revision_no"
                                           pattern="\d{1,2}"
                                           name="revision_no"
                                           value="{{ old('revision_no', '') }}"
                                           placeholder="e.g., 0, 1, 2">
                                    @error('revision_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Device Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="device_name" class="form-label">
                                        <i class='bx bx-chip'></i> Device Name / Part Number
                                    </label>
                                    <input type="text"
                                           class="form-control @error('device_name') is-invalid @enderror"
                                           id="device_name"
                                           name="device_name"
                                           value="{{ old('device_name') }}"
                                           placeholder="Enter device name (if applicable)">
                                    @error('device_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Originator Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="originator_name" class="form-label">
                                        <i class='bx bx-user'></i> Originator Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="originator_name"
                                           name="originator_name"
                                           value="{{ auth()->user()->name }}"
                                           readonly
                                           required>
                                    <small class="form-text text-muted">
                                        <i class='bx bx-info-circle'></i> Originator is automatically set to your name
                                    </small>
                                </div>


                                <!-- Document File Upload -->
                                <div class="col-md-12 mb-3">
                                    <label for="document_file" class="form-label">
                                        <i class='bx bx-upload'></i> Document File <span class="text-danger">*</span>
                                    </label>
                                    <input type="file"
                                           class="form-control @error('document_file') is-invalid @enderror"
                                           id="document_file"
                                           name="document_file"
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                                           required>
                                    <div class="form-text">
                                        <i class='bx bx-info-circle'></i>
                                        Accepted formats: PDF, Word, Excel, PowerPoint, Text files. Maximum size: 10MB
                                    </div>
                                    @error('document_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Remarks -->
                                <div class="col-md-12 mb-3">
                                    <label for="remarks" class="form-label">
                                        <i class='bx bx-note'></i> Remarks
                                    </label>
                                    <textarea class="form-control @error('remarks') is-invalid @enderror"
                                              id="remarks"
                                              name="remarks"
                                              rows="4"
                                              placeholder="Enter any additional remarks or notes">{{ old('remarks') }}</textarea>
                                    @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submission Info -->
                            <div class="alert alert-info">
                                <i class='bx bx-info-circle'></i>
                                <strong>Note:</strong> This document registration will be submitted for implementation.
                                You will be able to edit the details while it's in pending status.
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('document-registry.index') }}" class="btn btn-secondary" id="cancel-btn">
                                    <i class='bx bx-x'></i> Cancel
                                </a>
                                <button type="button" id="submitBtn" class="btn btn-primary">
                                    <i class='bx bx-check'></i> Submit for Registration
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Loading State -->
                    <div class="card-body text-center" id="loadingContainer">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Please select a category to continue...</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- / Content -->
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Main categories with subcategories from server
    const mainCategories = @json($mainCategories ?? []);

    // Build a flat index of subcategories for quick lookup
    const subcategoryIndex = {};
    mainCategories.forEach(mainCategory => {
        if (!Array.isArray(mainCategory.subcategories)) {
            return;
        }
        mainCategory.subcategories.forEach(subcategory => {
            subcategoryIndex[subcategory.id] = Object.assign({}, subcategory, {
                main_category: {
                    id: mainCategory.id,
                    name: mainCategory.name
                }
            });
        });
    });

    // Check if there's an old category_id from validation errors
    const oldCategoryId = '{{ old('category_id') }}';

    // Show category selection on page load (unless there are validation errors)
    if (!oldCategoryId) {
        showCategorySelection();
    } else {
        // If there are validation errors, pre-fill the category and show form
        const selectedCategory = subcategoryIndex[oldCategoryId];
        if (selectedCategory) {
            selectCategory(selectedCategory);
        }
    }

    // Change category button handler
    document.getElementById('changeCategoryBtn').addEventListener('click', showCategorySelection);

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatCategoryLabel(category) {
        if (!category) {
            return '';
        }
        if (category.main_category && category.main_category.name) {
            return `${category.name} (${category.main_category.name})`;
        }
        return category.name;
    }

    function showCategorySelection() {
        let sectionsRendered = 0;
        let optionsHtml = '<div class="category-groups">';

        mainCategories.forEach(mainCategory => {
            const subcategories = Array.isArray(mainCategory.subcategories) ? mainCategory.subcategories : [];
            if (!subcategories.length) {
                return;
            }

            sectionsRendered++;
            optionsHtml += `
                <div class="category-section mb-4">
                    <h5 class="section-title mb-3">
                        <i class="bx bx-folder text-primary"></i> ${escapeHtml(mainCategory.name || 'Main Category')}
                    </h5>
                    <div class="row">
            `;

            subcategories.forEach(subcategory => {
                const codeValue = subcategory.code ? String(subcategory.code).toUpperCase() : null;
                const codeLabel = codeValue ? `Code: ${escapeHtml(codeValue)}` : 'Code not set';

                optionsHtml += `
                    <div class="col-md-6 mb-3">
                        <div class="category-option p-3 border rounded hover-shadow"
                             data-subcategory-id="${subcategory.id}"
                             style="cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="category-icon me-3">
                                    <i class="bx bx-category-alt fs-4 text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">${escapeHtml(subcategory.name || 'Subcategory')}</h6>
                                    <small class="text-muted">${codeLabel}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            optionsHtml += '</div></div>';
        });

        if (!sectionsRendered) {
            optionsHtml += `
                <div class="alert alert-warning mb-0">
                    <i class="bx bx-info-circle"></i> No active subcategories are available. Please contact your administrator.
                </div>
            `;
        }

        optionsHtml += '</div>';

        Swal.fire({
            title: '<i class="bx bx-category"></i> Select Document Category',
            html: `
                <div class="text-start">
                    <p class="text-muted mb-4">Choose the subcategory that best fits your document:</p>
                    ${optionsHtml}
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bx bx-info-circle"></i>
                        <strong>Required:</strong> You must select a subcategory to continue.
                    </div>
                </div>
            `,
            width: '800px',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                htmlContainer: 'text-start',
                popup: 'category-selection-popup'
            },
            didOpen: () => {
                const popup = Swal.getPopup();
                if (!popup) {
                    return;
                }

                // Add hover effects and click handlers
                const categoryOptions = popup.querySelectorAll('.category-option');
                categoryOptions.forEach(option => {
                    option.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#f8f9fa';
                        this.style.borderColor = '#007bff';
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                    });

                    option.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = '';
                        this.style.borderColor = '';
                        this.style.transform = '';
                        this.style.boxShadow = '';
                    });

                    option.addEventListener('click', function() {
                        const categoryId = this.getAttribute('data-subcategory-id');
                        const selectedCategory = subcategoryIndex[categoryId];

                        if (!selectedCategory) {
                            return;
                        }

                        // Add selected visual feedback
                        categoryOptions.forEach(opt => {
                            opt.style.backgroundColor = '';
                            opt.style.borderColor = '';
                        });
                        this.style.backgroundColor = '#e3f2fd';
                        this.style.borderColor = '#2196f3';

                        // Close SweetAlert and proceed
                        Swal.close();
                        selectCategory(selectedCategory);
                    });
                });
            }
        });
    }

    function selectCategory(category) {
        if (!category) {
            Swal.fire('Category Required', 'Please select a category to continue.', 'warning');
            return;
        }

        // Set the category values
        document.getElementById('category_id').value = category.id;
        document.getElementById('category_display').value = formatCategoryLabel(category);

        // Hide loading, show form
        document.getElementById('loadingContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';

        // Show success message
        Swal.fire({
            title: 'Category Selected!',
            text: `You have selected: ${formatCategoryLabel(category)}`,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    // Rest of the existing form functionality
    const documentNoInput = document.getElementById('document_no');

    documentNoInput.addEventListener('blur', function() {
        let value = this.value.trim().toUpperCase();
        if (value && !value.includes('-')) {
            const year = new Date().getFullYear();
            const match = value.match(/(\d+)$/);
            if (match) {
                const number = match[1].padStart(3, '0');
                value = `DOC-${year}-${number}`;
            }
        }
        this.value = value;
    });

    const revisionInput = document.getElementById('revision_no');
    revisionInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9A-Za-z.-]/g, '');
    });

    const fileInput = document.getElementById('document_file');
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'File size must be less than 10MB',
                    confirmButtonColor: '#d33'
                });
                this.value = '';
                return;
            }

            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            console.log(`Selected file: ${fileName} (${fileSize} MB)`);
        }
    });

    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('documentForm');

    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const documentTitle = formData.get('document_title') || 'Not specified';
            const documentNo = formData.get('document_no') || 'Not specified';
            const revisionNo = formData.get('revision_no') || 'Not specified';
            const deviceName = formData.get('device_name') || 'Not specified';
            const customerSelect = document.getElementById('customer_id');
            const customerText = customerSelect.selectedIndex > 0 ? customerSelect.options[customerSelect.selectedIndex].text : 'Not specified';
            const fileName = formData.get('document_file') ? formData.get('document_file').name : 'No file selected';
            const remarks = formData.get('remarks') || 'No remarks';
            const categoryDisplay = document.getElementById('category_display').value || 'Not selected';

            Swal.fire({
                title: 'Confirm Document Submission',
                html: `
                    <div class="text-left">
                        <p><strong>Please review the following details before submitting:</strong></p>
                        <hr>
                        <p><strong>Document Title:</strong> ${documentTitle}</p>
                        <p><strong>Category:</strong> ${categoryDisplay}</p>
                        <p><strong>Document Number:</strong> ${documentNo}</p>
                        <p><strong>Revision Number:</strong> ${revisionNo}</p>
                        <p><strong>Device Name / Part Number:</strong> ${deviceName}</p>
                        <p><strong>Customer:</strong> ${customerText}<br></p>
                        <p><strong>File:</strong> ${fileName}</p>
                        <p><strong>Remarks:</strong> ${remarks.substring(0, 100)}${remarks.length > 100 ? '...' : ''}</p>
                        <hr>
                        <p class="text-muted"><small><i class='bx bx-info-circle'></i> This document will be submitted for approval and you can edit it while it's in pending status.</small></p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bx bx-check"></i> Yes, Submit',
                cancelButtonText: '<i class="bx bx-x"></i> Cancel',
                width: '600px',
                customClass: {
                    htmlContainer: 'text-left'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Submitting Document...',
                        text: 'Please wait while we process your document registration.',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });
        });
    }
});
</script>

<style>
.category-selection-popup .swal2-html-container {
    max-height: 500px;
    overflow-y: auto;
}

.hover-shadow {
    transition: all 0.2s ease-in-out;
}

.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush

@push('driverjs')
<script>
window.addEventListener('start-driverjs-tour', function() {
    const driver = window.driver.js.driver;
    driver({
        showProgress: true,
        steps: [
            {
                element: '#document_title',
                popover: {
                    title: 'Document Title',
                    description: 'Begin by entering the title of your document. For example, "IT Equipment Preventive Maintenance Procedure.pdf". This field is required and should clearly describe the document\'s purpose.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#category_display',
                popover: {
                    title: 'Category Selection',
                    description: 'Select the appropriate category for your document. Categories help organize documents and ensure they are routed correctly. For instance, choose "Mechatronics and Automation" if your document relates to that category.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#customer_id',
                popover: {
                    title: 'Customer',
                    description: 'Optionally, select a customer associated with this document. This is useful for documents specific to a client or company. For example, select "Acme Corp" if the document is for that customer.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#document_no',
                popover: {
                    title: 'Document Number',
                    description: 'Provide a unique document number, such as "DOC-2024-001". This helps in tracking and referencing the document throughout its lifecycle.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#revision_no',
                popover: {
                    title: 'Revision Number',
                    description: 'Specify the revision number for your document. For initial submissions, use "01". For updates, increment the number, e.g., "02" or "03".',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#device_name',
                popover: {
                    title: 'Device Name',
                    description: 'If applicable, enter the device name related to this document. For example, "Servo Motor X100". Leave blank if not relevant.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#originator_name',
                popover: {
                    title: 'Originator Name',
                    description: 'This field is automatically set to your name, indicating who is submitting the document. You cannot change this value.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#document_file',
                popover: {
                    title: 'Document File Upload',
                    description: 'Upload your document file here. Accepted formats include PDF, Word, Excel, PowerPoint, and Text files. The maximum file size is 10MB. For example, upload "IT Equipment Preventive Maintenance Procedure.pdf".',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#remarks',
                popover: {
                    title: 'Remarks',
                    description: 'Add any additional notes or remarks that may help reviewers understand your submission. For example, "This document outlines the preventive maintenance procedures for IT equipment."',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#submitBtn',
                popover: {
                    title: 'Submit for Registration',
                    description: 'Once all required fields are filled, click this button to submit your document for registration. You will be able to edit details while the document is in pending status.',
                    side: 'bottom',
                    align: 'start'
                }
            }
        ]
    }).drive();
});
</script>
@endpush
