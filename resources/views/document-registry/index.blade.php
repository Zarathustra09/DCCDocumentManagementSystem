@php($showHelpTour = true)
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class='bx bx-folder-open'></i> My Registrations</h3>
                        @can('submit document for approval')
                            <a href="{{ route('document-registry.create') }}" class="btn btn-primary" id="create-registration-btn">
                                <i class='bx bx-plus'></i> Register New Document
                            </a>
                        @endcan
                    </div>

                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('document-registry.index') }}" class="mb-4" id="registry-filter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="status" class="form-select" id="filter-status">
                                        <option value="">All Statuses</option>
                                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Implemented" {{ request('status') == 'Implemented' ? 'selected' : '' }}>Implemented</option>
                                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="category_id" class="form-select" id="filter-category">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary" id="filter-btn">Filter</button>
                                </div>
                            </div>
                        </form>

                        <!-- Entries Table -->
                        <div class="table-responsive">
                            {!! $dataTable->table(['class' => 'table table-striped table-hover'], true) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>

<style>
.badge {
    font-size: 0.85em;
    padding: 0.375rem 0.75rem;
}
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}
.badge.bg-success {
    background-color: #198754 !important;
    color: #ffffff !important;
}
.badge.bg-danger {
    background-color: #dc3545 !important;
    color: #ffffff !important;
}
</style>
@endsection

@push('driverjs')
<script>
window.addEventListener('start-driverjs-tour', function() {
    const driver = window.driver.js.driver;
    driver({
        showProgress: true,
        steps: [
            {
                element: '#create-registration-btn',
                popover: {
                    title: 'Register New Document',
                    description: 'Click here to register a new document.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#filter-status',
                popover: {
                    title: 'Status Filter',
                    description: 'Filter registrations by their status.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#filter-category',
                popover: {
                    title: 'Category Filter',
                    description: 'Filter registrations by category.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#filter-btn',
                popover: {
                    title: 'Apply Filters',
                    description: 'Click to apply the selected filters.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#documentRegistry',
                popover: {
                    title: 'Registrations Table',
                    description: 'View and manage all your document registrations here.',
                    side: 'top',
                    align: 'center'
                }
            }
        ]
    }).drive();
});
</script>
@endpush

@push('scripts')
{!! $dataTable->scripts() !!}
<script>
document.getElementById('registry-filter-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const tables = window.LaravelDataTables || {};
    if (tables.documentRegistry && tables.documentRegistry.ajax) {
        tables.documentRegistry.ajax.reload();
    } else {
        this.submit();
    }
});
</script>
@endpush
