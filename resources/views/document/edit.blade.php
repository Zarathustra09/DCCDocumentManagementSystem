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
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                        </div>

                        <!-- Keep existing TinyMCE editor section -->

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

<!-- Keep existing delete modal -->
@endsection

<!-- Keep existing styles and scripts -->
