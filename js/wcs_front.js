/**
 * Scripts for the Weekly Class Schedule 4.0 front-end.
 */
(function ($) {

    $(document).ready(function () {
        WCS4_LIB.apply_qtip();
        bind_submit_handler();
    });
    /**
     * Handles the Add Item button click event.
     */
    var bind_submit_handler = function () {
        $('.wcs4-submit-report-form').click(function (e) {
            var entry;
            e.preventDefault();
            entry = {
                action: 'add_or_update_report_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: $('input#wcs4_report_subject').length ? [$('input#wcs4_report_subject').val()] :($('select#wcs4_report_subject[multiple]').length ? $('select#wcs4_report_subject option:selected').toArray().map(item => item.value) : $('select#wcs4_report_subject option:selected').val()),
                teacher_id: $('input#wcs4_report_teacher').length ? [$('input#wcs4_report_teacher').val()] :($('select#wcs4_report_teacher[multiple]').length ? $('select#wcs4_report_teacher option:selected').toArray().map(item => item.value) : $('select#wcs4_report_teacher option:selected').val()),
                student_id: $('input#wcs4_report_student').length ? [$('input#wcs4_report_student').val()] :($('select#wcs4_report_student[multiple]').length ? $('select#wcs4_report_student option:selected').toArray().map(item => item.value) : $('select#wcs4_report_student option:selected').val()),
                date: $('#wcs4_report_date').val(),
                start_time: $('#wcs4_report_start_time').val(),
                end_time: $('#wcs4_report_end_time').val(),
                topic: $('#wcs4_report_topic').val()
            };
            WCS4_LIB.submit_entry(entry, function (data) {
                if (data.result === 'updated') {
                    jQuery('.wcs4-submit-report-form').closest('form').get(0).reset()
                }
            });
        });
    }

})(jQuery);
