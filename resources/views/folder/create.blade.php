@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Create New Folder</h5>
                        <a href="javascript:history.back()" class="btn btn-sm btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to Folders
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('folders.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Folder Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('department') is-invalid @enderror" id="department" name="department" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $key => $name)
                                    <option value="{{ $key }}" {{ old('department') == $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the department this folder belongs to.</div>
                        </div>

                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent Folder</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">No Parent (Root Level)</option>
                                @foreach($folders->groupBy('department') as $dept => $deptFolders)
                                    <optgroup label="{{ \App\Models\Folder::DEPARTMENTS[$dept] }}">
                                        @foreach($deptFolders as $folder)
                                            <option value="{{ $folder->id }}"
                                                {{ old('parent_id', $currentFolderId ?? '') == $folder->id ? 'selected' : '' }}
                                                data-department="{{ $folder->department }}">
                                                {{ $folder->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select a parent folder or leave empty to create at root level. Parent must be in the same department.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-folder-plus"></i> Create Folder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department');
    const parentSelect = document.getElementById('parent_id');

    departmentSelect.addEventListener('change', function() {
        const selectedDept = this.value;
        const parentOptions = parentSelect.querySelectorAll('option[data-department]');

        // Reset parent selection
        parentSelect.value = '';

        // Show/hide parent options based on department
        parentOptions.forEach(option => {
            if (selectedDept && option.dataset.department !== selectedDept) {
                option.style.display = 'none';
                option.disabled = true;
            } else {
                option.style.display = 'block';
                option.disabled = false;
            }
        });
    });

    // Trigger on page load if department is already selected
    if (departmentSelect.value) {
        departmentSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
