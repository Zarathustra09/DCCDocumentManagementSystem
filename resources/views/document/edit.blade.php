@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Document</h5>
                    <div>
                        <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-outline-secondary me-2">
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
                            <label for="folder_id" class="form-label">Folder</label>
                            <select class="form-select" id="folder_id" name="folder_id">
                                <option value="">Root</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" {{ $document->folder_id == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                        </div>

                        @if(in_array($document->file_type, ['doc', 'docx']))
                        <div class="mb-3">
                            <label class="form-label">Document Content</label>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Edit your Word document content below</small>
                                <div>
                                    <button type="button" id="reload-content-btn" class="btn btn-sm btn-outline-secondary d-none">
                                        <i class="bx bx-refresh"></i> Reload
                                    </button>
                                    <button type="button" id="save-content-btn" class="btn btn-sm btn-success" disabled>
                                        <i class="bx bx-save"></i> Save Content
                                    </button>
                                </div>
                            </div>
                            <div id="editor-loading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2">Loading document content...</p>
                            </div>
                            <textarea id="tinymce-editor" name="content" class="d-none"></textarea>
                            <div id="content-status" class="content-saved-indicator">
                                Content saved successfully!
                            </div>
                        </div>
                        @endif

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

@can('delete documents')
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete "{{ $document->original_filename }}"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('documents.destroy', $document) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('styles')
<style>
    .tox-tinymce {
        border-radius: 0.375rem !important;
    }

    .content-saved-indicator {
        background-color: #d1e7dd;
        border: 1px solid #badbcc;
        color: #0f5132;
        padding: 8px 12px;
        border-radius: 4px;
        margin-top: 10px;
        display: none;
    }

    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.tiny.cloud/1/{{env('TINYMCE_API_KEY')}}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    let tinymceEditor = null;
    let contentLoaded = false;
    let originalContent = '';

    // Enhanced logging function
    function logToConsole(level, message, data = {}) {
        const timestamp = new Date().toISOString();
        const logData = {
            timestamp,
            level,
            message,
            user_id: '{{ Auth::id() }}',
            document_id: '{{ $document->id }}',
            ...data
        };

        console[level](`[${timestamp}] ${message}`, logData);
    }

    function loadDocumentContent() {
        logToConsole('info', 'Auto-loading document content for editing');

        fetch(`/documents/{{ $document->id }}/preview`)
            .then(response => response.json())
            .then(data => {
                const loadingDiv = document.getElementById('editor-loading');
                const editorTextarea = document.getElementById('tinymce-editor');
                const reloadBtn = document.getElementById('reload-content-btn');

                if (data.success && tinymceEditor) {
                    logToConsole('info', 'Document content loaded successfully', {
                        content_length: data.content.length
                    });

                    tinymceEditor.setContent(data.content);
                    originalContent = data.content;
                    contentLoaded = true;

                    loadingDiv.classList.add('d-none');
                    editorTextarea.classList.remove('d-none');
                    reloadBtn.classList.remove('d-none');
                    document.getElementById('save-content-btn').disabled = true;
                } else {
                    logToConsole('error', 'Failed to load document content', {
                        error_message: data.message
                    });

                    loadingDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bx bx-error me-1"></i>
                            Error loading content: ${data.message || 'Unknown error'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                logToConsole('error', 'Network error loading document content', {
                    error: error.message
                });

                document.getElementById('editor-loading').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bx bx-error me-1"></i>
                        Network error occurred while loading content
                    </div>
                `;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        logToConsole('info', 'Document edit page loaded');

        // Initialize TinyMCE
        function initTinyMCE() {
            logToConsole('info', 'Initializing TinyMCE editor');

            tinymce.init({
                selector: '#tinymce-editor',
                height: 500,
                menubar: true,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic forecolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                setup: function(editor) {
                    editor.on('init', function() {
                        tinymceEditor = editor;
                        logToConsole('info', 'TinyMCE editor initialized successfully');

                        // Auto-load content after editor is ready
                        @if(in_array($document->file_type, ['doc', 'docx']))
                        setTimeout(() => loadDocumentContent(), 100);
                        @endif
                    });

                    editor.on('change', function() {
                        if (contentLoaded && tinymceEditor) {
                            const currentContent = tinymceEditor.getContent();
                            const hasChanges = currentContent !== originalContent;
                            document.getElementById('save-content-btn').disabled = !hasChanges;

                            logToConsole('debug', 'Editor content changed', {
                                content_length: currentContent.length,
                                has_changes: hasChanges
                            });
                        }
                    });
                }
            });
        }

        // Reload document content
        document.getElementById('reload-content-btn').addEventListener('click', function() {
            const loadingDiv = document.getElementById('editor-loading');
            const editorTextarea = document.getElementById('tinymce-editor');

            loadingDiv.classList.remove('d-none');
            editorTextarea.classList.add('d-none');
            this.classList.add('d-none');

            loadingDiv.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Reloading document content...</p>
                </div>
            `;

            loadDocumentContent();
        });

        // Save document content
        document.getElementById('save-content-btn').addEventListener('click', function() {
            if (!tinymceEditor || !contentLoaded) {
                alert('Please wait for content to load first');
                return;
            }

            const btn = this;
            const originalText = btn.innerHTML;
            const content = tinymceEditor.getContent();

            logToConsole('info', 'Saving document content', {
                content_length: content.length
            });

            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner"></span> Saving...';

            fetch(`/documents/{{ $document->id }}/update-content`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    logToConsole('info', 'Document content saved successfully');

                    originalContent = content;
                    btn.innerHTML = '<i class="bx bx-check"></i> Saved';
                    btn.disabled = true;

                    // Show success indicator
                    const indicator = document.getElementById('content-status');
                    indicator.style.display = 'block';
                    setTimeout(() => {
                        indicator.style.display = 'none';
                        btn.innerHTML = originalText;
                    }, 3000);
                } else {
                    logToConsole('error', 'Failed to save document content', {
                        error_message: data.message
                    });

                    alert('Error saving content: ' + (data.message || 'Unknown error'));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                logToConsole('error', 'Network error saving document content', {
                    error: error.message
                });

                alert('Network error occurred while saving content');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

        // Initialize TinyMCE for Word documents
        @if(in_array($document->file_type, ['doc', 'docx']))
        initTinyMCE();
        @endif

        // Form submission logging
        document.getElementById('document-edit-form').addEventListener('submit', function() {
            logToConsole('info', 'Document metadata update submitted');
        });
    });

    // Global error handler
    window.addEventListener('error', function(e) {
        logToConsole('error', 'JavaScript error occurred', {
            message: e.message,
            filename: e.filename,
            line: e.lineno,
            stack: e.error ? e.error.stack : 'No stack trace'
        });
    });
</script>
@endpush
