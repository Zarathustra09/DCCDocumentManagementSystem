

<script>
    (function ($) {
        window.dcnAdvanced = window.dcnAdvanced || {};

        window.dcnAdvanced.syncToggleButton = function (isChecked) {
            const $btn = $('#dtAdvancedToggle').find('button');
            if (!$btn.length) {
                return;
            }
            $btn.toggleClass('btn-active', isChecked);
            $btn.toggleClass('btn-inactive', !isChecked);
            $btn.attr('aria-pressed', isChecked ? 'true' : 'false');
        };

        window.dcnAdvanced.setAdvancedFieldsState = function (show) {
            const $filters = $('#advancedFilters');
            $filters.stop(true, true)[show ? 'slideDown' : 'slideUp']();
            $filters.find('select, input, button').prop('disabled', !show);
            window.dcnAdvanced.syncToggleButton(show);
        };

        $(function () {
            const $filters = $('#advancedFilters');
            const isChecked = $('#advancedToggle').is(':checked');

            $filters.find('select, input, button').prop('disabled', !isChecked);
            if (!isChecked) {
                $filters.hide();
            }
            window.dcnAdvanced.syncToggleButton(isChecked);

            $('#advancedToggle').on('change', function () {
                window.dcnAdvanced.setAdvancedFieldsState($(this).is(':checked'));
            });
        });
    })(jQuery);
</script>
