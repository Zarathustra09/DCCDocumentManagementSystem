@php($showHelpTour = true)
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
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

        @if($canApprove && $pendingCount > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class='bx bx-time-five text-warning'></i>
                                Pending Document Registrations
                                <span class="badge bg-warning text-dark">{{ $pendingCount }}</span>
                            </h4>
                            <a href="{{ route('document-registry.list') }}" class="btn btn-outline-primary btn-sm">
                                <i class='bx bx-show'></i> View All Pending
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                @if(isset($dataTable))
                                    {!! $dataTable->table(['class' => 'table table-hover table-striped'], true) !!}
                                @else
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
                                        <tbody></tbody>
                                    </table>
                                @endif
                            </div>

                            @if($pendingCount >= 10)
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
    <!-- / Content -->
</div>
@endsection

@push('scripts')
    {{-- remove manual DataTable init; use Yajra scripts when available --}}
    @if(isset($dataTable))
        {!! $dataTable->scripts() !!}
    @endif

    {{-- keep other page scripts (driverjs etc.) --}}
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
