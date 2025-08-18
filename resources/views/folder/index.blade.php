@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar / Folder Tree -->
        <div class="col-md-3 col-lg-2 d-none d-md-block sidebar" style="min-height: calc(100vh - 60px); background-color: #f8f9fa; border-right: 1px solid #e9ecef;">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('folders.index') && !request('base_folder') ? 'active text-primary' : 'text-dark' }} drop-zone"
                           href="{{ route('folders.index') }}"
                           data-folder-id=""
                           data-base-folder-id="">
                            <i class="bx bxs-home me-2"></i> All Main Folders
                        </a>
                    </li>
                </ul>

                <div class="px-3 mb-2 d-flex justify-content-between align-items-center">
                    <span class="small text-muted text-uppercase" style="letter-spacing: 0.5px;">Categories</span>
                    @can('create folders')
                    <button class="btn btn-link btn-sm p-0" onclick="showCreateCategorySwal()" title="Add Category">
                        <i class="bx bx-plus text-primary"></i>
                    </button>
                    @endcan
                </div>

                <ul class="nav flex-column">


                    @foreach($baseFolders as $baseFolder)
                        @can('view ' . $baseFolder->name . ' documents')
                        <li class="nav-item position-relative">
                            <div class="d-flex align-items-center justify-content-between category-item">
                                <a class="nav-link d-flex align-items-center text-dark drop-zone flex-grow-1 {{ request('base_folder') == $baseFolder->id ? 'active text-primary' : '' }}"
                                   href="{{ route('folders.index', ['base_folder' => $baseFolder->id]) }}"
                                   data-folder-id=""
                                   data-base-folder-id="{{ $baseFolder->id }}">
                                    <i class="bx bx-folder me-2 text-warning"></i>
                                    <span class="flex-grow-1">{{ $baseFolder->name }}</span>
                                </a>
                                @can('edit ' . $baseFolder->name . ' documents')
                                <div class="category-actions d-none">
                                    <button class="btn btn-link btn-sm p-0 me-1"
                                            onclick="showEditCategorySwal({{ $baseFolder->id }}, '{{ addslashes($baseFolder->name) }}', '{{ addslashes($baseFolder->description ?? '') }}')"
                                            title="Edit">
                                        <i class="bx bx-edit text-secondary"></i>
                                    </button>
                                    @can('delete ' . $baseFolder->name . ' documents')
                                    <button class="btn btn-link btn-sm p-0"
                                            onclick="deleteCategory({{ $baseFolder->id }}, '{{ addslashes($baseFolder->name) }}')"
                                            title="Delete">
                                        <i class="bx bx-trash text-danger"></i>
                                    </button>
                                    @endcan
                                </div>
                                @endcan
                            </div>
                        </li>
                        @endcan
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4" id="main-content">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0">
                        @if($selectedBaseFolder)
                            {{ $selectedBaseFolder->name }}
                        @else
                            All Documents
                        @endif
                    </h4>
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
                    <i class="bx bxs-folder-open text-primary me-2" style="font-size: 1.5rem;"></i>
                    <span class="text-muted">
                        @if($selectedBaseFolder)
                            {{ $selectedBaseFolder->description ?? 'No description' }}
                        @else
                            Browse all documents and folders
                        @endif
                    </span>
                </div>

                <div class="action-buttons">
                   @if($selectedBaseFolder)
                       @can('create ' . $selectedBaseFolder->name . ' documents')
                           <button class="btn btn-sm btn-outline-primary" onclick="showCreateFolderSwal()">
                               <i class="bx bx-folder-plus"></i> Add Folder
                           </button>
                       @endcan
                   @endif
                </div>
            </div>

            <!-- Icon View -->
            <div class="icon-view" id="icon-view">
                <div class="row g-3">
                    @foreach($folders as $folder)
                        @can('view ' . $folder->baseFolder->name . ' documents')
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <div class="folder-item position-relative rounded shadow-sm drop-zone"
                                 data-folder-id="{{ $folder->id }}"
                                 data-base-folder-id="{{ $folder->base_folder_id }}"
                                 draggable="true"
                                 data-type="folder"
                                 data-id="{{ $folder->id }}"
                                 data-name="{{ $folder->name }}">
                                <a href="{{ route('folders.show', $folder) }}" class="text-decoration-none">
                                    <div class="p-3 d-flex flex-column align-items-center" style="background-color: rgba(255, 193, 7, 0.05); border: 1px solid rgba(255, 193, 7, 0.2); border-radius: 0.375rem;">
                                        <i class="bx bxs-folder text-warning" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0 text-truncate w-100 text-center small">{{ $folder->name }}</p>
                                    </div>
                                </a>
                                <div class="item-actions position-absolute top-0 end-0 p-1 d-none">
                                    <div class="btn-group btn-group-sm">
                                        @can('edit ' . $folder->baseFolder->name . ' documents')
                                        <a href="{{ route('folders.edit', $folder) }}" class="btn btn-light btn-sm rounded-circle" title="Edit">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete ' . $folder->baseFolder->name . ' documents')
                                        <button type="button" class="btn btn-light btn-sm rounded-circle delete-btn"
                                               data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan
                    @endforeach

                    @if($folders->isEmpty() || $folders->filter(fn($folder) => auth()->user()->can('view ' . $folder->baseFolder->name . ' documents'))->isEmpty())
                        <div class="col-12 text-center py-5">
                            <div class="empty-state p-4 rounded drop-zone" style="background-color: #f8f9fa;"
                                 data-folder-id=""
                                 data-base-folder-id="{{ $selectedBaseFolder->id ?? '' }}">
                                <i class="bx bx-folder-open text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-3 text-muted">No folders found</p>
                                <p class="small text-muted mb-3">
                                    @if($selectedBaseFolder && auth()->user()->can('create ' . $selectedBaseFolder->name . ' documents'))
                                        Create your first folder to organize documents
                                    @else
                                        No accessible folders available
                                    @endif
                                </p>
                                <div>
                                    @if($selectedBaseFolder)
                                        @can('create ' . $selectedBaseFolder->name . ' documents')
                                        <button class="btn btn-sm btn-outline-primary me-2" onclick="showCreateFolderSwal()">
                                            <i class="bx bx-folder-plus"></i> Add Folder
                                        </button>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- List View -->
            <div class="list-view d-none" id="list-view">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Modified</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($folders as $folder)
                                @can('view ' . $folder->baseFolder->name . ' documents')
                                <tr class="drop-zone"
                                    data-folder-id="{{ $folder->id }}"
                                    data-base-folder-id="{{ $folder->base_folder_id }}"
                                    draggable="true"
                                    data-type="folder"
                                    data-id="{{ $folder->id }}"
                                    data-name="{{ $folder->name }}">
                                    <td>
                                        <a href="{{ route('folders.show', $folder) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <i class="bx bxs-folder text-warning me-2" style="font-size: 1.25rem;"></i>
                                            <span>{{ $folder->name }}</span>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ $folder->baseFolder->name }}</span></td>
                                    <td><small>{{ $folder->updated_at->format('M d, Y') }}</small></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('edit ' . $folder->baseFolder->name . ' documents')
                                            <a href="{{ route('folders.edit', $folder) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @endcan
                                            @can('delete ' . $folder->baseFolder->name . ' documents')
                                            <button type="button" class="btn btn-outline-danger btn-sm delete-btn"
                                                   data-id="{{ $folder->id }}" data-name="{{ $folder->name }}" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endcan
                            @endforeach

                            @if($folders->isEmpty() || $folders->filter(fn($folder) => auth()->user()->can('view ' . $folder->baseFolder->name . ' documents'))->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="bx bx-folder-open text-muted" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0 text-muted">No folders found</p>
                                        <p class="small text-muted">
                                            @if($selectedBaseFolder && auth()->user()->can('create ' . $selectedBaseFolder->name . ' documents'))
                                                Create your first folder to get started
                                            @else
                                                No accessible folders available
                                            @endif
                                        </p>
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

<style>
.category-item:hover {
    background-color: rgba(0,0,0,0.05);
    border-radius: 0.375rem;
}

.sidebar .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 0.375rem;
}

.sidebar .nav-link.active {
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 0.375rem;
}
</style>

@push('scripts')
    @include('folder.scripts.indexScript')
@endpush
@endsection
