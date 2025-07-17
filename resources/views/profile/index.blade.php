@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills flex-column flex-md-row mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Account</a>
                        </li>
                    </ul>

                    <div class="card mb-4">
                        <h5 class="card-header">Profile Details</h5>
                        <!-- Account -->
                        <div class="card-body">
                            <form id="formProfileImageUpload" method="POST" action="{{ route('profile.uploadImage') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <img
                                        src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://placehold.co/100' }}"
                                        alt="user-avatar"
                                        class="d-block rounded"
                                        height="100"
                                        width="100"
                                        id="uploadedAvatar"
                                    />

                                    <div class="button-wrapper">
                                        <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                            <span class="d-none d-sm-block">Upload new photo</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input
                                                type="file"
                                                id="upload"
                                                class="account-file-input"
                                                name="profile_image"
                                                hidden
                                                accept="image/png, image/jpeg, image/jpg, image/gif"
                                                onchange="uploadImageImmediately(this)"
                                            />
                                        </label>
                                        <button type="button" class="btn btn-outline-secondary account-image-reset mb-4" onclick="resetImage()">
                                            <i class="bx bx-reset d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">Reset</span>
                                        </button>

                                        <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 2MB</p>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr class="my-0" />
                        <div class="card-body">
                            <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="name" class="form-label">Name</label>
                                        <input
                                            class="form-control @error('name') is-invalid @enderror"
                                            type="text"
                                            id="name"
                                            name="name"
                                            value="{{ old('name', $user->name) }}"
                                            required
                                            autofocus
                                        />
                                        @error('name')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}"
                                            required
                                            readonly
                                        />
                                        @error('email')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="created_at" class="form-label">Member Since</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="created_at"
                                            value="{{ $user->created_at->format('F Y') }}"
                                            readonly
                                        />
                                    </div>


                                    <div class="mb-3 col-md-6">
                                        <label for="role" class="form-label">Role</label>
                                        <div class="mt-2">
                                            @if($user->roles->isNotEmpty())
                                                @foreach($user->roles as $role)
                                                    <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary">No Role Assigned</span>
                                            @endif
                                        </div>
                                    </div>


{{--                                    <div class="mb-3 col-md-6">--}}
{{--                                        <label for="email_verified" class="form-label">Email Status</label>--}}
{{--                                        <div class="mt-2">--}}
{{--                                            @if($user->email_verified_at)--}}
{{--                                                <span class="badge bg-success">Email Verified</span>--}}
{{--                                            @else--}}
{{--                                                <span class="badge bg-warning">Email Not Verified</span>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bx bx-save"></i> Save changes
                                    </button>
                                    <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-x"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                        <!-- /Account -->
                    </div>

                    <div class="card mb-4">
                        <h5 class="card-header">Change Password</h5>
                        <div class="card-body">
                            <form id="formChangePassword" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input
                                            class="form-control"
                                            type="password"
                                            id="current_password"
                                            name="current_password"
                                            required
                                        />
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="mb-3 col-md-6">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input
                                            class="form-control"
                                            type="password"
                                            id="new_password"
                                            name="new_password"
                                            required
                                            minlength="8"
                                        />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                        <input
                                            class="form-control"
                                            type="password"
                                            id="new_password_confirmation"
                                            name="new_password_confirmation"
                                            required
                                            minlength="8"
                                        />
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bx bx-save"></i> Change Password
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="resetPasswordForm()">
                                        <i class="bx bx-x"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <h5 class="card-header">Delete Account</h5>
                        <div class="card-body">
                            <div class="mb-3 col-12 mb-0">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
                                    <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                                </div>
                            </div>
                            <form id="formAccountDeactivation" method="POST" action="{{ route('profile.destroy') }}">
                                @csrf
                                @method('DELETE')
                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="accountActivation"
                                        id="accountActivation"
                                        required
                                    />
                                    <label class="form-check-label" for="accountActivation">
                                        I confirm my account deactivation
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-danger deactivate-account">
                                    <i class="bx bx-trash"></i> Delete Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>
@endsection

@push('scripts')
    <script>


        // Password change functionality
        document.getElementById('formChangePassword').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Validate password confirmation
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;

            if (newPassword !== confirmPassword) {
                showSwal('error', 'New password and confirmation do not match.');
                return;
            }

            if (newPassword.length < 8) {
                showSwal('error', 'New password must be at least 8 characters long.');
                return;
            }

            // Show loading state
            submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Changing...';
            submitBtn.disabled = true;

            const formData = new FormData(form);

            fetch('{{ route('profile.updatePassword') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showSwal('success', data.message);
                        resetPasswordForm();
                    } else {
                        showSwal('error', data.message || 'Failed to change password');
                    }
                })
                .catch(error => {
                    console.error('Password change error:', error);
                    showSwal('error', 'Error changing password. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });

        function resetPasswordForm() {
            document.getElementById('formChangePassword').reset();
        }


        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('uploadedAvatar').src = e.target.result;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function uploadImageImmediately(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validate file size (2MB)
                if (file.size > 2048000) {
                    showSwal('error', 'File size must be less than 2MB.');
                    input.value = '';
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    showSwal('error', 'Only JPEG, PNG, JPG, and GIF files are allowed.');
                    input.value = '';
                    return;
                }

                const formData = new FormData();
                formData.append('profile_image', file);
                formData.append('_token', '{{ csrf_token() }}');

                // Show preview immediately
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('uploadedAvatar').src = e.target.result;
                };
                reader.readAsDataURL(file);

                // Show loading state
                const uploadBtn = document.querySelector('label[for="upload"]');
                const originalText = uploadBtn.innerHTML;
                uploadBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Uploading...';
                uploadBtn.style.pointerEvents = 'none';

                // Upload to server
                fetch('{{ route('profile.uploadImage') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showSwal('success', data.message);
                            document.getElementById('uploadedAvatar').src = data.image_url;
                        } else {
                            showSwal('error', data.message || 'Upload failed');
                            resetImageDisplay();
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        showSwal('error', 'Error uploading image. Please try again.');
                        resetImageDisplay();
                    })
                    .finally(() => {
                        // Reset button state
                        uploadBtn.innerHTML = originalText;
                        uploadBtn.style.pointerEvents = 'auto';
                    });
            }
        }

        function resetImage() {
            const uploadBtn = document.querySelector('.account-image-reset');
            const originalText = uploadBtn.innerHTML;

            // Show loading state
            uploadBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Removing...';
            uploadBtn.style.pointerEvents = 'none';

            // Send request to server
            fetch('{{ route('profile.resetImage') }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Reset the image to placeholder
                        document.getElementById('uploadedAvatar').src = 'https://placehold.co/100';
                        document.getElementById('upload').value = '';
                        showSwal('success', data.message);
                    } else {
                        showSwal('error', data.message || 'Failed to remove profile picture');
                    }
                })
                .catch(error => {
                    console.error('Reset error:', error);
                    showSwal('error', 'Error removing profile picture. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    uploadBtn.innerHTML = originalText;
                    uploadBtn.style.pointerEvents = 'auto';
                });
        }

        function resetImageDisplay() {
            document.getElementById('upload').value = '';
            document.getElementById('uploadedAvatar').src = '{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://placehold.co/100' }}';
        }

        function showSwal(type, message) {
            const config = {
                icon: type,
                title: type === 'success' ? 'Success' : 'Error',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    container: 'custom-swal-container',
                    popup: 'custom-swal-popup'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            };

            Swal.fire(config);
        }
    </script>
@endpush
