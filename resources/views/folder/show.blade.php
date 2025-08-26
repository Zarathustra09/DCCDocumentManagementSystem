@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar / Folder Tree -->
        <div class="col-md-3 col-lg-2 d-none d-md-block sidebar" style="min-height: calc(100vh - 60px); background-color: #f8f9fa; border-right: 1px solid #e9ecef;">
            <div class="position-sticky pt-3">

                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ !$folder->parent_id ? 'active text-primary' : 'text-dark' }} drop-zone"
                           href="{{ route('folders.index') }}" data-folder-id="">
                            <i class="bx bxs-home me-2"></i> Home
                        </a>
                    </li>
                    @if($folder->parent)
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center text-dark drop-zone"
                           href="{{ route('folders.show', $folder->parent) }}" data-folder-id="{{ $folder->parent->id }}">
                            <i class="bx bx-arrow-back me-2"></i> Back to {{ $folder->parent->name }}
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="px-3 mb-2 small text-muted text-uppercase" style="letter-spacing: 0.5px;">Stats</div>
                <div class="px-3 mb-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small">Files</span>
                                <span class="badge bg-secondary rounded-pill">{{ $documents->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">Folders</span>
                                <span class="badge bg-secondary rounded-pill">{{ $subfolders->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4" id="main-content">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb" class="d-none d-md-block me-3">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('folders.index') }}" class="text-decoration-none drop-zone" data-folder-id="">
                                    <i class="bx bxs-home"></i>
                                </a>
                            </li>
                            @if($folder->parent)
                                <?php $parents = collect([]); $parent = $folder->parent; ?>
                                @while($parent)
                                    <?php $parents->prepend($parent); $parent = $parent->parent; ?>
                                @endwhile

                                @foreach($parents as $parent)
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('folders.show', $parent) }}" class="text-decoration-none drop-zone" data-folder-id="{{ $parent->id }}">
                                            {{ $parent->name }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                            <li class="breadcrumb-item active" aria-current="page">{{ $folder->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center">
                    <div class="btn-group btn-group-sm me-2">
                        <button type="button" class="btn btn-outline-secondary active" data-view="icons" id="icon-view-btn">
                            <i class="bx bx-grid-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-view="list" id="list-view-btn">
                            <i class="bx bx-list-ul"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="my-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bx bxs-folder text-warning me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0">{{ $folder->name }}</h5>
                </div>

                <div class="action-buttons">
                    @can('create folders')
                        <button class="btn btn-sm btn-outline-primary" onclick="showCreateFolderSwal()">
                            <i class="bx bx-folder-plus"></i> Add
                        </button>
                    @endcan
                        @can('create ' . $folder->baseFolder->name . ' documents')
                            <button class="swal-upload-btn btn btn-sm btn-outline-success">
                                <i class="bx bx-upload"></i> Upload
                            </button>
                        @endcan
                    @can('edit ' . $folder->baseFolder->name . ' documents')
                    <a class="btn btn-sm btn-outline-secondary" onclick="showEditFolderSwal()">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                    @endcan
                </div>
            </div>

            @if($folder->description)
                <p class="text-muted small mb-3">{{ $folder->description }}</p>
            @endif

            <!-- Icon View -->
            <div class="icon-view" id="icon-view">
                <div class="row g-3">
                    @foreach($subfolders as $subfolder)
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <div class="folder-item position-relative rounded shadow-sm drop-zone"
                                 data-folder-id="{{ $subfolder->id }}"
                                 draggable="true"
                                 data-type="folder"
                                 data-id="{{ $subfolder->id }}"
                                 data-name="{{ $subfolder->name }}">
                                <a href="{{ route('folders.show', $subfolder) }}" class="text-decoration-none">
                                    <div class="p-3 d-flex flex-column align-items-center" style="background-color: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.2); border-radius: 0.375rem;">
                                        <i class="bx bxs-folder text-warning" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0 text-truncate w-100 text-center small">{{ $subfolder->name }}</p>
                                    </div>
                                </a>
                                <div class="item-actions position-absolute top-0 end-0 p-1 d-none">
                                    <div class="btn-group btn-group-sm">
                                        @can('edit ' . $subfolder->department . ' documents')
                                        <a href="{{ route('folders.edit', $subfolder) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete ' . $subfolder->department . ' documents')
                                        <button type="button" class="btn btn-light btn-sm rounded-circle delete-btn"
                                               data-id="{{ $subfolder->id }}" data-name="{{ $subfolder->name }}" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @foreach($documents as $document)
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <div class="file-item position-relative rounded shadow-sm"
                                 draggable="true"
                                 data-type="document"
                                 data-id="{{ $document->id }}"
                                 data-name="{{ $document->original_filename }}">
                                <a href="{{ route('documents.show', $document) }}" class="text-decoration-none">
                                    <div class="p-3 d-flex flex-column align-items-center" style="background-color: #fff; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                                        <i class="bx
                                            @if(in_array($document->file_type, ['pdf'])) bxs-file-pdf text-danger
                                            @elseif(in_array($document->file_type, ['doc', 'docx'])) bxs-file-doc text-primary
                                            @elseif(in_array($document->file_type, ['xls', 'xlsx'])) bxs-file-spreadsheet text-success
                                            @elseif(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif'])) bxs-file-image text-info
                                            @else bxs-file text-secondary
                                            @endif
                                        " style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0 text-truncate w-100 text-center small">{{ $document->original_filename }}</p>
                                    </div>
                                </a>
                                <div class="item-actions position-absolute top-0 end-0 p-1 d-none">
                                    <div class="btn-group btn-group-sm">
                                        @can('download ' . $document->folder->department . ' documents')
                                        <a href="{{ route('documents.download', $document) }}" class="btn btn-light btn-sm rounded-circle" title="Download">
                                            <i class="bx bx-download"></i>
                                        </a>
                                        @endcan
                                        @can('edit ' . $document->folder->department . ' documents')
                                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete ' . $document->folder->department . ' documents')
                                        <button type="button" class="btn btn-light btn-sm rounded-circle delete-doc-btn"
                                               data-id="{{ $document->id }}" data-name="{{ $document->original_filename }}" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($subfolders->isEmpty() && $documents->isEmpty())
                        <div class="col-12 text-center py-5">
                            <div class="empty-state p-4 rounded drop-zone" style="background-color: #f8f9fa;" data-folder-id="{{ $folder->id }}">
                                <i class="bx bx-folder-open text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-3 text-muted">This folder is empty</p>
                                <p class="small text-muted mb-3">Drag files here to upload them or move items here</p>
                                <div>
                                    @can('create folders')
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="showCreateFolderSwal()">
                                        <i class="bx bx-folder-plus"></i> Add
                                    </button>
                                    @endcan
                                        @can('create ' . $folder->baseFolder->name . ' documents')
                                            <button class="swal-upload-btn btn btn-sm btn-outline-success">
                                                <i class="bx bx-upload"></i> Upload
                                            </button>
                                        @endcan
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- List View -->
            <div class="list-view d-none" id="list-view">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="folder-table">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Modified</th>
{{--                                <th class="text-end">Actions</th>--}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subfolders as $subfolder)
                                <tr class="drop-zone"
                                    data-folder-id="{{ $subfolder->id }}"
                                    draggable="true"
                                    data-type="folder"
                                    data-id="{{ $subfolder->id }}"
                                    data-name="{{ $subfolder->name }}">
                                    <td style="width: 40%">
                                        <a href="{{ route('folders.show', $subfolder) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <i class="bx bxs-folder text-warning me-2" style="font-size: 1.25rem;"></i>
                                            <span>{{ $subfolder->name }}</span>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark">Folder</span></td>
                                    <td>-</td>
                                    <td><small>{{ $subfolder->updated_at->format('M d, Y') }}</small></td>
{{--                                    <td class="text-end">--}}
{{--                                        <div class="btn-group btn-group-sm">--}}
{{--                                            @can('edit ' . $subfolder->department . ' documents')--}}
{{--                                            <a href="{{ route('folders.edit', $subfolder) }}" class="btn btn-outline-secondary btn-sm" title="Edit">--}}
{{--                                                <i class="bx bx-edit"></i>--}}
{{--                                            </a>--}}
{{--                                            @endcan--}}
{{--                                            @can('delete ' . $subfolder->department . ' documents')--}}
{{--                                            <button type="button" class="btn btn-outline-danger btn-sm delete-btn"--}}
{{--                                                   data-id="{{ $subfolder->id }}" data-name="{{ $subfolder->name }}" title="Delete">--}}
{{--                                                <i class="bx bx-trash"></i>--}}
{{--                                            </button>--}}
{{--                                            @endcan--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
                                </tr>
                            @endforeach

                            @foreach($documents as $document)
                                <tr draggable="true"
                                    data-type="document"
                                    data-id="{{ $document->id }}"
                                    data-name="{{ $document->original_filename }}">
                                    <td style="width: 40%">
                                        <a href="{{ route('documents.show', $document) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <i class="bx
                                                @if(in_array($document->file_type, ['pdf'])) bxs-file-pdf text-danger
                                                @elseif(in_array($document->file_type, ['doc', 'docx'])) bxs-file-doc text-primary
                                                @elseif(in_array($document->file_type, ['xls', 'xlsx'])) bxs-file-spreadsheet text-success
                                                @elseif(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif'])) bxs-file-image text-info
                                                @else bxs-file text-secondary
                                                @endif
                                            me-2" style="font-size: 1.25rem;"></i>
                                            <span>{{ $document->original_filename }}</span>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ strtoupper($document->file_type) }}</span></td>
                                    <td><small>{{ number_format($document->file_size / 1024, 0) }} KB</small></td>
                                    <td><small>{{ $document->updated_at->format('M d, Y') }}</small></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('download ' . $document->folder->department . ' documents')
                                            <a href="{{ route('documents.download', $document) }}" class="btn btn-outline-success btn-sm" title="Download">
                                                <i class="bx bx-download"></i>
                                            </a>
                                            @endcan
                                            @can('edit ' . $document->folder->department . ' documents')
                                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete ' . $document->folder->department . ' documents')
                                            <button type="button" class="btn btn-outline-danger btn-sm delete-doc-btn"
                                                   data-id="{{ $document->id }}" data-name="{{ $document->original_filename }}" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if($subfolders->isEmpty() && $documents->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center py-4 drop-zone" data-folder-id="{{ $folder->id }}">
                                        <i class="bx bx-folder-open text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0 text-muted">This folder is empty</p>
                                        <p class="small text-muted">Drag files here to upload them or move items here</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Drop Overlay -->
<div id="global-drop-overlay" class="global-drop-overlay d-none">
    <div class="drop-message">
        <i class="bx bx-cloud-upload" style="font-size: 4rem;"></i>
        <h3 id="drop-message-text">Drop files to upload to "{{ $folder->name }}"</h3>
        <p class="mb-0">Multiple files supported</p>
    </div>
</div>

<!-- Upload Progress Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uploading Files</h5>
            </div>
            <div class="modal-body">
                <div id="upload-progress-container"></div>
            </div>
            <div class="modal-footer d-none" id="upload-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Folder Modal -->
<div class="modal fade" id="deleteFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Delete folder "<span id="folder-name" class="fw-medium"></span>"?</p>
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

<!-- Delete Document Modal -->
<div class="modal fade" id="deleteDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Delete file "<span id="doc-name" class="fw-medium"></span>"?</p>
                <p class="mb-0 small text-danger">This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-doc-form" action="" method="POST">
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

@push('scripts')
    @include('folder.scripts.moveScript')
    @include('folder.scripts.showScript')
@endpush
@endsection
