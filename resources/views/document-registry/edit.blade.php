@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class='bx bx-edit'></i> Edit Document Registration</h3>
                    <a href="javascript:history.back()" class="btn btn-secondary">
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

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">
                                    <i class='bx bx-category'></i> Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                        id="category_id"
                                        name="category_id"
                                        required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ old('category_id', $documentRegistrationEntry->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
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
                            <strong>Current Status:</strong>
                            @if($documentRegistrationEntry->status->name === 'Pending')
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class='bx bx-time'></i> {{ $documentRegistrationEntry->status->name }}
                                </span>
                            @elseif($documentRegistrationEntry->status->name === 'Implemented')
                                <span class="badge bg-success text-white ms-2">
                                    <i class='bx bx-check'></i> {{ $documentRegistrationEntry->status->name }}
                                </span>
                            @else
                                <span class="badge bg-danger text-white ms-2">
                                    <i class='bx bx-x'></i> {{ $documentRegistrationEntry->status->name }}
                                </span>
                            @endif
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
@endsection

@push('styles')
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validate revision number format
            const revisionInput = document.getElementById('revision_no');
            revisionInput.addEventListener('input', function() {
                // Only allow numbers and basic revision formats
                this.value = this.value.replace(/[^0-9A-Za-z.-]/g, '');
            });
        });
    </script>
@endpush
