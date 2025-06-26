@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar / Document Info -->
        <div class="col-md-3 col-lg-2 d-none d-md-block sidebar" style="min-height: calc(100vh - 60px); background-color: #f8f9fa; border-right: 1px solid #e9ecef;">
            <div class="position-sticky pt-3">
                <div class="px-3 mb-4 d-flex align-items-center">
                    <i class="bx bxs-file text-primary" style="font-size: 1.25rem;"></i>
                    <span class="ms-2 fw-medium">Document</span>
                </div>

                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center text-dark" href="{{ route('folders.index') }}">
                            <i class="bx bxs-home me-2"></i> Home
                        </a>
                    </li>
                    @if($document->folder)
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center text-dark" href="{{ route('folders.show', $document->folder) }}">
                            <i class="bx bx-arrow-back me-2"></i> Back
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="px-3 mb-2 small text-muted text-uppercase" style="letter-spacing: 0.5px;">Details</div>
                <div class="px-3 mb-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body py-2 px-3">
                            <div class="mb-2">
                                <span class="small d-block text-muted">Type</span>
                                <span class="badge bg-secondary">{{ strtoupper($document->file_type) }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="small d-block text-muted">Size</span>
                                <span>{{ number_format($document->file_size / 1024, 1) }} KB</span>
                            </div>
                            <div class="mb-2">
                                <span class="small d-block text-muted">Created</span>
                                <span>{{ $document->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="mb-0">
                                <span class="small d-block text-muted">Modified</span>
                                <span>{{ $document->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb" class="d-none d-md-block me-3">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('folders.index') }}" class="text-decoration-none"><i class="bx bxs-home"></i></a>
                            </li>
                            @if($document->folder)
                                @php
                                    $parents = collect([]);
                                    $parent = $document->folder;
                                    while($parent) {
                                        $parents->prepend($parent);
                                        $parent = $parent->parent;
                                    }
                                @endphp

                                @foreach($parents as $parent)
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('folders.show', $parent) }}" class="text-decoration-none">{{ $parent->name }}</a>
                                    </li>
                                @endforeach
                            @endif
                            <li class="breadcrumb-item active" aria-current="page">{{ $document->original_filename }}</li>
                        </ol>
                    </nav>
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
                    <i class="bx
                        @if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) bxs-file-image text-success
                        @elseif(in_array($document->file_type, ['pdf'])) bxs-file-pdf text-danger
                        @elseif(in_array($document->file_type, ['doc', 'docx'])) bxs-file-doc text-primary
                        @elseif(in_array($document->file_type, ['xls', 'xlsx'])) bxs-file-txt text-success
                        @elseif(in_array($document->file_type, ['ppt', 'pptx'])) bxs-file text-warning
                        @else bxs-file text-secondary
                        @endif
                    " style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0 ms-2">{{ $document->original_filename }}</h5>
                </div>

                <div class="action-buttons">
                    @can('download documents')
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-download"></i> Download
                    </a>
                    @endcan
                    @can('edit documents')
                    <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                    @endcan
                    @can('delete documents')
                    <button type="button" class="btn btn-sm btn-outline-danger delete-doc-btn" data-id="{{ $document->id }}" data-name="{{ $document->original_filename }}">
                        <i class="bx bx-trash"></i> Delete
                    </button>
                    @endcan
                </div>
            </div>

            @if($document->description)
                <p class="text-muted small mb-4">{{ $document->description }}</p>
            @endif

            <!-- Document Preview Area -->
            <div class="document-preview border rounded p-3 mb-4">
                @if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <div class="text-center">
                        <img src="{{ Storage::url($document->file_path) }}" alt="{{ $document->original_filename }}" class="img-fluid" style="max-height: 500px;">
                    </div>
                @elseif($document->file_type == 'pdf')
                    <div class="ratio ratio-16x9" style="min-height: 500px;">
                        <object data="{{ Storage::url($document->file_path) }}" type="application/pdf" class="w-100 h-100">
                            <p>Your browser does not support PDFs. <a href="{{ route('documents.download', $document) }}">Download the PDF</a> instead.</p>
                        </object>
                    </div>
                @elseif(in_array($document->file_type, ['txt', 'md', 'html', 'css', 'js', 'php']))
                    <div class="bg-light p-3 rounded text-break" style="max-height: 500px; overflow-y: auto;">
                        <pre class="mb-0" style="white-space: pre-wrap;">{{ Storage::disk('public')->get($document->file_path) }}</pre>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bx
                                @if(in_array($document->file_type, ['doc', 'docx'])) bxs-file-doc text-primary
                                @elseif(in_array($document->file_type, ['xls', 'xlsx'])) bxs-file-txt text-success
                                @elseif(in_array($document->file_type, ['ppt', 'pptx'])) bxs-file text-warning
                                @else bxs-file text-secondary
                                @endif
                            " style="font-size: 5rem;"></i>
                        </div>
                        <h5>{{ strtoupper($document->file_type) }} File</h5>
                        <p class="text-muted mb-3">Preview not available for this file type</p>
                        @can('download documents')
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                            <i class="bx bx-download me-1"></i> Download File
                        </a>
                        @endcan
                    </div>
                @endif
            </div>

            <!-- Technical Info -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2">
                    <span class="small text-muted">Technical Information</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm m-0">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 140px;" class="ps-3">File Name</th>
                                <td>{{ $document->filename }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-3">MIME Type</th>
                                <td>{{ $document->mime_type }}</td>
                            </tr>
                            @if(!empty($document->meta_data))
                                <tr>
                                    <th scope="row" class="ps-3">Uploaded</th>
                                    <td>{{ \Carbon\Carbon::parse($document->meta_data['uploaded_at'])->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete document modal functionality
        const docModal = new bootstrap.Modal(document.getElementById('deleteDocModal'));
        document.querySelectorAll('.delete-doc-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('doc-name').textContent = this.dataset.name;
                document.getElementById('delete-doc-form').action = `/documents/${this.dataset.id}`;
                docModal.show();
            });
        });
    });
</script>
@endpush
@endsection
