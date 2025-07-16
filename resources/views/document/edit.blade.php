@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Document</h5>
                    <div>
                        <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="bx bx-arrow-back"></i> Back
                        </a>
                        @can('delete documents')
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bx bx-trash"></i> Delete
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form id="document-edit-form" action="{{ route('documents.update', $document) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select @error('department') is-invalid @enderror" id="department" name="department" required>
                                @foreach($departments as $key => $name)
                                    <option value="{{ $key }}" {{ $document->department == $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="folder_id" class="form-label">Folder <span class="text-danger">*</span></label>
                            <select class="form-select @error('folder_id') is-invalid @enderror" id="folder_id" name="folder_id" required>
                                <option value="">Please select a folder</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ $document->folder_id == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Documents must be placed in a folder</div>
                            @error('folder_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        @can('approve document registration')
                            <div class="mb-3">
                                <label for="document_registration_entry_id" class="form-label">Associated Registration Entry</label>
                                <select class="form-select select2-ajax @error('document_registration_entry_id') is-invalid @enderror"
                                        id="document_registration_entry_id"
                                        name="document_registration_entry_id"
                                        data-placeholder="Search for a registration entry..."
                                        data-ajax-url="{{ route('document-registration-entries.search') }}">
                                    <option value="">None (Optional)</option>
                                    @if($document->documentRegistrationEntry)
                                        <option value="{{ $document->documentRegistrationEntry->id }}" selected>
                                            {{ $document->documentRegistrationEntry->document_no }} - {{ $document->documentRegistrationEntry->document_title }}
                                        </option>
                                    @endif
                                </select>
                                <div class="form-text">Optionally link this document to an approved registration entry</div>
                                    @error('document_registration_entry_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        @endcan

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this document? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('documents.destroy', $document) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Document</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
    }
    .select2-result__title {
        font-weight: 500;
    }
    .select2-result__info {
        font-size: 0.85em;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for registration entry
    $('.select2-ajax').select2({
        theme: 'bootstrap-5',
        width: '100%',
        delay: 250,
        minimumInputLength: 2,
        ajax: {
            url: function() {
                return $(this).data('ajax-url');
            },
            dataType: 'json',
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data) {
                return {
                    results: data.results,
                    pagination: data.pagination
                };
            },
            cache: true
        },
        placeholder: $(this).data('placeholder'),
        allowClear: true,
        templateResult: formatRegistrationEntry,
        templateSelection: formatRegistrationEntrySelection
    });

    function formatRegistrationEntry(entry) {
        if (entry.loading) {
            return entry.text;
        }

        if (!entry.id) {
            return entry.text;
        }

        var $container = $(
            '<div class="select2-result">' +
                '<div class="select2-result__title">' + entry.text + '</div>' +
                (entry.info ? '<div class="select2-result__info">' + entry.info + '</div>' : '') +
            '</div>'
        );

        return $container;
    }

    function formatRegistrationEntrySelection(entry) {
        return entry.text || entry.id;
    }

    // Function to handle department change
    const departmentSelect = document.getElementById('department');
    const folderSelect = document.getElementById('folder_id');

    departmentSelect.addEventListener('change', function() {
        const selectedDepartment = this.value;

        // Alert user they need to select a folder from the new department
        alert('Please select a folder from the ' + selectedDepartment + ' department.');

        // You could redirect to reload with the new department or handle via AJAX
        window.location.href = '{{ route("documents.edit", $document) }}?department=' + selectedDepartment;
    });
});
</script>
@endpush
