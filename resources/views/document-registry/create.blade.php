@extends('layouts.app')

@section('content')
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

                    <div class="card-body">
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

                                <!-- Category -->
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">
                                        <i class='bx bx-category'></i> Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror"
                                            id="category_id"
                                            name="category_id"
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }} ({{ $category->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format document number
    const documentNoInput = document.getElementById('document_no');

    // Auto-format document number
    documentNoInput.addEventListener('blur', function() {
        let value = this.value.trim().toUpperCase();
        if (value && !value.includes('-')) {
            // Auto-format if it doesn't contain hyphens
            const year = new Date().getFullYear();
            const match = value.match(/(\d+)$/);
            if (match) {
                const number = match[1].padStart(3, '0');
                value = `DOC-${year}-${number}`;
            }
        }
        this.value = value;
    });

    // Validate revision number format
    const revisionInput = document.getElementById('revision_no');
    revisionInput.addEventListener('input', function() {
        // Only allow numbers and basic revision formats
        this.value = this.value.replace(/[^0-9A-Za-z.-]/g, '');
    });

    // File upload validation
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

            // Display file info
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            console.log(`Selected file: ${fileName} (${fileSize} MB)`);
        }
    });

    // Form submission with SweetAlert confirmation
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('documentForm');

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();

        // Check if form is valid first
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Get form values for confirmation
        const formData = new FormData(form);
        const documentTitle = formData.get('document_title') || 'Not specified';
        const documentNo = formData.get('document_no') || 'Not specified';
        const revisionNo = formData.get('revision_no') || 'Not specified';
        const deviceName = formData.get('device_name') || 'Not specified';
        const customer = formData.get('customer') || 'Not specified';
        const fileName = formData.get('document_file') ? formData.get('document_file').name : 'No file selected';
        const remarks = formData.get('remarks') || 'No remarks';

        // Get selected category name
        const categorySelect = document.getElementById('category_id');
        const categoryName = categorySelect.options[categorySelect.selectedIndex].textContent || 'Not selected';

        // Show confirmation dialog with form details
        Swal.fire({
            title: 'Confirm Document Submission',
            html: `
        <div class="text-left">
            <p><strong>Please review the following details before submitting:</strong></p>
            <hr>
            <p><strong>Document Title:</strong> ${documentTitle}</p>
            <p><strong>Category:</strong> ${categoryName}</p>
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
                // Show loading state
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

                // Submit the form
                form.submit();
            }
        });
    });
});
</script>
@endpush
