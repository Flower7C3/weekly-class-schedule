/**
 * Scripts for the Weekly Class Schedule 4.0 front-end.
 */
(function ($) {

    $(document).ready(function () {
        WCS4_LIB.apply_qtip();
        bind_submit_journal_handler();
        bind_submit_progress_handler();
    });
    /**
     * Handles the Add Item button click event.
     */
    var bind_submit_journal_handler = function () {
        $('.wcs4-submit-journal-form').click(function (e) {
            var entry;
            e.preventDefault();
            entry = {
                action: 'wcs_add_journal_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value('wcs4_journal_subject'),
                teacher_id: WCS4_LIB.form_field_value('wcs4_journal_teacher'),
                student_id: WCS4_LIB.form_field_value('wcs4_journal_student'),
                date: $('#wcs4_journal_date').val(),
                start_time: $('#wcs4_journal_start_time').val(),
                end_time: $('#wcs4_journal_end_time').val(),
                topic: $('#wcs4_journal_topic').val()
            };
            WCS4_LIB.submit_entry(entry, function (data) {
                if (data.result === 'updated') {
                    jQuery('.wcs4-submit-journal-form').closest('form').get(0).reset()
                }
            });
        });
    }
    var bind_submit_progress_handler = function () {
        $('.wcs4-submit-progress-form').click(function (e) {
            var entry;
            e.preventDefault();
            entry = {
                action: 'wcs_add_progress_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value('wcs4_progress_subject'),
                teacher_id: WCS4_LIB.form_field_value('wcs4_progress_teacher'),
                student_id: WCS4_LIB.form_field_value('wcs4_progress_student'),
                type: 'partial',
                improvements: $('#wcs4_progress_improvements').val(),
                indications: $('#wcs4_progress_indications').val()
            };
            WCS4_LIB.submit_entry(entry, function (data) {
                if (data.result === 'updated') {
                    jQuery('.wcs4-submit-progress-form').closest('form').get(0).reset()
                }
            });
        });
    }

})(jQuery);
