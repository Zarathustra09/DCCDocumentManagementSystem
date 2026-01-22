
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

<script>
    (function ($) {
        $(function () {
            const withDcnCount = {{ $entries->whereNotNull('dcn_no')->count() }};
            const withoutDcnCount = {{ $entries->whereNull('dcn_no')->count() }};

            $('#dcnTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        text: '<i class="bx bx-download"></i> Export to Excel',
                        className: 'btn btn-success btn-sm dt-export-btn',
                        action: function () {
                            const query = window.location.search;
                            window.location.href = 'dcn/export' + query;
                        }
                    }
                ],
                responsive: true,
                order: [],
                pageLength: 10,
                columnDefs: [
                    {orderable: false, targets: [10]}
                ],
                language: {
                    search: 'Search entries:',
                    lengthMenu: 'Show _MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries'
                },
                initComplete: function () {
                    const api = this.api();
                    const isChecked = $('#advancedToggle').is(':checked');
                    const btnClass = isChecked ? 'btn-active' : 'btn-inactive';
                    const toggleHtml = `
                        <div id="dtAdvancedToggle" style="display:inline-block; margin-left:12px; vertical-align:middle;">
                            <button type="button" id="dtAdvancedVisibleToggle" class="btn btn-sm ${btnClass}" aria-pressed="${isChecked ? 'true' : 'false'}" title="Toggle advanced filters">
                                <i class="bx bx-filter"></i>
                                <span class="d-none d-sm-inline">Advanced</span>
                            </button>
                        </div>
                    `;

                    $(api.table().container()).find('.dataTables_filter').append(toggleHtml);

                    if (window.dcnAdvanced && typeof window.dcnAdvanced.syncToggleButton === 'function') {
                        window.dcnAdvanced.syncToggleButton(isChecked);
                    }

                    $('#dtAdvancedVisibleToggle').on('click', function () {
                        const $checkbox = $('#advancedToggle');
                        $checkbox.prop('checked', !$checkbox.is(':checked')).trigger('change');
                    });

                    const badgeHtml = `
                        <div id="dcnToolbarCounts" aria-hidden="true">
                            <span class="badge bg-success">${withDcnCount} With DCN</span>
                            <span class="badge bg-warning text-dark">${withoutDcnCount} Without DCN</span>
                        </div>
                    `;
                    const $container = $(api.table().container());
                    const $exportBtn = $container.find('.dt-export-btn').first();

                    if ($exportBtn.length) {
                        $exportBtn.after(badgeHtml);
                    } else {
                        $container.find('.dt-buttons').append(badgeHtml);
                    }
                }
            });
        });
    })(jQuery);
</script>
