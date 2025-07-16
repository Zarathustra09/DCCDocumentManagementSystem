@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upload Document</h5>
                    <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                            <div class="form-text">Supported types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, GIF, XLS, XLSX. Max size: 10MB</div>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('department') is-invalid @enderror" id="department" name="department" required>
                                @foreach($availableDepartments as $key => $name)
                                    <option value="{{ $key }}" {{ old('department', $preselectedDepartment) == $key ? 'selected' : '' }}>
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
                                @foreach($folders->where('department', old('department', $preselectedDepartment)) as $folder)
                                    <option value="{{ $folder->id }}" {{ old('folder_id', $currentFolderId) == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Documents must be placed in a folder</div>
                            @error('folder_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="document_registration_entry_id" class="form-label">Associated Registration Entry</label>
                            <select class="form-select select2-ajax @error('document_registration_entry_id') is-invalid @enderror"
                                    id="document_registration_entry_id"
                                    name="document_registration_entry_id"
                                    data-placeholder="Search for a registration entry..."
                                    data-ajax-url="{{ route('document-registration-entries.search') }}">
                                <option value="">None (Optional)</option>
                                @if(old('document_registration_entry_id'))
                                    @php
                                        $selectedEntry = App\Models\DocumentRegistrationEntry::find(old('document_registration_entry_id'));
                                    @endphp
                                    @if($selectedEntry)
                                        <option value="{{ $selectedEntry->id }}" selected>
                                            {{ $selectedEntry->document_no }} - {{ $selectedEntry->document_title }}
                                        </option>
                                    @endif
                                @endif
                            </select>
                            <div class="form-text">Optionally link this document to an approved registration entry</div>
                            @error('document_registration_entry_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-upload"></i> Upload Document
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

    // Existing folder and department handling
    const departmentSelect = document.getElementById('department');
    const folderSelect = document.getElementById('folder_id');

    departmentSelect.addEventListener('change', function() {
        const selectedDepartment = this.value;

        // Clear and disable folder select first
        folderSelect.innerHTML = '<option value="">Loading folders...</option>';
        folderSelect.disabled = true;

        // Fetch folders for the selected department via AJAX
        fetch(`/api/folders?department=${selectedDepartment}`)
            .then(response => response.json())
            .then(data => {
                folderSelect.innerHTML = '<option value="">Please select a folder</option>';

                data.forEach(folder => {
                    const option = document.createElement('option');
                    option.value = folder.id;
                    option.textContent = folder.name;
                    folderSelect.appendChild(option);
                });

                folderSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching folders:', error);
                folderSelect.innerHTML = '<option value="">Error loading folders</option>';
                folderSelect.disabled = false;
            });
    });
});
</script>
@endpush
