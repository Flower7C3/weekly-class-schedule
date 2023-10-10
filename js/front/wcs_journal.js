/**
 * Javascript for WCS4 journal.
 */

(function ($) {

    let SCOPE = 'journal';

    $(document).ready(function () {
        bind_submit_journal_handler();
        WCS4_FRONT.bind_edit_handler(SCOPE, set_entry_data_to_form);
        $(document).on('change.wcs4_journal_type', '#wcs4-journal-form [name="type"]', function () {
            bind_journal_form_handler();
        });
        bind_journal_form_handler();
    });

    /**
     * Handles the Add Item button click event.
     */
    let bind_submit_journal_handler = function () {
        let $form = $('#wcs4-journal-form');
        $form.find('[data-wcs4="submit-form"]').click(function (e) {
            e.preventDefault();
            let $spinner = $(this).closest('form').find('.spinner');
            $spinner.addClass('is-active');
            let entry = {
                action: 'wcs_add_journal_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.form_field_value($form, 'teacher'),
                student_id: WCS4_LIB.form_field_value($form, 'student'),
                date: WCS4_LIB.form_field_value($form, 'date'),
                start_time: WCS4_LIB.form_field_value($form, 'start_time'),
                end_time: WCS4_LIB.form_field_value($form, 'end_time'),
                topic: WCS4_LIB.form_field_value($form, 'topic'),
                type: WCS4_LIB.form_field_value($form, 'type'),
                row_id: WCS4_LIB.form_field_value($form, 'row_id'),
            };
            WCS4_LIB.submit_entry(entry, function (data, status) {
                $spinner.removeClass('is-active');
                // if (200 <= status && status < 300) {
                //     setTimeout(function () {
                //         // jQuery('#wcs4-journal-form').get(0).reset();
                //         jQuery('#wcs4-journal-modal').modal('hide');
                //     }, 2000);
                // }
            });
        });
    };


    let bind_journal_form_handler = function () {
        let $form = $('#wcs4-journal-form');
        let type = $form.find('[name="type"]:checked').val();
        if (typeof type === 'undefined' || '' === type) {
            return;
        }
        if (type.startsWith('type.absent_teacher.') || type === 'type.teacher_office_works') {
            $('#wcs4_journal_student').closest('fieldset').hide();
        } else {
            $('#wcs4_journal_student').closest('fieldset').show();
        }
    }

    /**
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        let $form = $('#wcs4-journal-form');
        if (entry.hasOwnProperty('id')) {
            // We got an entry.
            $form.find('[name="type"] [value="' + entry.type + '"]').prop('selected', true).select();
            $form.find('[name="subject"]').val(entry.subject_id);
            $form.find('[name="teacher"]').val(entry.teacher_id);
            $form.find('[name="student"]').val(entry.student_id);
            $form.find('[name="date"]').val(entry.date);
            $form.find('[name="start_time"]').val(entry.start_time);
            $form.find('[name="end_time"]').val(entry.end_time);
            $form.find('[name="topic"]').val(entry.topic);
            $form.find('[name="row_id"]').val(entry.id);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };
})(jQuery);