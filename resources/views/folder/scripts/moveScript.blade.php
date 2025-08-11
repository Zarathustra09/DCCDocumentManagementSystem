<script>
    // Drag and Drop functionality for moving documents and folders
    let draggedElement = null;

    document.addEventListener('DOMContentLoaded', function() {
        setupDragAndDrop();
    });

    function setupDragAndDrop() {
        // Set up draggable items
        document.querySelectorAll('[draggable="true"]').forEach(item => {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
        });

        // Set up drop zones
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.addEventListener('dragover', handleDragOver);
            zone.addEventListener('dragenter', handleDragEnter);
            zone.addEventListener('dragleave', handleDragLeave);
            zone.addEventListener('drop', handleDrop);
        });

        // Global drop functionality for file uploads
        setupGlobalDrop();
    }

    function handleDragStart(e) {
        draggedElement = {
            element: this,
            type: this.dataset.type,
            id: this.dataset.id,
            name: this.dataset.name
        };

        this.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
    }

    function handleDragEnd(e) {
        this.style.opacity = '1';

        // Remove all drop zone highlights
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.classList.remove('drag-over');
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }

    function handleDragEnter(e) {
        e.preventDefault();
        if (draggedElement) {
            this.classList.add('drag-over');
        }
    }

    function handleDragLeave(e) {
        if (!this.contains(e.relatedTarget)) {
            this.classList.remove('drag-over');
        }
    }

    function handleDrop(e) {
        e.preventDefault();
        this.classList.remove('drag-over');

        if (!draggedElement) return;

        const targetFolderId = this.dataset.folderId;

        // Don't allow dropping on itself
        if (draggedElement.type === 'folder' && targetFolderId === draggedElement.id) {
            return;
        }

        if (draggedElement.type === 'document') {
            moveDocument(draggedElement.id, targetFolderId, draggedElement.name);
        } else if (draggedElement.type === 'folder') {
            moveFolder(draggedElement.id, targetFolderId, draggedElement.name);
        }

        draggedElement = null;
    }

    function moveDocument(documentId, targetFolderId, documentName) {
        const targetName = targetFolderId ?
            document.querySelector(`[data-folder-id="${targetFolderId}"]`).textContent.trim() :
            'Root';

        Swal.fire({
            title: 'Move Document',
            text: `Move "${documentName}" to "${targetName}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Move',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/documents/${documentId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        folder_id: targetFolderId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Moved!', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Move error:', error);
                        Swal.fire('Error', 'Failed to move document.', 'error');
                    });
            }
        });
    }

    function moveFolder(folderId, targetFolderId, folderName) {
        const targetName = targetFolderId ?
            document.querySelector(`[data-folder-id="${targetFolderId}"]`).textContent.trim() :
            'Root';

        Swal.fire({
            title: 'Move Folder',
            text: `Move "${folderName}" to "${targetName}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Move',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/folders/${folderId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        parent_id: targetFolderId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Moved!', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Move error:', error);
                        Swal.fire('Error', 'Failed to move folder.', 'error');
                    });
            }
        });
    }

    function setupGlobalDrop() {
        const globalOverlay = document.getElementById('global-drop-overlay');
        let dragCounter = 0;

        document.addEventListener('dragenter', function(e) {
            // Only handle file drags from outside the browser
            if (e.dataTransfer.types.includes('Files')) {
                dragCounter++;
                if (dragCounter === 1) {
                    globalOverlay.classList.remove('d-none');
                }
            }
        });

        document.addEventListener('dragleave', function(e) {
            if (e.dataTransfer.types.includes('Files')) {
                dragCounter--;
                if (dragCounter <= 0) {
                    dragCounter = 0;
                    globalOverlay.classList.add('d-none');
                }
            }
        });

        document.addEventListener('dragover', function(e) {
            if (e.dataTransfer.types.includes('Files')) {
                e.preventDefault();
            }
        });

        document.addEventListener('drop', function(e) {
            if (e.dataTransfer.types.includes('Files')) {
                e.preventDefault();
                dragCounter = 0;
                globalOverlay.classList.add('d-none');

                const files = Array.from(e.dataTransfer.files);
                if (files.length > 0) {
                    uploadFiles(files);
                }
            }
        });
    }

    function uploadFiles(files) {
        const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        const progressContainer = document.getElementById('upload-progress-container');
        const uploadFooter = document.getElementById('upload-footer');

        uploadModal.show();
        progressContainer.innerHTML = '';
        uploadFooter.classList.add('d-none');

        let completedUploads = 0;
        let totalFiles = files.length;

        files.forEach((file, index) => {
            const progressId = `progress-${index}`;
            const progressHtml = `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">${file.name}</span>
                    <span class="small text-muted" id="${progressId}-status">Uploading...</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" id="${progressId}" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        `;
            progressContainer.innerHTML += progressHtml;

            uploadSingleFile(file, progressId, () => {
                completedUploads++;
                if (completedUploads === totalFiles) {
                    uploadFooter.classList.remove('d-none');
                    setTimeout(() => {
                        uploadModal.hide();
                        location.reload();
                    }, 2000);
                }
            });
        });
    }

    function uploadSingleFile(file, progressId, callback) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('folder_id', '{{ $folder->id ?? "" }}');

        const xhr = new XMLHttpRequest();

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                document.getElementById(progressId).style.width = percent + '%';
                document.getElementById(progressId).textContent = percent + '%';
            }
        };

        xhr.onload = function() {
            const statusElement = document.getElementById(progressId + '-status');
            if (xhr.status === 200) {
                statusElement.textContent = 'Complete';
                statusElement.className = 'small text-success';
            } else {
                statusElement.textContent = 'Failed';
                statusElement.className = 'small text-danger';
            }
            callback();
        };

        xhr.onerror = function() {
            const statusElement = document.getElementById(progressId + '-status');
            statusElement.textContent = 'Failed';
            statusElement.className = 'small text-danger';
            callback();
        };

        xhr.open('POST', '{{ route("documents.store") }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.send(formData);
    }
</script>

<style>
    .drag-over {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border: 2px dashed #0d6efd !important;
        transition: all 0.2s ease;
    }

    .global-drop-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .drop-message {
        text-align: center;
        padding: 2rem;
        border: 3px dashed rgba(255, 255, 255, 0.5);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
    }

    [draggable="true"] {
        cursor: move;
    }

    [draggable="true"]:hover {
        opacity: 0.9;
    }
</style>
