@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Account Settings /</span> Account
            </h4>

            <div class="alert alert-info">
                <strong>Note:</strong> To change your account details, please use the SPEARS system.
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <h5 class="card-header">Profile Details</h5>
                        <div class="card-body">
                            <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
                                <img
                                    src="{{ auth()->user()->profile_image ? 'http://172.16.0.3:8012/images/' . ltrim(auth()->user()->profile_image, '/') : 'https://placehold.co/40' }}"
                                    alt="user-avatar"
                                    class="d-block rounded"
                                    height="100"
                                    width="100"
                                    id="uploadedAvatar"
                                />
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Name</label>
                                    <input class="form-control" type="text" value="{{ $user->name }}" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">E-mail</label>
                                    <input class="form-control" type="text" value="{{ $user->email }}" readonly />
                                </div>
                               @php
                                   $invalidDates = [
                                       '0000-00-00',
                                       '0000-00-00 00:00:00',
                                       '2025-02-11 00:00:00',
                                   ];
                                   $createdOnRaw = $user->getRawOriginal('created_on');
                                   $validCreatedOn = $user->created_on && !in_array($createdOnRaw, $invalidDates, true);
                               @endphp
                               <div class="mb-3 col-md-6">
                                   <label class="form-label">Member Since</label>
                                   <input class="form-control" type="text"
                                          value="{{ $validCreatedOn ? optional($user->created_on)->format('F Y') : 'N/A' }}"
                                          readonly />
                               </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Role</label>
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
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Employee No</label>
                                    <input class="form-control" type="text" value="{{ $user->employee_no }}" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Username</label>
                                    <input class="form-control" type="text" value="{{ $user->username }}" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Birthdate</label>
                                    <input class="form-control" type="text" value="{{ $user->birthdate ? $user->birthdate->format('Y-m-d') : '' }}" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Gender</label>
                                    <input class="form-control" type="text" value="{{ $user->gender }}" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Contact Info</label>
                                    <input class="form-control" type="text" value="{{ $user->contact_info }}" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Address</label>
                                    <input class="form-control" type="text" value="{{ $user->address }}" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Date Hired</label>
                                    <input class="form-control" type="text" value="{{ $user->datehired ? $user->datehired->format('Y-m-d') : '' }}" readonly />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Barcode</label>
                                    <input class="form-control" type="text" value="{{ $user->barcode }}" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>
@endsection
