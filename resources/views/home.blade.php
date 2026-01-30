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

        @if(isset($pendingTable) || isset($noDcnTable))
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class='bx bx-time-five text-warning'></i>
                                Registrations Requiring Attention
                            </h4>
                            <a href="{{ route('document-registry.list') }}" class="btn btn-outline-primary btn-sm" id="view-all-link">
                                <i class='bx bx-show'></i> <span id="view-all-text">View All Pending Registrations</span>
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="nav-align-top mb-3">
                                <ul class="nav nav-tabs" role="tablist" id="homeTabs">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" role="tab"
                                                data-bs-toggle="tab" data-bs-target="#tab-pending-registrations"
                                                aria-selected="true"
                                                data-view-route="{{ route('document-registry.list') }}"
                                                data-view-text="View All Pending Registrations">
                                            <i class='bx bx-time'></i> Pending Registrations
                                            <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab"
                                                data-bs-toggle="tab" data-bs-target="#tab-no-dcn"
                                                aria-selected="false"
                                                data-view-route="{{ route('dcn.list') }}"
                                                data-view-text="View All DCN Assignments">
                                            <i class='bx bx-barcode'></i> No DCN Assigned
                                            <span class="badge bg-primary ms-1">{{ $noDcnCount }}</span>
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab-pending-registrations" role="tabpanel">
                                        <div class="table-responsive">
                                            @isset($pendingTable)
                                                {!! $pendingTable->table(['class' => 'table table-hover table-striped w-100'], true) !!}
                                            @endisset
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tab-no-dcn" role="tabpanel">
                                        <div class="table-responsive">
                                            @isset($noDcnTable)
                                                {!! $noDcnTable->table(['class' => 'table table-hover table-striped w-100'], true) !!}
                                            @endisset
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($pendingCount == 0 && $noDcnCount == 0)
                                <div class="alert alert-success text-center mb-0">
                                    <i class='bx bx-check-circle'></i> No pending or unassigned registrations.
                                </div>
                            @endif
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
    @isset($pendingTable)
        {!! $pendingTable->scripts() !!}
    @endisset
    @isset($noDcnTable)
        {!! $noDcnTable->scripts() !!}
    @endisset

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabButtons = document.querySelectorAll('#homeTabs button[data-bs-toggle="tab"]');
            const viewAllLink = document.getElementById('view-all-link');
            const viewAllText = document.getElementById('view-all-text');

            tabButtons.forEach(btn => {
                btn.addEventListener('shown.bs.tab', function () {
                    const target = this.getAttribute('data-bs-target');
                    const id = target === '#tab-no-dcn' ? 'noDcnRegistrationsTable' : 'pendingRegistrationsTable';
                    const dt = window.LaravelDataTables ? window.LaravelDataTables[id] : null;
                    if (dt) dt.columns.adjust().draw(false);

                    // Update "View All" link and text
                    const viewRoute = this.getAttribute('data-view-route');
                    const viewText = this.getAttribute('data-view-text');
                    if (viewAllLink && viewRoute) {
                        viewAllLink.href = viewRoute;
                    }
                    if (viewAllText && viewText) {
                        viewAllText.textContent = viewText;
                    }
                });
            });
        });
    </script>

    {{-- keep existing driverjs tour --}}
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
