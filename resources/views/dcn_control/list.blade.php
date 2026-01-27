@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class='bx bx-list-ul'></i>
                            <span id="logTitleLabel">Document Registration Logs</span>
                        </h3>
                        <button class="btn btn-secondary btn-sm" id="changeLogBtn">
                            <i class="bx bx-repeat"></i> Change Log Type
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3" id="logInfoAlert" style="display:none;"></div>

                        <div class="table-responsive">
                            {!! $dataTable->table(['class' => 'table table-striped table-hover'], true) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
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
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script>window.selectedLogType = 'build';</script>
{!! $dataTable->scripts() !!}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleEl = document.getElementById('logTitleLabel');
    const alertEl = document.getElementById('logInfoAlert');
    const changeBtn = document.getElementById('changeLogBtn');
    const getDt = () => window.LaravelDataTables && window.LaravelDataTables['logTable'];

    changeBtn.addEventListener('click', showLogWizard);
    showLogWizard();

    function showLogWizard() {
        const wizardHtml = `
            <div class="category-groups">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="category-option p-3 border rounded hover-shadow" data-log="build" style="cursor:pointer; transition:all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="me-3"><i class="bx bx-wrench fs-3 text-primary"></i></div>
                                <div>
                                    <h6 class="mb-1">Build Sheet Registration Log</h6>
                                    <small class="text-muted">Entries submitted by users outside organization 1.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="category-option p-3 border rounded hover-shadow" data-log="mechatronics" style="cursor:pointer; transition:all 0.2s;">
                            <div class="d-flex align-items-center">
                                <div class="me-3"><i class="bx bx-cog fs-3 text-info"></i></div>
                                <div>
                                    <h6 class="mb-1">Mechatronics Registration Log</h6>
                                    <small class="text-muted">Entries submitted by users with organization_id = 1.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bx bx-info-circle"></i>
                    <strong>Required:</strong> Please choose a log to continue.
                </div>
            </div>
        `;

        Swal.fire({
            title: '<i class="bx bx-list-ul"></i> Select Registration Log',
            html: wizardHtml,
            width: '750px',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: { htmlContainer: 'text-start' },
            didOpen: () => {
                const options = Swal.getPopup().querySelectorAll('.category-option');
                options.forEach(opt => {
                    opt.addEventListener('mouseenter', function () {
                        this.style.backgroundColor = '#f8f9fa';
                        this.style.borderColor = '#007bff';
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                    });
                    opt.addEventListener('mouseleave', function () {
                        this.style.backgroundColor = '';
                        this.style.borderColor = '';
                        this.style.transform = '';
                        this.style.boxShadow = '';
                    });
                    opt.addEventListener('click', function () {
                        window.selectedLogType = this.getAttribute('data-log') || 'build';
                        Swal.close();
                        onLogSelected();
                    });
                });
            }
        });
    }

    function onLogSelected() {
        const label = window.selectedLogType === 'mechatronics'
            ? 'Mechatronics Registration Log'
            : 'Build Sheet Registration Log';
        titleEl.textContent = label;
        alertEl.style.display = 'block';
        alertEl.innerHTML = window.selectedLogType === 'mechatronics'
            ? "<i class='bx bx-info-circle'></i> Showing entries where the originator's organization_id = 1."
            : "<i class='bx bx-info-circle'></i> Showing entries where the originator's organization_id ≠ 1.";
        const dt = getDt();
        if (dt) {
            dt.ajax.reload();
        }
    }
});
</script>
@endpush
