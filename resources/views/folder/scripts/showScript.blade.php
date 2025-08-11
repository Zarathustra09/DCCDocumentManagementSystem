<script>
    $(document).ready(function() {
        $('#folder-table').DataTable();
    });

    document.addEventListener('DOMContentLoaded', function() {
        // View toggle functionality
        const iconViewBtn = document.getElementById('icon-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const iconView = document.getElementById('icon-view');
        const listView = document.getElementById('list-view');
        const mainContent = document.getElementById('main-content');
        const globalOverlay = document.getElementById('global-drop-overlay');
        const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));

        let dragCounter = 0;
        let isDraggingItem = false;
        let draggedItem = null;

        // File type validation
        const allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx'];
        const maxFileSize = 10 * 1024 * 1024; // 10MB

        iconViewBtn.addEventListener('click', function() {
            iconView.classList.remove('d-none');
            listView.classList.add('d-none');
            iconViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            localStorage.setItem('folder-view', 'icons');
        });

        listViewBtn.addEventListener('click', function() {
            listView.classList.remove('d-none');
            iconView.classList.add('d-none');
            listViewBtn.classList.add('active');
            iconViewBtn.classList.remove('active');
            localStorage.setItem('folder-view', 'list');
        });

        // Load saved preference
        if (localStorage.getItem('folder-view') === 'list') {
            listViewBtn.click();
        }

        // Hover effects for items
        document.querySelectorAll('.folder-item, .file-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                if (!isDraggingItem) {
                    this.querySelector('.item-actions').classList.remove('d-none');
                }
            });

            item.addEventListener('mouseleave', function() {
                this.querySelector('.item-actions').classList.add('d-none');
            });
        });

        // Drag and drop for moving items
        // Drag and drop for moving items
        document.querySelectorAll('[draggable="true"]').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                isDraggingItem = true;
                draggedItem = {
                    id: this.dataset.id,
                    type: this.dataset.type,
                    name: this.dataset.name || this.querySelector('.item-name')?.textContent || 'Unknown'
                };

                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('application/json', JSON.stringify(draggedItem));

                // Create custom drag preview
                const dragPreview = document.createElement('div');
                dragPreview.className = 'drag-preview';
                dragPreview.innerHTML = `
            <i class="bx ${draggedItem.type === 'folder' ? 'bx-folder' : 'bx-file'}" style="margin-right: 0.5rem; color: ${draggedItem.type === 'folder' ? '#ffc107' : '#6c757d'};"></i>
            <span>${draggedItem.name}</span>
        `;
                document.body.appendChild(dragPreview);

                // Set custom drag image
                e.dataTransfer.setDragImage(dragPreview, 20, 20);

                // Remove preview after drag starts
                setTimeout(() => {
                    if (document.body.contains(dragPreview)) {
                        document.body.removeChild(dragPreview);
                    }
                }, 0);

                // Add overlay to potential drop zones
                setTimeout(() => {
                    document.querySelectorAll('.drop-zone').forEach(zone => {
                        if (zone.dataset.id !== draggedItem.id) {
                            const overlay = document.createElement('div');
                            overlay.className = 'drag-move-overlay d-none';
                            overlay.innerHTML = `
                        <div class="move-message">
                            <i class="bx bx-move"></i>
                            Drop to move here
                        </div>
                    `;
                            zone.style.position = 'relative';
                            zone.appendChild(overlay);
                        }
                    });
                }, 50);
            });

            item.addEventListener('dragend', function() {
                isDraggingItem = false;
                this.classList.remove('dragging');
                draggedItem = null;

                // Remove all overlays
                document.querySelectorAll('.drag-move-overlay').forEach(overlay => {
                    overlay.remove();
                });

                // Remove drag-over class from all drop zones
                document.querySelectorAll('.drop-zone').forEach(zone => {
                    zone.classList.remove('drag-over');
                });
            });
        });

// Drop zone events for moving items
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();

                if (isDraggingItem && this.dataset.id !== draggedItem.id) {
                    e.dataTransfer.dropEffect = 'move';
                    this.classList.add('drag-over');

                    const overlay = this.querySelector('.drag-move-overlay');
                    if (overlay) {
                        overlay.classList.remove('d-none');
                    }
                } else if (!isDraggingItem) {
                    e.dataTransfer.dropEffect = 'copy';
                    this.classList.add('drag-over');
                }
            });

            zone.addEventListener('dragleave', function(e) {
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');

                    const overlay = this.querySelector('.drag-move-overlay');
                    if (overlay) {
                        overlay.classList.add('d-none');
                    }
                }
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                const overlay = this.querySelector('.drag-move-overlay');
                if (overlay) {
                    overlay.remove();
                }

                if (isDraggingItem && this.dataset.id !== draggedItem.id) {
                    const targetFolderId = this.dataset.id;

                    // Prevent dropping folder into itself or its children
                    if (draggedItem.type === 'folder' && targetFolderId === draggedItem.id) {
                        alert('Cannot move folder into itself');
                        return;
                    }

                    moveItem(draggedItem, targetFolderId);
                } else if (!isDraggingItem) {
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleFileUploads(files);
                    }
                }
            });
        });

        // Drop zone events for moving items
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();

                if (isDraggingItem) {
                    e.dataTransfer.dropEffect = 'move';
                    this.classList.add('drag-over');
                } else {
                    e.dataTransfer.dropEffect = 'copy';
                }
            });

            zone.addEventListener('dragleave', function(e) {
                // Only remove drag-over if we're leaving the element and not entering a child
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');
                }
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                if (isDraggingItem) {
                    // Handle moving items
                    const targetFolderId = this.dataset.folderId || null;
                    const item = JSON.parse(e.dataTransfer.getData('application/json'));

                    // Don't allow dropping on self
                    if (item.type === 'folder' && item.id == targetFolderId) {
                        return;
                    }

                    moveItem(item, targetFolderId);
                } else {
                    // Handle file uploads
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleFileUploads(files);
                    }
                }
            });
        });

        // Global drag events for file upload (when not dragging items)
        document.addEventListener('dragenter', (e) => {
            if (!isDraggingItem) {
                e.preventDefault();
                dragCounter++;
                if (dragCounter === 1) {
                    globalOverlay.classList.remove('d-none');
                    mainContent.classList.add('drag-over');
                }
            }
        });

        document.addEventListener('dragleave', (e) => {
            if (!isDraggingItem) {
                e.preventDefault();
                dragCounter--;
                if (dragCounter === 0) {
                    globalOverlay.classList.add('d-none');
                    mainContent.classList.remove('drag-over');
                }
            }
        });

        document.addEventListener('dragover', (e) => {
            e.preventDefault();
        });

        document.addEventListener('drop', (e) => {
            if (!isDraggingItem) {
                e.preventDefault();
                dragCounter = 0;
                globalOverlay.classList.add('d-none');
                mainContent.classList.remove('drag-over');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileUploads(files);
                }
            }
        });

        function moveItem(item, targetFolderId) {
            const url = item.type === 'folder'
                ? `/folders/${item.id}/move`
                : `/documents/${item.id}/move`;

            const data = item.type === 'folder'
                ? { parent_id: targetFolderId }
                : { folder_id: targetFolderId };

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show mt-3';
                        alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;

                        const container = document.querySelector('.my-3');
                        container.parentNode.insertBefore(alert, container.nextSibling);

                        // Reload page after short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Move error:', error);
                    alert('An error occurred while moving the item.');
                });
        }

        function handleFileUploads(files) {
            const validFiles = [];
            const errors = [];

            // Validate files
            Array.from(files).forEach(file => {
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (!allowedTypes.includes(fileExtension)) {
                    errors.push(`${file.name}: File type not supported`);
                    return;
                }

                if (file.size > maxFileSize) {
                    errors.push(`${file.name}: File size exceeds 10MB limit`);
                    return;
                }

                validFiles.push(file);
            });

            // Show errors if any
            if (errors.length > 0) {
                alert('Some files could not be uploaded:\n\n' + errors.join('\n'));
            }

            // Upload valid files
            if (validFiles.length > 0) {
                uploadFiles(validFiles);
            }
        }

        function uploadFiles(files) {
            const progressContainer = document.getElementById('upload-progress-container');
            const uploadFooter = document.getElementById('upload-footer');

            progressContainer.innerHTML = '';
            uploadFooter.classList.add('d-none');
            uploadModal.show();

            let completedUploads = 0;
            let successfulUploads = 0;

            Array.from(files).forEach((file, index) => {
                const progressItem = createProgressItem(file.name);
                progressContainer.appendChild(progressItem);

                uploadFile(file, progressItem, () => {
                    completedUploads++;
                    successfulUploads++;

                    if (completedUploads === files.length) {
                        uploadFooter.classList.remove('d-none');

                        if (successfulUploads > 0) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    }
                }, () => {
                    completedUploads++;

                    if (completedUploads === files.length) {
                        uploadFooter.classList.remove('d-none');
                    }
                });
            });
        }

        function createProgressItem(fileName) {
            const div = document.createElement('div');
            div.className = 'upload-progress-item';
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium">${fileName}</span>
                    <span class="status">Uploading...</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                </div>
            `;
            return div;
        }

        function uploadFile(file, progressItem, onSuccess, onError) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('folder_id', '{{ $folder->id }}');
            formData.append('_token', '{{ csrf_token() }}');

            const xhr = new XMLHttpRequest();
            const progressBar = progressItem.querySelector('.progress-bar');
            const statusSpan = progressItem.querySelector('.status');

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                }
            });

            xhr.addEventListener('load', () => {
                if (xhr.status === 200 || xhr.status === 302) {
                    progressItem.classList.add('success');
                    statusSpan.textContent = 'Uploaded';
                    statusSpan.className = 'status text-success';
                    progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                    progressBar.classList.add('bg-success');
                    progressBar.style.width = '100%';
                    onSuccess();
                } else {
                    progressItem.classList.add('error');
                    statusSpan.textContent = 'Failed';
                    statusSpan.className = 'status text-danger';
                    progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                    progressBar.classList.add('bg-danger');
                    onError();
                }
            });

            xhr.addEventListener('error', () => {
                progressItem.classList.add('error');
                statusSpan.textContent = 'Failed';
                statusSpan.className = 'status text-danger';
                progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                progressBar.classList.add('bg-danger');
                onError();
            });

            xhr.open('POST', '{{ route("documents.store") }}');
            xhr.send(formData);
        }

        // Delete folder modal functionality
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteFolderModal'));
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('folder-name').textContent = this.dataset.name;
                document.getElementById('delete-folder-form').action = `/folders/${this.dataset.id}`;
                deleteModal.show();
            });
        });

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


    document.getElementById('swal-upload-btn')?.addEventListener('click', function() {
        Swal.fire({
            title: 'Upload Document',
            html: `
            <input type="file" id="swal-file-input" class="swal2-input" style="width:100%" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.xls,.xlsx">
            <textarea id="swal-desc-input" class="swal2-textarea" placeholder="Description (optional)"></textarea>
        `,
            showCancelButton: true,
            confirmButtonText: 'Upload',
            preConfirm: () => {
                const fileInput = document.getElementById('swal-file-input');
                const descInput = document.getElementById('swal-desc-input');
                if (!fileInput.files.length) {
                    Swal.showValidationMessage('Please select a file');
                    return false;
                }
                return { file: fileInput.files[0], description: descInput.value };
            }
        }).then(result => {
            if (result.isConfirmed && result.value.file) {
                const file = result.value.file;
                const description = result.value.description;
                const allowedTypes = ['pdf','doc','docx','txt','jpg','jpeg','png','gif','xls','xlsx'];
                const ext = file.name.split('.').pop().toLowerCase();
                if (!allowedTypes.includes(ext)) {
                    Swal.fire('Error', 'File type not supported', 'error');
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    Swal.fire('Error', 'File size exceeds 10MB', 'error');
                    return;
                }
                const formData = new FormData();
                formData.append('file', file);
                formData.append('folder_id', '{{ $folder->id }}');
                formData.append('description', description);
                formData.append('_token', '{{ csrf_token() }}');

                Swal.fire({
                    title: 'Uploading...',
                    html: '<div id="swal-progress" class="progress"><div class="progress-bar" style="width:0%"></div></div>',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("documents.store") }}');
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        Swal.getHtmlContainer().querySelector('.progress-bar').style.width = percent + '%';
                    }
                };
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        Swal.fire('Success', 'Document uploaded successfully.', 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', 'Upload failed.', 'error');
                    }
                };
                xhr.onerror = function() {
                    Swal.fire('Error', 'Upload failed.', 'error');
                };
                xhr.send(formData);
            }
        });
    });


    function showEditFolderSwal() {
        Swal.fire({
            title: 'Edit Folder',
            html: `
            <div class="mb-3 text-start">
                <label for="swal-folder-name" class="form-label fw-bold">Name</label>
                <input id="swal-folder-name" class="form-control" placeholder="Name" value="{{ addslashes($folder->name) }}">
            </div>
            <div class="mb-3 text-start">
                <label for="swal-folder-desc" class="form-label fw-bold">Description</label>
                <textarea id="swal-folder-desc" class="form-control" placeholder="Description" rows="3">{{ addslashes($folder->description ?? '') }}</textarea>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'Save',
            customClass: {
                popup: 'swal2-edit-modal'
            },
            preConfirm: () => {
                return {
                    name: document.getElementById('swal-folder-name').value,
                    description: document.getElementById('swal-folder-desc').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("folders.quick-update", $folder) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(result.value)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Saved!', 'Folder updated.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Update failed.', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Update failed.', 'error'));
            }
        });
    }
</script>
