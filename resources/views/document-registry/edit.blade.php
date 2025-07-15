@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-edit'></i> Edit Document Registration</h3>
                    <a href="{{ route('document-registry.show', $documentRegistrationEntry) }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back to Details
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('document-registry.update', $documentRegistrationEntry) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                                       value="{{ old('document_title', $documentRegistrationEntry->document_title) }}"
                                       required
                                       placeholder="Enter document title">
                                @error('document_title')
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
                                       value="{{ old('document_no', $documentRegistrationEntry->document_no) }}"
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
                                       name="revision_no"
                                       value="{{ old('revision_no', $documentRegistrationEntry->revision_no) }}"
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
                                       value="{{ old('device_name', $documentRegistrationEntry->device_name) }}"
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
                                       class="form-control @error('originator_name') is-invalid @enderror"
                                       id="originator_name"
                                       name="originator_name"
                                       value="{{ old('originator_name', $documentRegistrationEntry->originator_name) }}"
                                       required
                                       placeholder="Enter originator name">
                                @error('originator_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Customer -->
                            <div class="col-md-12 mb-3">
                                <label for="customer" class="form-label">
                                    <i class='bx bx-building'></i> Customer
                                </label>
                                <input type="text"
                                       class="form-control @error('customer') is-invalid @enderror"
                                       id="customer"
                                       name="customer"
                                       value="{{ old('customer', $documentRegistrationEntry->customer) }}"
                                       placeholder="Enter customer name (if applicable)">
                                @error('customer')
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
                                          placeholder="Enter any additional remarks or notes">{{ old('remarks', $documentRegistrationEntry->remarks) }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Status Info -->
                        <div class="alert alert-info">
                            <i class='bx bx-info-circle'></i>
                            <strong>Status:</strong> {{ $documentRegistrationEntry->status_name }}
                            <br>
                            <strong>Note:</strong> Changes will be saved but the approval status will remain unchanged.
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('document-registry.show', $documentRegistrationEntry) }}" class="btn btn-secondary">
                                <i class='bx bx-x'></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-check'></i> Update Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format document number
    const documentNoInput = document.getElementById('document_no');
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
});
</script>
@endsection
