@extends('layouts.app')

@section('content')

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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class='bx bx-plus'></i> New Document Registration</h3>
                        <a href="{{ route('document-registry.index') }}" class="btn btn-secondary">
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

                                <!-- Document Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="document_no" class="form-label">
                                        <i class='bx bx-hash'></i> Document Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('document_no') is-invalid @enderror"
                                           id="document_no"
                                           name="document_no"
                                           value="{{ old('document_no') }}"
                                           required
                                           placeholder="e.g., DOC-2024-001">
                                    @error('document_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Revision Number -->
                                <div class="col-md-6 mb-3">
                                    <label for="revision_no" class="form-label">
                                        <i class='bx bx-revision'></i> Revision Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('revision_no') is-invalid @enderror"
                                           id="revision_no"
                                           pattern="\d{1,2}"
                                           name="revision_no"
                                           value="{{ old('revision_no', '0') }}"
                                           required
                                           placeholder="e.g., 0, 1, 2">
                                    @error('revision_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Device Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="device_name" class="form-label">
                                        <i class='bx bx-chip'></i> Device Name
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

                                <!-- Customer -->
                                <div class="col-md-6 mb-3">
                                    <label for="customer" class="form-label">
                                        <i class='bx bx-building'></i> Customer
                                    </label>
                                    <input type="text"
                                           class="form-control @error('customer') is-invalid @enderror"
                                           id="customer"
                                           name="customer"
                                           value="{{ old('customer') }}"
                                           placeholder="Enter customer name (if applicable)">
                                    @error('customer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                <a href="{{ route('document-registry.index') }}" class="btn btn-secondary">
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Categories data from server
    const categories = @json($categories);

    // Check if there's an old category_id from validation errors
    const oldCategoryId = '{{ old('category_id') }}';

    // Show category selection on page load (unless there are validation errors)
    if (!oldCategoryId) {
        showCategorySelection();
    } else {
        // If there are validation errors, pre-fill the category and show form
        const selectedCategory = categories.find(cat => cat.id == oldCategoryId);
        if (selectedCategory) {
            selectCategory(selectedCategory);
        }
    }

    // Change category button handler
    document.getElementById('changeCategoryBtn').addEventListener('click', showCategorySelection);


    function showCategorySelection() {
        // Group categories
        const generalCategories = categories.filter(cat =>
            !cat.name.toLowerCase().includes('mechatronics')
        );

        const mechatronicsCategories = categories.filter(cat =>
            cat.name.toLowerCase().includes('mechatronics')
        );

        let optionsHtml = '<div class="category-groups">';

        // General Categories Section
        if (generalCategories.length > 0) {
            optionsHtml += `
                <div class="category-section mb-4">
                    <h5 class="section-title mb-3">
                        <i class="bx bx-folder text-primary"></i> General Categories
                    </h5>
                    <div class="row">
            `;

            generalCategories.forEach((category, index) => {
                optionsHtml += `
                    <div class="col-md-6 mb-3">
                        <div class="category-option p-3 border rounded cursor-pointer hover-shadow"
                             data-category-id="${category.id}"
                             style="cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="category-icon me-3">
                                    <i class="bx bx-category-alt fs-4 text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">${category.name}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            optionsHtml += '</div></div>';
        }

        // Mechatronics Categories Section
        if (mechatronicsCategories.length > 0) {
            optionsHtml += `
                <div class="category-section">
                    <h5 class="section-title mb-3">
                        <i class="bx bx-cog text-success"></i> Mechatronics and Automation
                    </h5>
                    <div class="row">
            `;

            mechatronicsCategories.forEach((category, index) => {
                optionsHtml += `
                    <div class="col-md-6 mb-3">
                        <div class="category-option p-3 border rounded cursor-pointer hover-shadow"
                             data-category-id="${category.id}"
                             style="cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="category-icon me-3">
                                    <i class="bx bx-chip fs-4 text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">${category.name}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            optionsHtml += '</div></div>';
        }

        optionsHtml += '</div>';

        Swal.fire({
            title: '<i class="bx bx-category"></i> Select Document Category',
            html: `
                <div class="text-start">
                    <p class="text-muted mb-4">Choose the category that best fits your document:</p>
                    ${optionsHtml}
                    <div class="alert alert-warning mt-3">
                        <i class="bx bx-info-circle"></i>
                        <strong>Required:</strong> You must select a category to continue.
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
                // Add hover effects and click handlers
                const categoryOptions = document.querySelectorAll('.category-option');
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
                        const categoryId = this.getAttribute('data-category-id');
                        const selectedCategory = categories.find(cat => cat.id == categoryId);

                        // Add selected visual feedback
                        categoryOptions.forEach(opt => opt.style.backgroundColor = '');
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
        // Set the category values
        document.getElementById('category_id').value = category.id;

        // Set display field to show the full category name (no code)
        document.getElementById('category_display').value = category.name;

        // Hide loading, show form
        document.getElementById('loadingContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';

        // Show success message
        Swal.fire({
            title: 'Category Selected!',
            text: `You have selected: ${category.name}`,
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
            const customer = formData.get('customer') || 'Not specified';
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
                        <p><strong>Device Name:</strong> ${deviceName}</p>
                        <p><strong>Customer:</strong> ${customer}</p>
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
