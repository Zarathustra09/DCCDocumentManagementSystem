@extends('layouts.app')

@section('content')
    <div class="container-xxl pt-4">
        <div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 0 15px;">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">
                                <i class="bx bx-folder-open text-primary me-2"></i>
                                Document Folders
                            </h2>
                            <p class="text-muted mb-0">Organize and manage your top-level document folders by category</p>
                        </div>
                        @can('create folders')
                            <div>
                                <a href="{{ route('folders.create') }}" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="bx bx-plus-circle me-2"></i>
                                    Folder
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Filters and View Toggle -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <!-- Base Folder Filter -->
                                <div class="d-flex align-items-center gap-3">
                                    <div class="filter-group">
                                        <label class="form-label small text-muted mb-1">Category</label>
                                        <select class="form-select form-select-sm" id="departmentFilter">
                                            <option value="">All Categories</option>
                                            @foreach($baseFolders as $baseFolder)
                                                <option value="{{ $baseFolder->id }}">{{ $baseFolder->name }}</option>
                                            @endforeach
                                            @if($orphanedFolders->count() > 0)
                                                <option value="orphaned">Uncategorized</option>
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Collapse/Expand Controls -->
                                    <div class="collapse-controls">
                                        <label class="form-label small text-muted mb-1">Sections</label>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="collapseAllBtn">
                                                <i class="bx bx-collapse-vertical"></i> Collapse All
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="expandAllBtn">
                                                <i class="bx bx-expand-vertical"></i> Expand All
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- View Toggle -->
                                <div class="view-toggle">
                                    <label class="form-label small text-muted mb-1">View</label>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary btn-sm view-btn active" data-view="compact">
                                            <i class="bx bx-grid-alt"></i> Compact
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm view-btn" data-view="detailed">
                                            <i class="bx bx-list-ul"></i> Detailed
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Alert -->
            @if (session('success'))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <i class="bx bx-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="row">
                <div class="col-12">
                    @if($baseFolders->isEmpty() && $orphanedFolders->isEmpty())
                        <!-- Empty State -->
                        <div class="empty-state text-center py-5">
                            <div class="empty-state-icon mb-4">
                                <i class="bx bx-folder-open display-1 text-muted"></i>
                            </div>
                            <h3 class="text-muted mb-3">No Folders Yet</h3>
                            <p class="text-muted fs-5 mb-4">
                                Start organizing your documents by creating your first folder
                            </p>
                            @can('create folders')
                                <a href="{{ route('folders.create') }}" class="btn btn-primary btn-lg">
                                    <i class="bx bx-folder-plus me-2"></i>
                                    Create Your First Folder
                                </a>
                            @endcan
                        </div>
                    @else
                        <!-- Compact View (Default) -->
                        <div id="compactView" class="folders-container">
                            @foreach($baseFolders as $baseFolder)
                                @if($baseFolder->folders->count() > 0)
                                    <div class="department-section mb-4" data-department="{{ $baseFolder->id }}">
                                        <!-- Collapsible Base Folder Header -->
                                        <div class="department-header-compact mb-3" data-bs-toggle="collapse" data-bs-target="#dept-{{ $baseFolder->id }}" aria-expanded="true">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="bx bx-chevron-down collapse-icon me-2"></i>
                                                    <i class="bx bx-category text-primary me-2"></i>
                                                    <h5 class="mb-0 fw-bold">{{ $baseFolder->name }}</h5>
                                                    <span class="badge bg-primary-subtle text-primary ms-2">
                                                {{ $baseFolder->folders->count() }}
                                            </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Collapsible Folders Grid -->
                                        <div class="collapse show" id="dept-{{ $baseFolder->id }}">
                                            <div class="row g-3">
                                                @foreach($baseFolder->folders as $folder)
                                                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 folder-item" data-name="{{ strtolower($folder->name) }}">
                                                        <div class="folder-card-compact h-100">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <div class="folder-icon-small">
                                                                        <i class="bx bx-folder text-warning fs-2"></i>
                                                                    </div>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-ghost btn-sm" type="button" data-bs-toggle="dropdown">
                                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                                                            <li>
                                                                                <a class="dropdown-item" href="{{ route('folders.show', $folder) }}">
                                                                                    <i class="bx bx-show me-2"></i>View
                                                                                </a>
                                                                            </li>
                                                                            @can('edit folders')
                                                                                <li>
                                                                                    <a class="dropdown-item" href="{{ route('folders.edit', $folder) }}">
                                                                                        <i class="bx bx-edit me-2"></i>Edit
                                                                                    </a>
                                                                                </li>
                                                                            @endcan
                                                                            @can('delete folders')
                                                                                <li><hr class="dropdown-divider"></li>
                                                                                <li>
                                                                                    <button class="dropdown-item text-danger delete-folder-btn"
                                                                                            data-id="{{ $folder->id }}"
                                                                                            data-name="{{ $folder->name }}"
                                                                                            data-children="{{ $folder->children->count() }}"
                                                                                            data-documents="{{ $folder->documents->count() }}">
                                                                                        <i class="bx bx-trash me-2"></i>Delete
                                                                                    </button>
                                                                                </li>
                                                                            @endcan
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <h6 class="folder-title-compact fw-bold mb-2" title="{{ $folder->name }}">
                                                                    {{ Str::limit($folder->name, 20) }}
                                                                </h6>

                                                                <div class="folder-stats-compact mb-2">
                                                                    <div class="d-flex justify-content-between text-muted small">
                                                                        <span><i class="bx bx-folder"></i> {{ $folder->children->count() }}</span>
                                                                        <span><i class="bx bx-file"></i> {{ $folder->documents->count() }}</span>
                                                                    </div>
                                                                </div>

                                                                <button class="btn btn-primary btn-sm w-100" onclick="window.location.href='{{ route('folders.show', $folder) }}'">
                                                                    <i class="bx bx-folder-open me-1"></i>Open
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Orphaned Folders (folders without base_folder_id) -->
                            @if($orphanedFolders->count() > 0)
                                <div class="department-section mb-4" data-department="orphaned">
                                    <div class="department-header-compact mb-3" data-bs-toggle="collapse" data-bs-target="#dept-orphaned" aria-expanded="true">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-chevron-down collapse-icon me-2"></i>
                                                <i class="bx bx-folder-open text-secondary me-2"></i>
                                                <h5 class="mb-0 fw-bold text-secondary">Uncategorized</h5>
                                                <span class="badge bg-secondary-subtle text-secondary ms-2">
                                            {{ $orphanedFolders->count() }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="collapse show" id="dept-orphaned">
                                        <div class="row g-3">
                                            @foreach($orphanedFolders as $folder)
                                                <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 folder-item" data-name="{{ strtolower($folder->name) }}">
                                                    <div class="folder-card-compact h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <div class="folder-icon-small">
                                                                    <i class="bx bx-folder text-warning fs-2"></i>
                                                                </div>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-ghost btn-sm" type="button" data-bs-toggle="dropdown">
                                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                                        <li>
                                                                            <a class="dropdown-item" href="{{ route('folders.show', $folder) }}">
                                                                                <i class="bx bx-show me-2"></i>View
                                                                            </a>
                                                                        </li>
                                                                        @can('edit folders')
                                                                            <li>
                                                                                <a class="dropdown-item" href="{{ route('folders.edit', $folder) }}">
                                                                                    <i class="bx bx-edit me-2"></i>Edit
                                                                                </a>
                                                                            </li>
                                                                        @endcan
                                                                        @can('delete folders')
                                                                            <li><hr class="dropdown-divider"></li>
                                                                            <li>
                                                                                <button class="dropdown-item text-danger delete-folder-btn"
                                                                                        data-id="{{ $folder->id }}"
                                                                                        data-name="{{ $folder->name }}"
                                                                                        data-children="{{ $folder->children->count() }}"
                                                                                        data-documents="{{ $folder->documents->count() }}">
                                                                                    <i class="bx bx-trash me-2"></i>Delete
                                                                                </button>
                                                                            </li>
                                                                        @endcan
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                            <h6 class="folder-title-compact fw-bold mb-2" title="{{ $folder->name }}">
                                                                {{ Str::limit($folder->name, 20) }}
                                                            </h6>

                                                            <div class="folder-stats-compact mb-2">
                                                                <div class="d-flex justify-content-between text-muted small">
                                                                    <span><i class="bx bx-folder"></i> {{ $folder->children->count() }}</span>
                                                                    <span><i class="bx bx-file"></i> {{ $folder->documents->count() }}</span>
                                                                </div>
                                                            </div>

                                                            <button class="btn btn-primary btn-sm w-100" onclick="window.location.href='{{ route('folders.show', $folder) }}'">
                                                                <i class="bx bx-folder-open me-1"></i>Open
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Detailed View (Hidden by default) -->
                        <div id="detailedView" class="folders-container" style="display: none;">
                            @foreach($baseFolders as $baseFolder)
                                @if($baseFolder->folders->count() > 0)
                                    <div class="department-section mb-4" data-department="{{ $baseFolder->id }}">
                                        <div class="department-header mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="department-icon me-3">
                                                    <i class="bx bx-category fs-3 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h4 class="fw-bold mb-1">{{ $baseFolder->name }}</h4>
                                                    @if($baseFolder->description)
                                                        <p class="text-muted mb-1">{{ $baseFolder->description }}</p>
                                                    @endif
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                                                {{ $baseFolder->folders->count() }} {{ Str::plural('folder', $baseFolder->folders->count()) }}
                                            </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-4">
                                            @foreach($baseFolder->folders as $folder)
                                                <div class="col-xl-4 col-lg-6 folder-item" data-name="{{ strtolower($folder->name) }}">
                                                    <div class="folder-card h-100 position-relative">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                                <div class="folder-icon">
                                                                    <i class="bx bx-folder fs-1 text-warning"></i>
                                                                </div>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-ghost btn-sm" type="button" data-bs-toggle="dropdown">
                                                                        <i class="bx bx-dots-vertical-rounded fs-5"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                                        <li>
                                                                            <a class="dropdown-item" href="{{ route('folders.show', $folder) }}">
                                                                                <i class="bx bx-show me-2"></i>View Folder
                                                                            </a>
                                                                        </li>
                                                                        @can('edit folders')
                                                                            <li>
                                                                                <a class="dropdown-item" href="{{ route('folders.edit', $folder) }}">
                                                                                    <i class="bx bx-edit me-2"></i>Edit Folder
                                                                                </a>
                                                                            </li>
                                                                        @endcan
                                                                        @can('delete folders')
                                                                            <li><hr class="dropdown-divider"></li>
                                                                            <li>
                                                                                <button class="dropdown-item text-danger delete-folder-btn"
                                                                                        data-id="{{ $folder->id }}"
                                                                                        data-name="{{ $folder->name }}"
                                                                                        data-children="{{ $folder->children->count() }}"
                                                                                        data-documents="{{ $folder->documents->count() }}">
                                                                                    <i class="bx bx-trash me-2"></i>Delete Folder
                                                                                </button>
                                                                            </li>
                                                                        @endcan
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                            <div class="folder-info">
                                                                <h5 class="folder-title fw-bold mb-2">{{ $folder->name }}</h5>

                                                                @if($folder->description)
                                                                    <p class="folder-description text-muted small mb-3">
                                                                        {{ Str::limit($folder->description, 80) }}
                                                                    </p>
                                                                @endif

                                                                <div class="folder-stats mb-3">
                                                                    <div class="row g-2">
                                                                        <div class="col-6">
                                                                            <div class="stat-item text-center p-2 bg-light rounded">
                                                                                <i class="bx bx-folder text-primary"></i>
                                                                                <div class="fw-bold">{{ $folder->children->count() }}</div>
                                                                                <div class="small text-muted">Subfolders</div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div class="stat-item text-center p-2 bg-light rounded">
                                                                                <i class="bx bx-file text-success"></i>
                                                                                <div class="fw-bold">{{ $folder->documents->count() }}</div>
                                                                                <div class="small text-muted">Documents</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="folder-meta text-muted small">
                                                                    <i class="bx bx-time-five me-1"></i>
                                                                    Updated {{ $folder->updated_at->diffForHumans() }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card-footer bg-transparent border-0 p-4 pt-0">
                                                            <a href="{{ route('folders.show', $folder) }}" class="btn btn-primary w-100">
                                                                <i class="bx bx-folder-open me-2"></i>
                                                                Open Folder
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Orphaned Folders in Detailed View -->
                            @if($orphanedFolders->count() > 0)
                                <div class="department-section mb-4" data-department="orphaned">
                                    <div class="department-header mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="department-icon me-3">
                                                <i class="bx bx-folder-open fs-3 text-secondary"></i>
                                            </div>
                                            <div>
                                                <h4 class="fw-bold mb-1 text-secondary">Uncategorized</h4>
                                                <p class="text-muted mb-1">Folders not assigned to any category</p>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                            {{ $orphanedFolders->count() }} {{ Str::plural('folder', $orphanedFolders->count()) }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        @foreach($orphanedFolders as $folder)
                                            <div class="col-xl-4 col-lg-6 folder-item" data-name="{{ strtolower($folder->name) }}">
                                                <div class="folder-card h-100 position-relative">
                                                    <div class="card-body p-4">
                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <div class="folder-icon">
                                                                <i class="bx bx-folder fs-1 text-warning"></i>
                                                            </div>
                                                            <div class="dropdown">
                                                                <button class="btn btn-ghost btn-sm" type="button" data-bs-toggle="dropdown">
                                                                    <i class="bx bx-dots-vertical-rounded fs-5"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('folders.show', $folder) }}">
                                                                            <i class="bx bx-show me-2"></i>View Folder
                                                                        </a>
                                                                    </li>
                                                                    @can('edit folders')
                                                                        <li>
                                                                            <a class="dropdown-item" href="{{ route('folders.edit', $folder) }}">
                                                                                <i class="bx bx-edit me-2"></i>Edit Folder
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('delete folders')
                                                                        <li><hr class="dropdown-divider"></li>
                                                                        <li>
                                                                            <button class="dropdown-item text-danger delete-folder-btn"
                                                                                    data-id="{{ $folder->id }}"
                                                                                    data-name="{{ $folder->name }}"
                                                                                    data-children="{{ $folder->children->count() }}"
                                                                                    data-documents="{{ $folder->documents->count() }}">
                                                                                <i class="bx bx-trash me-2"></i>Delete Folder
                                                                            </button>
                                                                        </li>
                                                                    @endcan
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="folder-info">
                                                            <h5 class="folder-title fw-bold mb-2">{{ $folder->name }}</h5>

                                                            @if($folder->description)
                                                                <p class="folder-description text-muted small mb-3">
                                                                    {{ Str::limit($folder->description, 80) }}
                                                                </p>
                                                            @endif

                                                            <div class="folder-stats mb-3">
                                                                <div class="row g-2">
                                                                    <div class="col-6">
                                                                        <div class="stat-item text-center p-2 bg-light rounded">
                                                                            <i class="bx bx-folder text-primary"></i>
                                                                            <div class="fw-bold">{{ $folder->children->count() }}</div>
                                                                            <div class="small text-muted">Subfolders</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="stat-item text-center p-2 bg-light rounded">
                                                                            <i class="bx bx-file text-success"></i>
                                                                            <div class="fw-bold">{{ $folder->documents->count() }}</div>
                                                                            <div class="small text-muted">Documents</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="folder-meta text-muted small">
                                                                <i class="bx bx-time-five me-1"></i>
                                                                Updated {{ $folder->updated_at->diffForHumans() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                                                        <a href="{{ route('folders.show', $folder) }}" class="btn btn-primary w-100">
                                                            <i class="bx bx-folder-open me-2"></i>
                                                            Open Folder
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    @can('delete folders')
        <div class="modal fade" id="deleteFolderModal" tabindex="-1" aria-labelledby="deleteFolderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title">
                            <i class="bx bx-trash me-2"></i>
                            Delete Folder
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <i class="bx bx-error-circle text-danger display-4 mb-3"></i>
                            <h6 class="mb-3">Are you sure you want to delete this folder?</h6>
                            <div class="folder-name-display bg-light p-3 rounded mb-3">
                                <strong id="folder-name"></strong>
                            </div>
                        </div>

                        <div id="folder-contents" class="alert alert-warning border-0" style="display: none;">
                            <div class="d-flex align-items-start">
                                <i class="bx bx-error-circle text-warning me-2 mt-1"></i>
                                <div>
                                    <strong>Warning:</strong> This folder contains:
                                    <ul class="mb-0 mt-2" id="contents-list"></ul>
                                    <small class="text-muted d-block mt-2">All contents will be permanently deleted.</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-danger border-0 mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            <small><strong>This action cannot be undone.</strong></small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bx bx-x me-2"></i>Cancel
                        </button>
                        <form id="delete-folder-form" action="" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bx bx-trash me-2"></i>Delete Forever
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @push('styles')
        <style>
            .container-fluid {
                max-width: 1400px;
            }

            /* Compact Department Header */
            .department-header-compact {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 8px;
                padding: 1rem 1.25rem;
                border-left: 4px solid var(--bs-primary);
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .department-header-compact:hover {
                background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            }

            .collapse-icon {
                transition: transform 0.2s ease;
            }

            .collapsed .collapse-icon {
                transform: rotate(-90deg);
            }

            /* Compact Folder Cards */
            .folder-card-compact {
                background: white;
                border: 1px solid #e9ecef;
                border-radius: 12px;
                transition: all 0.2s ease;
                height: 100%;
            }

            .folder-card-compact:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                border-color: var(--bs-primary);
            }

            .folder-icon-small {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .folder-title-compact {
                color: #2c3e50;
                font-size: 0.9rem;
                line-height: 1.2;
            }

            .folder-stats-compact {
                border-top: 1px solid #f1f3f4;
                padding-top: 0.5rem;
            }

            /* Filter Controls */
            .filter-group {
                min-width: 150px;
            }

            .view-toggle {
                min-width: 120px;
            }

            .view-btn.active {
                background-color: var(--bs-primary);
                color: white;
                border-color: var(--bs-primary);
            }

            .collapse-controls {
                min-width: 180px;
            }

            .collapse-controls .btn-group {
                width: 100%;
            }

            .collapse-controls .btn {
                flex: 1;
                font-size: 0.75rem;
            }

            /* Detailed view styles */
            .folder-card {
                background: white;
                border: none;
                border-radius: 16px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
            }

            .folder-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            }

            .folder-card .card-body {
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            }

            .folder-icon {
                width: 60px;
                height: 60px;
                background: rgba(255, 193, 7, 0.1);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-ghost {
                background: transparent;
                border: none;
                color: #6c757d;
                padding: 4px 8px;
                border-radius: 6px;
                transition: all 0.2s ease;
            }

            .btn-ghost:hover {
                background: #f8f9fa;
                color: #495057;
            }

            .department-header {
                padding: 20px;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 16px;
                border: 1px solid #dee2e6;
                margin-bottom: 20px;
            }

            .stat-item {
                transition: all 0.3s ease;
            }

            .stat-item:hover {
                background: #e9ecef !important;
                transform: scale(1.05);
            }

            .empty-state {
                padding: 60px 20px;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .col-xxl-2 {
                    flex: 0 0 50%;
                    max-width: 50%;
                }

                .filter-group {
                    min-width: 120px;
                }

                .collapse-controls {
                    min-width: 160px;
                }

                .collapse-controls .btn {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.7rem;
                }

                .d-flex.flex-wrap.gap-3 {
                    gap: 1rem !important;
                }
            }

            @media (max-width: 576px) {
                .col-xxl-2 {
                    flex: 0 0 100%;
                    max-width: 100%;
                }
            }

            .folders-container {
                transition: opacity 0.3s ease;
            }

            .folder-item {
                animation: fadeInUp 0.4s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // View toggle functionality
                const viewButtons = document.querySelectorAll('.view-btn');
                const compactView = document.getElementById('compactView');
                const detailedView = document.getElementById('detailedView');

                viewButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const view = this.dataset.view;

                        viewButtons.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        if (view === 'compact') {
                            compactView.style.display = 'block';
                            detailedView.style.display = 'none';
                        } else {
                            compactView.style.display = 'none';
                            detailedView.style.display = 'block';
                        }
                    });
                });

                // Collapse/Expand All functionality
                const collapseAllBtn = document.getElementById('collapseAllBtn');
                const expandAllBtn = document.getElementById('expandAllBtn');

                collapseAllBtn.addEventListener('click', function() {
                    const collapseElements = document.querySelectorAll('.department-section .collapse');
                    const headers = document.querySelectorAll('.department-header-compact');

                    collapseElements.forEach(collapse => {
                        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapse);
                        bsCollapse.hide();
                    });

                    headers.forEach(header => {
                        header.classList.add('collapsed');
                        header.setAttribute('aria-expanded', 'false');
                    });
                });

                expandAllBtn.addEventListener('click', function() {
                    const collapseElements = document.querySelectorAll('.department-section .collapse');
                    const headers = document.querySelectorAll('.department-header-compact');

                    collapseElements.forEach(collapse => {
                        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapse);
                        bsCollapse.show();
                    });

                    headers.forEach(header => {
                        header.classList.remove('collapsed');
                        header.setAttribute('aria-expanded', 'true');
                    });
                });

                // Category filter (updated for base folders)
                const departmentFilter = document.getElementById('departmentFilter');
                departmentFilter.addEventListener('change', function() {
                    const selectedCategory = this.value;
                    const departmentSections = document.querySelectorAll('.department-section');

                    departmentSections.forEach(section => {
                        if (!selectedCategory || section.dataset.department === selectedCategory) {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                });

                // Delete modal functionality
                const folderModal = new bootstrap.Modal(document.getElementById('deleteFolderModal'));

                document.querySelectorAll('.delete-folder-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const folderId = this.dataset.id;
                        const folderName = this.dataset.name;
                        const childrenCount = parseInt(this.dataset.children) || 0;
                        const documentsCount = parseInt(this.dataset.documents) || 0;

                        document.getElementById('folder-name').textContent = folderName;
                        document.getElementById('delete-folder-form').action = `/folders/${folderId}`;

                        const contentsDiv = document.getElementById('folder-contents');
                        const contentsList = document.getElementById('contents-list');

                        if (childrenCount > 0 || documentsCount > 0) {
                            contentsList.innerHTML = '';

                            if (childrenCount > 0) {
                                contentsList.innerHTML += `<li><i class="bx bx-folder me-1"></i>${childrenCount} subfolder${childrenCount !== 1 ? 's' : ''}</li>`;
                            }

                            if (documentsCount > 0) {
                                contentsList.innerHTML += `<li><i class="bx bx-file me-1"></i>${documentsCount} document${documentsCount !== 1 ? 's' : ''}</li>`;
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
