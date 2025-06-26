@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bx bxs-folder text-warning me-2"></i>
                            <h5 class="mb-0">{{ $folder->name }}</h5>
                        </div>
                        <div>
                            @can('edit folders')
                            <a href="{{ route('folders.edit', $folder) }}" class="btn btn-sm btn-primary me-1">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                            @endcan
                            <a href="{{ route('folders.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bx bx-arrow-back"></i> Back
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

                    <div class="mb-4">
                        <h6><i class="bx bx-info-circle"></i> Folder Details</h6>
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-bold">Description</div>
                                    <div class="col-md-9">{{ $folder->description ?? 'No description' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-bold">Created</div>
                                    <div class="col-md-9">{{ $folder->created_at->format('M d, Y \a\t h:i A') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 fw-bold">Updated</div>
                                    <div class="col-md-9">{{ $folder->updated_at->format('M d, Y \a\t h:i A') }}</div>
                                </div>
                                @if($folder->parent)
                                <div class="row mt-2">
                                    <div class="col-md-3 fw-bold">Parent Folder</div>
                                    <div class="col-md-9">
                                        <a href="{{ route('folders.show', $folder->parent) }}">
                                            <i class="bx bxs-folder text-warning"></i> {{ $folder->parent->name }}
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6><i class="bx bx-folder"></i> Subfolders ({{ $subfolders->count() }})</h6>
                            @can('create folders')
                            <a href="{{ route('folders.create') }}?parent_id={{ $folder->id }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-folder-plus"></i> New Subfolder
                            </a>
                            @endcan
                        </div>

                        @if($subfolders->isEmpty())
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-0">No subfolders found</p>
                            </div>
                        @else
                            <div class="list-group">
                                @foreach($subfolders as $subfolder)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <a href="{{ route('folders.show', $subfolder) }}" class="text-decoration-none d-flex align-items-center">
                                        <i class="bx bxs-folder text-warning me-2"></i>
                                        {{ $subfolder->name }}
                                        <span class="badge bg-secondary rounded-pill ms-2">{{ $subfolder->documents->count() }}</span>
                                    </a>
                                    <div class="btn-group" role="group">
                                        @can('edit folders')
                                        <a href="{{ route('folders.edit', $subfolder) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete folders')
                                        <form action="{{ route('folders.destroy', $subfolder) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this folder and all its contents?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6><i class="bx bx-file"></i> Documents ({{ $documents->count() }})</h6>
                            @can('create documents')
                            <a href="{{ route('documents.create') }}?folder_id={{ $folder->id }}" class="btn btn-sm btn-outline-success">
                                <i class="bx bx-upload"></i> Upload Document
                            </a>
                            @endcan
                        </div>

                        @if($documents->isEmpty())
                            <div class="text-center p-3 bg-light rounded">
                                <p class="text-muted mb-0">No documents found</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Uploaded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($documents as $document)
                                        <tr>
                                            <td>
                                                <a href="{{ route('documents.show', $document) }}" class="d-flex align-items-center">
                                                    <i class="bx bxs-file me-2
                                                        @if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif'])) text-info
                                                        @elseif(in_array($document->file_type, ['pdf'])) text-danger
                                                        @elseif(in_array($document->file_type, ['doc', 'docx'])) text-primary
                                                        @elseif(in_array($document->file_type, ['xls', 'xlsx'])) text-success
                                                        @elseif(in_array($document->file_type, ['ppt', 'pptx'])) text-warning
                                                        @else text-secondary
                                                        @endif
                                                    "></i>
                                                    {{ $document->original_filename }}
                                                </a>
                                            </td>
                                            <td>{{ strtoupper($document->file_type) }}</td>
                                            <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-info">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    @can('download documents')
                                                    <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-success">
                                                        <i class="bx bx-download"></i>
                                                    </a>
                                                    @endcan
                                                    @can('edit documents')
                                                    <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-primary">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    @endcan
                                                    @can('delete documents')
                                                    <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
