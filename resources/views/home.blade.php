@php($showHelpTour = true)
@extends('layouts.app')

@section('content')
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header" id="dashboard-header">
                    <h3 class="card-title"><i class='bx bx-home'></i> Dashboard</h3>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <h4>Welcome back, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted">{{ __('You are logged in and ready to manage your documents.') }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group" aria-label="Document Actions">
                                <a href="{{ route('document-registry.create') }}" class="btn btn-primary" id="create-registration-btn">
                                    <i class='bx bx-file-find'></i> Create Registration
                                </a>
                                <a href="{{ route('document-registry.index') }}" class="btn btn-outline-primary" id="view-registrations-btn">
                                    <i class='bx bx-file-find'></i> View My Registrations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($canApprove && $pendingRegistrations->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class='bx bx-time-five text-warning'></i>
                            Pending Document Registrations
                            <span class="badge bg-warning text-dark">{{ $pendingRegistrations->count() }}</span>
                        </h4>
                        <a href="{{ route('document-registry.list') }}" class="btn btn-outline-primary btn-sm">
                            <i class='bx bx-show'></i> View All Pending
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="documentRegistry">
                                <thead class="table-light">
                                    <tr>
                                        <th>Control No.</th>
                                        <th>Document Details</th>
                                        <th>Originator</th>
                                        <th>Device Name</th>
                                        <th>Submitted By</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRegistrations as $entry)
                                        <tr>
                                            <td>
                                                <strong>{{ $entry->control_no }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $entry->document_title }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $entry->document_no }} Rev. {{ $entry->revision_no }}
                                                    </small>
                                                    @if($entry->customer)
                                                        <br>
                                                        <small class="text-info">Customer: {{ $entry->customer->name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $entry->originator_name }}
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                        <small class="text-muted">{{ $entry->device_name ?? '-' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $entry->submittedBy->name }}
                                                    <br>
                                                    <small class="text-muted">{{ $entry->submitted_at->format('m/d/Y') }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $entry->submitted_at->format('g:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <i class='bx bx-time'></i> {{ $entry->status->name }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="bx bx-cog"></i> Manage
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('document-registry.show', $entry) }}">
                                                            <i class="bx bx-show me-2"></i> View Details
                                                        </a>
                                                        @if($entry->status->name === 'Pending' &&
                                                            $entry->submitted_by === auth()->id() &&
                                                            auth()->user()->can('edit document registration details'))
                                                            <a class="dropdown-item" href="{{ route('document-registry.edit', $entry) }}">
                                                                <i class="bx bx-edit-alt me-2"></i> Edit
                                                            </a>
                                                        @endif
                                                        @if($entry->status->name === 'Pending' &&
                                                            $entry->submitted_by === auth()->id() &&
                                                            auth()->user()->can('withdraw document submission'))
                                                            <form action="{{ route('document-registry.withdraw', $entry) }}"
                                                                  method="POST" onsubmit="return confirm('Are you sure you want to withdraw this submission?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bx bx-trash me-2"></i> Withdraw
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($pendingRegistrations->count() >= 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('document-registry.list')}}" class="btn btn-primary">
                                    <i class='bx bx-show'></i> View All Pending Registrations
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif($canApprove)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class='bx bx-check-circle text-success' style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No Pending Registrations</h4>
                        <p class="text-muted">All document registrations have been processed.</p>
                        <a href="{{ route('document-registry.index') }}" class="btn btn-primary">
                            <i class='bx bx-file-find'></i> View My Registrations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#documentRegistry').DataTable({
            "pageLength": 10,
            "order": [[ 2, "desc" ]]
        });
    });
</script>
@endpush

@push('driverjs')
<script>
window.addEventListener('start-driverjs-tour', function() {
    const driver = window.driver.js.driver;
    driver({
        showProgress: true,
        steps: [
            {
                element: '#menu-home',
                popover: {
                    title: 'Home',
                    description: 'Go to your dashboard overview.',
                    side: 'right',
                    align: 'start'
                }
            },
            {
                element: '#menu-my-documents',
                popover: {
                    title: 'My Documents',
                    description: 'Access all your documents here.',
                    side: 'right',
                    align: 'start'
                }
            },
            {
                element: '#menu-my-registrations',
                popover: {
                    title: 'My Registrations',
                    description: 'View and manage your document registrations.',
                    side: 'right',
                    align: 'start'
                }
            },
            {
                element: '.dropdown-user',
                popover: {
                    title: 'Profile Menu',
                    description: 'Access your profile, role, and logout options here.',
                    side: 'bottom',
                    align: 'end'
                }
            },
            {
                element: '#create-registration-btn',
                popover: {
                    title: 'Create Registration',
                    description: 'Click here to create a new document registration.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#view-registrations-btn',
                popover: {
                    title: 'View My Registrations',
                    description: 'Click here to view your submitted document registrations.',
                    side: 'bottom',
                    align: 'start'
                }
            }
        ]
    }).drive();
});
</script>
@endpush
