@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Folder</h5>
                        <div>
                            <a href="{{ route('folders.show', $folder) }}" class="btn btn-sm btn-info me-1">
                                <i class="bx bx-show"></i> View
                            </a>
                            <a href="{{ route('folders.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bx bx-arrow-back"></i> Back to Folders
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('folders.update', $folder) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Folder Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $folder->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent Folder</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">No Parent (Root Level)</option>
                                @foreach($folders as $parentFolder)
                                    <option value="{{ $parentFolder->id }}" {{ (old('parent_id', $folder->parent_id) == $parentFolder->id) ? 'selected' : '' }}>
                                        {{ $parentFolder->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bx bx-info-circle"></i> Changing the parent folder will move this folder and all its contents to a new location.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $folder->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update Folder
                            </button>

                            @can('delete folders')
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bx bx-trash"></i> Delete Folder
                            </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bx bx-info-circle"></i> Additional Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Created</div>
                        <div class="col-md-8">{{ $folder->created_at->format('M d, Y \a\t h:i A') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Last Updated</div>
                        <div class="col-md-8">{{ $folder->updated_at->format('M d, Y \a\t h:i A') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Subfolders</div>
                        <div class="col-md-8">{{ $folder->children->count() }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Documents</div>
                        <div class="col-md-8">{{ $folder->documents->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@can('delete folders')
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the folder <strong>"{{ $folder->name }}"</strong> and all its contents?</p>
                <p class="mb-0 text-danger fw-bold">This action cannot be undone.</p>

                @if($folder->children->count() > 0 || $folder->documents->count() > 0)
                <div class="alert alert-warning mt-3">
                    <i class="bx bx-error-circle"></i> Warning: This folder contains:
                    <ul class="mb-0">
                        @if($folder->children->count() > 0)
                        <li>{{ $folder->children->count() }} subfolder(s)</li>
                        @endif
                        @if($folder->documents->count() > 0)
                        <li>{{ $folder->documents->count() }} document(s)</li>
                        @endif
                    </ul>
                    All these items will be permanently deleted.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('folders.destroy', $folder) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash"></i> Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection
