@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Upload Document</h5>
                        <a href="javascript:history.back()" class="btn btn-sm btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to Documents
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

                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum file size: 10MB</div>
                        </div>

                        <div class="mb-3">
                            <label for="folder_id" class="form-label">Folder</label>
                            <select class="form-select @error('folder_id') is-invalid @enderror" id="folder_id" name="folder_id">
                                <option value="">No Folder (Root Level)</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ request()->get('folder_id') == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('folder_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                <i class="bx bx-upload"></i> Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bx bx-info-circle"></i> Supported File Types</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-pdf text-danger me-2"></i> PDF Documents
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-doc text-primary me-2"></i> Word Documents (.doc, .docx)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-txt text-secondary me-2"></i> Text Files (.txt)
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-image text-info me-2"></i> Images (.jpg, .png, .gif)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file-spreadsheet text-success me-2"></i> Spreadsheets (.xls, .xlsx)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bx bxs-file text-warning me-2"></i> Other Common Formats
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
