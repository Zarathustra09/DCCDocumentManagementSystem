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

                       <div class="mb-3">
                           <label for="document_registration_entry_id" class="form-label">Associated Registration Entry</label>
                           <select class="form-select @error('document_registration_entry_id') is-invalid @enderror"
                                   id="document_registration_entry_id"
                                   name="document_registration_entry_id">
                               <option value="">None (Optional)</option>
                               @foreach($registrationEntries as $entry)
                                   <option value="{{ $entry->id }}"
                                       {{ old('document_registration_entry_id', $document->document_registration_entry_id) == $entry->id ? 'selected' : '' }}>
                                       {{ $entry->document_no }} - {{ $entry->document_title }}
                                   </option>
                               @endforeach
                           </select>
                           <div class="form-text">Optionally link this document to an approved registration entry</div>
                           @error('document_registration_entry_id')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                       </div>

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department');
    const folderSelect = document.getElementById('folder_id');

    // Function to handle department change
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
