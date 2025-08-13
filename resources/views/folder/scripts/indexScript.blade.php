<script>

    document.addEventListener('DOMContentLoaded', function() {
        // View toggle functionality
        const iconViewBtn = document.getElementById('icon-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const iconView = document.getElementById('icon-view');
        const listView = document.getElementById('list-view');

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
        document.querySelectorAll('.folder-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.querySelector('.item-actions').classList.remove('d-none');
            });

            item.addEventListener('mouseleave', function() {
                this.querySelector('.item-actions').classList.add('d-none');
            });
        });

        // Category hover effects
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                const actions = this.querySelector('.category-actions');
                if (actions) actions.classList.remove('d-none');
            });

            item.addEventListener('mouseleave', function() {
                const actions = this.querySelector('.category-actions');
                if (actions) actions.classList.add('d-none');
            });
        });

        // Delete folder functionality with SweetAlert
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const folderId = this.dataset.id;
                const folderName = this.dataset.name;
                showDeleteFolderSwal(folderId, folderName);
            });
        });
    });

    // Delete folder with SweetAlert
    function showDeleteFolderSwal(folderId, folderName) {
        Swal.fire({
            title: 'Delete Folder',
            text: `Are you sure you want to delete "${folderName}"? This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/folders/${folderId}`;
                form.style.display = 'none';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add method spoofing for DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Category management functions
    function showCreateCategorySwal() {
        Swal.fire({
            title: 'Create New Category',
            html: `
            <div class="mb-3 text-start">
                <label for="swal-category-name" class="form-label fw-bold">Category Name</label>
                <input id="swal-category-name" class="form-control" placeholder="Enter category name" required>
            </div>
            <div class="mb-3 text-start">
                <label for="swal-category-desc" class="form-label fw-bold">Description (Optional)</label>
                <textarea id="swal-category-desc" class="form-control" placeholder="Enter description" rows="3"></textarea>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'Create Category',
            preConfirm: () => {
                const name = document.getElementById('swal-category-name').value;
                if (!name.trim()) {
                    Swal.showValidationMessage('Category name is required');
                    return false;
                }
                return {
                    name: name.trim(),
                    description: document.getElementById('swal-category-desc').value.trim()
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("base-folder.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(result.value)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', 'Category created successfully.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message || 'Failed to create category.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to create category.', 'error');
                    });
            }
        });
    }

    function showEditCategorySwal(id, name, description) {
        Swal.fire({
            title: 'Edit Category',
            html: `
            <div class="mb-3 text-start">
                <label for="swal-category-name" class="form-label fw-bold">Category Name</label>
                <input id="swal-category-name" class="form-control" placeholder="Enter category name" value="${name}" required>
            </div>
            <div class="mb-3 text-start">
                <label for="swal-category-desc" class="form-label fw-bold">Description (Optional)</label>
                <textarea id="swal-category-desc" class="form-control" placeholder="Enter description" rows="3">${description}</textarea>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'Update Category',
            preConfirm: () => {
                const name = document.getElementById('swal-category-name').value;
                if (!name.trim()) {
                    Swal.showValidationMessage('Category name is required');
                    return false;
                }
                return {
                    name: name.trim(),
                    description: document.getElementById('swal-category-desc').value.trim()
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/base-folder/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(result.value)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', 'Category updated successfully.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message || 'Failed to update category.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to update category.', 'error');
                    });
            }
        });
    }

    function deleteCategory(id, name) {
        Swal.fire({
            title: 'Delete Category',
            text: `Are you sure you want to delete "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/base-folder/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Deleted!', 'Category has been deleted.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message || 'Failed to delete category.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to delete category.', 'error');
                    });
            }
        });
    }

    function showCreateFolderSwal() {
        const selectedBaseFolderId = {{ $selectedBaseFolder->id ?? 'null' }};
        const selectedBaseFolderName = '{{ $selectedBaseFolder->name ?? '' }}';

        // Don't show modal if no base folder is selected
        if (!selectedBaseFolderId) {
            Swal.fire({
                title: 'No Category Selected',
                text: 'Please select a category first before creating a folder.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        Swal.fire({
            title: `Create New Folder in "${selectedBaseFolderName}"`,
            html: `
                <div class="mb-3 text-start">
                    <label for="swal-folder-name" class="form-label fw-bold">Folder Name</label>
                    <input id="swal-folder-name" class="form-control" placeholder="Enter folder name" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="swal-folder-desc" class="form-label fw-bold">Description (Optional)</label>
                    <textarea id="swal-folder-desc" class="form-control" placeholder="Enter description" rows="3"></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Create Folder',
            preConfirm: () => {
                const name = document.getElementById('swal-folder-name').value;

                if (!name.trim()) {
                    Swal.showValidationMessage('Folder name is required');
                    return false;
                }

                return {
                    name: name.trim(),
                    base_folder_id: selectedBaseFolderId,
                    description: document.getElementById('swal-folder-desc').value.trim(),
                    parent_id: null
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("folders.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(result.value)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Folder created successfully',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to create folder',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while creating the folder',
                            icon: 'error'
                        });
                    });
            }
        });
    }


    $(document).ready(function() {
        // View toggle functionality
        const iconViewBtn = document.getElementById('icon-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const iconView = document.getElementById('icon-view');
        const listView = document.getElementById('list-view');
        const mainContent = document.getElementById('main-content');

        let dragCounter = 0;
        let isDraggingItem = false;
        let draggedItem = null;

        // File type validation
        const allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx'];
        const maxFileSize = 10 * 1024 * 1024; // 10MB

        if (iconViewBtn && listViewBtn) {
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
        }

        // Hover effects for items
        document.querySelectorAll('.folder-item, .file-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                if (!isDraggingItem) {
                    const actions = this.querySelector('.item-actions');
                    if (actions) actions.classList.remove('d-none');
                }
            });

            item.addEventListener('mouseleave', function() {
                const actions = this.querySelector('.item-actions');
                if (actions) actions.classList.add('d-none');
            });
        });

        // Drag and drop for moving items
        document.querySelectorAll('[draggable="true"]').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                isDraggingItem = true;
                draggedItem = {
                    id: this.dataset.id,
                    type: this.dataset.type,
                    name: this.dataset.name || this.querySelector('.item-name')?.textContent || 'Unknown',
                    baseFolderId: this.dataset.baseFolderId
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
                        if (zone.dataset.folderId !== draggedItem.id) {
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

                if (isDraggingItem && this.dataset.folderId !== draggedItem.id) {
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

                if (isDraggingItem && this.dataset.folderId !== draggedItem.id) {
                    const targetFolderId = this.dataset.folderId || null;
                    const targetBaseFolderId = this.dataset.baseFolderId || null;

                    // If dropping on a category (sidebar link), move to that category
                    if (targetBaseFolderId && !targetFolderId && draggedItem.baseFolderId !== targetBaseFolderId) {
                        moveFolderToCategory(draggedItem, targetBaseFolderId);
                    }
                    // If dropping on a folder within the same category
                    else if (targetFolderId) {
                        // Prevent dropping folder into itself or its children
                        if (draggedItem.type === 'folder' && targetFolderId === draggedItem.id) {
                            alert('Cannot move folder into itself');
                            return;
                        }
                        moveItem(draggedItem, targetFolderId);
                    }
                } else if (!isDraggingItem) {
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        // For index page, we need to determine target folder
                        const targetFolderId = this.dataset.folderId;
                        if (targetFolderId) {
                            handleFileUploads(files, targetFolderId);
                        }
                    }
                }
            });
        });

        function moveItem(item, targetFolderId) {
            // Only handle folder moves in index context
            if (item.type !== 'folder') {
                console.error('Document moves not supported in index context');
                return;
            }

            const url = `/folders/${item.id}/move`;
            const data = { parent_id: targetFolderId };

            fetch(url, {
                method: 'POST',
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
                        if (container) {
                            container.parentNode.insertBefore(alert, container.nextSibling);
                        }

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
                    alert('An error occurred while moving the folder.');
                });
        }

        function moveFolderToCategory(item, targetBaseFolderId) {
            if (item.type !== 'folder') {
                console.error('Only folders can be moved to categories');
                return;
            }

            const url = `/folders/${item.id}/move-to-category`;
            const data = { base_folder_id: targetBaseFolderId };

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
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
                        if (container) {
                            container.parentNode.insertBefore(alert, container.nextSibling);
                        }

                        // Reload page after short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Move to category error:', error);
                    alert('An error occurred while moving the folder to category.');
                });
        }

        // Rest of the existing functions remain the same...
        function handleFileUploads(files, targetFolderId) {
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
                uploadFiles(validFiles, targetFolderId);
            }
        }

        function uploadFiles(files, targetFolderId) {
            const uploadModal = document.getElementById('uploadModal');
            const progressContainer = document.getElementById('upload-progress-container');
            const uploadFooter = document.getElementById('upload-footer');

            if (!uploadModal || !progressContainer) {
                console.error('Upload modal elements not found');
                return;
            }

            const modal = new bootstrap.Modal(uploadModal);
            progressContainer.innerHTML = '';
            uploadFooter.classList.add('d-none');
            modal.show();

            let completedUploads = 0;
            let successfulUploads = 0;

            Array.from(files).forEach((file, index) => {
                const progressItem = createProgressItem(file.name);
                progressContainer.appendChild(progressItem);

                uploadFile(file, targetFolderId, progressItem, () => {
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

        function uploadFile(file, folderId, progressItem, onSuccess, onError) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('folder_id', folderId);
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
        const deleteModal = document.getElementById('deleteFolderModal');
        if (deleteModal) {
            const modal = new bootstrap.Modal(deleteModal);
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('folder-name').textContent = this.dataset.name;
                    document.getElementById('delete-folder-form').action = `/folders/${this.dataset.id}`;
                    modal.show();
                });
            });
        }

        // Category hover effects
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                const actions = this.querySelector('.category-actions');
                if (actions) actions.classList.remove('d-none');
            });

            item.addEventListener('mouseleave', function() {
                const actions = this.querySelector('.category-actions');
                if (actions) actions.classList.add('d-none');
            });
        });

        // Keep all the existing SweetAlert functions for category and folder management
        // ... (existing functions remain unchanged)
    });
</script>

<style>
    .drag-over {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border: 2px dashed #0d6efd !important;
        transition: all 0.2s ease;
    }

    .dragging {
        opacity: 0.5;
    }

    .drag-preview {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        font-size: 0.875rem;
        position: absolute;
        top: -1000px;
        left: -1000px;
        z-index: 10000;
    }

    .drag-move-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(13, 110, 253, 0.1);
        border: 2px dashed #0d6efd;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .move-message {
        background: rgba(13, 110, 253, 0.9);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    [draggable="true"] {
        cursor: move;
    }

    [draggable="true"]:hover {
        opacity: 0.9;
    }

    .upload-progress-item {
        margin-bottom: 1rem;
    }

    .upload-progress-item.success {
        opacity: 0.7;
    }

    .upload-progress-item.error {
        opacity: 0.7;
    }
</style>
