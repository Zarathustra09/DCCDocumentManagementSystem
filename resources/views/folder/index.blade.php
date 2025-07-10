@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-folder"></i> Folders
                        </h5>
                        @can('create folders')
                        <a href="{{ route('folders.create') }}" class="btn btn-primary">
                            <i class="bx bx-folder-plus"></i> Create Folder
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($folders->isEmpty())
                        <div class="text-center py-5">
                            <i class="bx bx-folder-open display-4 text-muted"></i>
                            <h5 class="mt-3 text-muted">No Folders Found</h5>
                            <p class="text-muted">You don't have access to any folders or no folders exist yet.</p>
                            @can('create folders')
                            <a href="{{ route('folders.create') }}" class="btn btn-primary">
                                <i class="bx bx-folder-plus"></i> Create Your First Folder
                            </a>
                            @endcan
                        </div>
                    @else
                        @foreach($folders as $department => $deptFolders)
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="bx bx-buildings"></i>
                                    {{ \App\Models\Folder::DEPARTMENTS[$department] ?? $department }}
                                    <span class="badge bg-secondary ms-2">{{ $deptFolders->count() }}</span>
                                </h6>

                                <div class="row">
                                    @foreach($deptFolders as $folder)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card h-100 folder-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-0">
                                                            <i class="bx bx-folder text-warning"></i>
                                                            {{ $folder->name }}
                                                        </h6>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('folders.show', $folder) }}">
                                                                        <i class="bx bx-show"></i> View
                                                                    </a>
                                                                </li>
                                                                @can('edit folders')
                                                                @if(Auth::user()->can("edit {$folder->department} documents") || Auth::user()->hasRole('admin'))
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('folders.edit', $folder) }}">
                                                                        <i class="bx bx-edit"></i> Edit
                                                                    </a>
                                                                </li>
                                                                @endif
                                                                @endcan
                                                                @can('delete folders')
                                                                @if(Auth::user()->can("delete {$folder->department} documents") || Auth::user()->hasRole('admin'))
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <button class="dropdown-item text-danger delete-folder-btn"
                                                                            data-id="{{ $folder->id }}"
                                                                            data-name="{{ $folder->name }}"
                                                                            data-children="{{ $folder->children->count() }}"
                                                                            data-documents="{{ $folder->documents->count() }}">
                                                                        <i class="bx bx-trash"></i> Delete
                                                                    </button>
                                                                </li>
                                                                @endif
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    @if($folder->description)
                                                        <p class="card-text text-muted small mb-2">{{ Str::limit($folder->description, 100) }}</p>
                                                    @endif

                                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                                        <div>
                                                            <i class="bx bx-folder"></i> {{ $folder->children->count() }} subfolders
                                                        </div>
                                                        <div>
                                                            <i class="bx bx-file"></i> {{ $folder->documents->count() }} documents
                                                        </div>
                                                    </div>

                                                    <div class="mt-2 text-muted small">
                                                        <i class="bx bx-time"></i> {{ $folder->updated_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent">
                                                    <a href="{{ route('folders.show', $folder) }}" class="btn btn-sm btn-outline-primary w-100">
                                                        <i class="bx bx-folder-open"></i> Open Folder
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@can('delete folders')
<!-- Delete Folder Modal -->
<div class="modal fade" id="deleteFolderModal" tabindex="-1" aria-labelledby="deleteFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Folder</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Delete folder "<span id="folder-name" class="fw-medium"></span>"?</p>
                <div id="folder-contents" class="alert alert-warning mt-3" style="display: none;">
                    <i class="bx bx-error-circle"></i> Warning: This folder contains:
                    <ul class="mb-0" id="contents-list"></ul>
                    All these items will be permanently deleted.
                </div>
                <p class="mb-0 small text-danger">This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-folder-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

@push('styles')
<style>
.folder-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #dee2e6;
}

.folder-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.folder-card .card-body {
    padding: 1rem;
}

.folder-card .card-footer {
    padding: 0.75rem 1rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete folder modal functionality
    const folderModal = new bootstrap.Modal(document.getElementById('deleteFolderModal'));

    document.querySelectorAll('.delete-folder-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const folderId = this.dataset.id;
            const folderName = this.dataset.name;
            const childrenCount = parseInt(this.dataset.children) || 0;
            const documentsCount = parseInt(this.dataset.documents) || 0;

            // Set folder name
            document.getElementById('folder-name').textContent = folderName;

            // Set form action
            document.getElementById('delete-folder-form').action = `/folders/${folderId}`;

            // Show/hide contents warning
            const contentsDiv = document.getElementById('folder-contents');
            const contentsList = document.getElementById('contents-list');

            if (childrenCount > 0 || documentsCount > 0) {
                contentsList.innerHTML = '';

                if (childrenCount > 0) {
                    contentsList.innerHTML += `<li>${childrenCount} subfolder(s)</li>`;
                }

                if (documentsCount > 0) {
                    contentsList.innerHTML += `<li>${documentsCount} document(s)</li>`;
                }

                contentsDiv.style.display = 'block';
            } else {
                contentsDiv.style.display = 'none';
            }

            folderModal.show();
        });
    });
});
</script>
@endpush
@endsection
