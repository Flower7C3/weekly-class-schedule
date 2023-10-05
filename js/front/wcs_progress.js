/**
 * Javascript for WCS4 progress.
 */

(function ($) {

    let SCOPE = 'progress';

    $(document).ready(function () {
        bind_submit_progress_handler();
        WCS4_FRONT.bind_edit_handler(SCOPE, set_entry_data_to_form);
    });

    let bind_submit_progress_handler = function () {
        let $form = $('#wcs4-progress-form');
        $form.find('[data-wcs4="submit-form"]').click(function (e) {
            e.preventDefault();
            let $spinner = $(this).closest('form').find('.spinner');
            $spinner.addClass('is-active');
            let entry = {
                action: 'wcs_add_progress_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.form_field_value($form, 'teacher'),
                student_id: WCS4_LIB.form_field_value($form, 'student'),
                start_date: WCS4_LIB.form_field_value($form, 'start_date'),
                end_date: WCS4_LIB.form_field_value($form, 'end_date'),
                improvements: WCS4_LIB.form_field_value($form, 'improvements'),
                indications: WCS4_LIB.form_field_value($form, 'indications'),
                type: 'type.partial',
                row_id: WCS4_LIB.form_field_value($form, 'row_id'),
            };
            WCS4_LIB.submit_entry(entry, function (data, status) {
                $spinner.removeClass('is-active');
                if (200 <= status && status < 300) {
                    // jQuery('#wcs4-progress-form').get(0).reset();
                    jQuery('#wcs4-progress-modal').modal('hide');
                }
            });
        });
    };


    /**
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        let $form = $('#wcs4-progress-form');
        if (entry.hasOwnProperty('id')) {
            $form.find('[name="subject"]').val(entry.subject_id);
            $form.find('[name="teacher[]"]').val(entry.teacher_id);
            $form.find('[name="student"]').val(entry.student_id);
            $form.find('[name="start_date"]').val(entry.start_date);
            $form.find('[name="end_date"]').val(entry.end_date);
            $form.find('[name="improvements"]').val(entry.improvements);
            $form.find('[name="indications"]').val(entry.indications);
            $form.find('[name="row_id"]').val(entry.id);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };

})(jQuery);