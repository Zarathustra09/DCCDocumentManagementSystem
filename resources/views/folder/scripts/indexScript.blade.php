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

        // Category filter
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

        // Add Folder functionality
        document.querySelectorAll('.add-folder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const baseFolderId = this.dataset.baseFolderId;
                const baseFolderName = this.dataset.baseFolderName;

                Swal.fire({
                    title: `Add Folder to ${baseFolderName}`,
                    html: `
                        <div class="mb-3 text-start">
                            <label for="folder-name" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folder-name" placeholder="Enter folder name">
                        </div>
                        <div class="mb-3 text-start">
                            <label for="folder-description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="folder-description" rows="3" placeholder="Enter folder description"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Create Folder',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    focusConfirm: false,
                    didOpen: () => {
                        document.getElementById('folder-name').focus();
                    },
                    preConfirm: () => {
                        const nameInput = Swal.getPopup().querySelector('#folder-name');
                        const descriptionInput = Swal.getPopup().querySelector('#folder-description');

                        const name = nameInput ? nameInput.value.trim() : '';
                        const description = descriptionInput ? descriptionInput.value.trim() : '';

                        if (!name) {
                            Swal.showValidationMessage('Please enter a folder name');
                            return false;
                        }

                        if (name.length > 255) {
                            Swal.showValidationMessage('Folder name must be less than 255 characters');
                            return false;
                        }

                        return { name, description };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        createFolder(baseFolderId, result.value.name, result.value.description);
                    }
                });
            });
        });

        // Create folder function
        function createFolder(baseFolderId, name, description) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('name', name);
            formData.append('description', description);
            formData.append('base_folder_id', baseFolderId);

            fetch('{{ route("folders.store") }}', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    }
                    throw new Error('Network response was not ok');
                })
                .then(() => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Folder created successfully',
                        icon: 'success',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to create folder. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        // Delete folder modal functionality
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

        // Base Folder CRUD functionality
        // Create Base Folder
        document.getElementById('createBaseFolderBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Create Base Folder',
                html: `
                    <div class="mb-3 text-start">
                        <label for="base-folder-name" class="form-label">Base Folder Name</label>
                        <input type="text" class="form-control" id="base-folder-name" placeholder="Enter base folder name">
                    </div>
                    <div class="mb-3 text-start">
                        <label for="base-folder-description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="base-folder-description" rows="3" placeholder="Enter base folder description"></textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Create Base Folder',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                didOpen: () => {
                    document.getElementById('base-folder-name').focus();
                },
                preConfirm: () => {
                    const nameInput = Swal.getPopup().querySelector('#base-folder-name');
                    const descriptionInput = Swal.getPopup().querySelector('#base-folder-description');

                    const name = nameInput ? nameInput.value.trim() : '';
                    const description = descriptionInput ? descriptionInput.value.trim() : '';

                    if (!name) {
                        Swal.showValidationMessage('Please enter a base folder name');
                        return false;
                    }

                    if (name.length > 255) {
                        Swal.showValidationMessage('Base folder name must be less than 255 characters');
                        return false;
                    }

                    return { name, description };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    createBaseFolder(result.value.name, result.value.description);
                }
            });
        });

        // Edit Base Folder
        document.querySelectorAll('.edit-base-folder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const baseFolderId = this.dataset.id;
                const baseFolderName = this.dataset.name;
                const baseFolderDescription = this.dataset.description || '';

                Swal.fire({
                    title: 'Edit Base Folder',
                    html: `
                        <div class="mb-3 text-start">
                            <label for="edit-base-folder-name" class="form-label">Base Folder Name</label>
                            <input type="text" class="form-control" id="edit-base-folder-name" value="${baseFolderName}" placeholder="Enter base folder name">
                        </div>
                        <div class="mb-3 text-start">
                            <label for="edit-base-folder-description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="edit-base-folder-description" rows="3" placeholder="Enter base folder description">${baseFolderDescription}</textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update Base Folder',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    focusConfirm: false,
                    didOpen: () => {
                        document.getElementById('edit-base-folder-name').focus();
                    },
                    preConfirm: () => {
                        const nameInput = Swal.getPopup().querySelector('#edit-base-folder-name');
                        const descriptionInput = Swal.getPopup().querySelector('#edit-base-folder-description');

                        const name = nameInput ? nameInput.value.trim() : '';
                        const description = descriptionInput ? descriptionInput.value.trim() : '';

                        if (!name) {
                            Swal.showValidationMessage('Please enter a base folder name');
                            return false;
                        }

                        if (name.length > 255) {
                            Swal.showValidationMessage('Base folder name must be less than 255 characters');
                            return false;
                        }

                        return { name, description };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateBaseFolder(baseFolderId, result.value.name, result.value.description);
                    }
                });
            });
        });

        // Delete Base Folder
        document.querySelectorAll('.delete-base-folder-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const baseFolderId = this.dataset.id;
                const baseFolderName = this.dataset.name;
                const foldersCount = parseInt(this.dataset.folders) || 0;

                let warningMessage = '';
                if (foldersCount > 0) {
                    warningMessage = `<div class="alert alert-warning mt-3">
                        <i class="bx bx-error-circle me-2"></i>
                        This base folder contains ${foldersCount} folder${foldersCount !== 1 ? 's' : ''}. You must move or delete them first.
                    </div>`;
                }

                Swal.fire({
                    title: 'Delete Base Folder',
                    html: `
                        <div class="text-center mb-3">
                            <i class="bx bx-error-circle text-danger display-4 mb-3"></i>
                            <p>Are you sure you want to delete the base folder <strong>"${baseFolderName}"</strong>?</p>
                            ${warningMessage}
                            <div class="alert alert-danger mt-3">
                                <small><strong>This action cannot be undone.</strong></small>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: foldersCount > 0 ? 'Cannot Delete' : 'Delete Forever',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: foldersCount > 0 ? '#6c757d' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    allowOutsideClick: false,
                    didOpen: () => {
                        if (foldersCount > 0) {
                            Swal.getConfirmButton().disabled = true;
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && foldersCount === 0) {
                        deleteBaseFolder(baseFolderId);
                    }
                });
            });
        });

        // Base Folder CRUD Functions
        function createBaseFolder(name, description) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('name', name);
            formData.append('description', description);

            fetch('{{ route("base-folder.store") }}', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    return response.json().then(data => Promise.reject(data));
                })
                .then(data => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Base folder created successfully',
                        icon: 'success',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to create base folder.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        function updateBaseFolder(id, name, description) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            formData.append('name', name);
            formData.append('description', description);

            fetch(`/base-folder/${id}`, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    return response.json().then(data => Promise.reject(data));
                })
                .then(data => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Base folder updated successfully',
                        icon: 'success',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to update base folder.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        function deleteBaseFolder(id) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');

            fetch(`/base-folder/${id}`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Base folder deleted successfully',
                            icon: 'success',
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to delete base folder.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }
    });
</script>
