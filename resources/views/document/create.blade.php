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
                                <option value="">Select Department</option>
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
                                  @foreach($folders as $folder)
                                      <option value="{{ $folder->id }}"
                                              data-department="{{ $folder->department }}"
                                              {{ old('folder_id', $currentFolderId) == $folder->id ? 'selected' : '' }}>
                                          {{ $folder->name }} ({{ $folder->department_name }})
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
                                        {{ old('document_registration_entry_id') == $entry->id ? 'selected' : '' }}>
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
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Optional description for this document">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department');
    const folderSelect = document.getElementById('folder_id');

    // Function to filter folders by department
    function filterFoldersByDepartment() {
        const selectedDepartment = departmentSelect.value;
        const folderOptions = folderSelect.querySelectorAll('option');

        // Reset folder selection if department changes
        if (folderSelect.value !== '') {
            const selectedFolder = folderSelect.querySelector('option[value="' + folderSelect.value + '"]');
            if (selectedFolder && selectedFolder.dataset.department !== selectedDepartment) {
                folderSelect.value = '';
            }
        }

        // Show/hide folder options based on department
        folderOptions.forEach(option => {
            if (option.value === '') {
                // Always show "Root" option
                option.style.display = 'block';
            } else if (selectedDepartment === '') {
                // Hide all folders if no department selected
                option.style.display = 'none';
            } else if (option.dataset.department === selectedDepartment) {
                // Show folders that match selected department
                option.style.display = 'block';
            } else {
                // Hide folders that don't match
                option.style.display = 'none';
            }
        });
    }

    // Filter folders when department changes
    departmentSelect.addEventListener('change', filterFoldersByDepartment);

    // Initial filter on page load
    filterFoldersByDepartment();

    // File validation
    const fileInput = document.getElementById('file');
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Check file size (10MB = 10 * 1024 * 1024 bytes)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size exceeds 10MB limit. Please choose a smaller file.');
                this.value = '';
                return;
            }

            // Check file type
            const allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(fileExtension)) {
                alert('File type not supported. Please choose a file with one of these extensions: ' + allowedTypes.join(', '));
                this.value = '';
                return;
            }
        }
    });
});
</script>
@endpush
