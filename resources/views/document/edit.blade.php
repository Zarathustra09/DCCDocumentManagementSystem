@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Document</h5>
                        <div>
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-info me-1">
                                <i class="bx bx-show"></i> View
                            </a>
                            <a href="{{ route('documents.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bx bx-arrow-back"></i> Back to Documents
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

                    <form action="{{ route('documents.update', $document) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">File Name</label>
                            <input type="text" class="form-control" value="{{ $document->original_filename }}" readonly>
                            <div class="form-text">Original filename cannot be changed</div>
                        </div>

                        <div class="mb-3">
                            <label for="folder_id" class="form-label">Folder</label>
                            <select class="form-select @error('folder_id') is-invalid @enderror" id="folder_id" name="folder_id">
                                <option value="">No Folder (Root Level)</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ (old('folder_id', $document->folder_id) == $folder->id) ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('folder_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Moving a document to a different folder will affect its organization</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update Document
                            </button>

                            @can('delete documents')
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bx bx-trash"></i> Delete Document
                            </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bx bx-info-circle"></i> Document Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">File Type</div>
                        <div class="col-md-8">{{ strtoupper($document->file_type) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Size</div>
                        <div class="col-md-8">{{ number_format($document->file_size / 1024, 2) }} KB</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">MIME Type</div>
                        <div class="col-md-8">{{ $document->mime_type }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Uploaded</div>
                        <div class="col-md-8">{{ $document->created_at->format('M d, Y \a\t h:i A') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Last Updated</div>
                        <div class="col-md-8">{{ $document->updated_at->format('M d, Y \a\t h:i A') }}</div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-success">
                            <i class="bx bx-download"></i> Download Original File
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@can('delete documents')
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the document <strong>"{{ $document->original_filename }}"</strong>?</p>
                <p class="mb-0 text-danger fw-bold">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('documents.destroy', $document) }}" method="POST">
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
