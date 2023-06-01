/**
 * Javascript for WCS4 progress.
 */

(function ($) {

    let SCOPE = 'progress';
    let FILTER_ID = '#wcs4-progresses-filter';
    let LIST_ID = '#wcs4-progresses-list-wrapper';

    $(document).ready(function () {
        WCS4_ADMIN.bind_search_handler(FILTER_ID, reload_html_view);
        WCS4_ADMIN.bind_sort_handler(LIST_ID, FILTER_ID, reload_html_view);
        WCS4_ADMIN.bind_edit_handler(SCOPE, set_entry_data_to_form);
        WCS4_ADMIN.bind_copy_handler(SCOPE, set_entry_data_to_form);
        WCS4_ADMIN.bind_delete_handler(SCOPE, function (data) {
            let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID))
            let $sortable = $('.sortable.sorted');
            reload_html_view(search_form_data, 'remove',
                $sortable.data('order-current-field'),
                $sortable.data('order-current-direction'));
        });
        bind_submit_handler();
        $(document).on('change.wcs4_progress_type', '#wcs4-progress-form [name="type"]', function () {
            bind_form_handler();
        });
        $(document).on('reset.wcs4_progress_type', '#wcs4-progress-form', function () {
            bind_form_handler();
        });
        bind_form_handler();
        bind_create_handler();
    });

    /**
     * Handles the Add Item button click event.
     */
    let bind_submit_handler = function () {
        let $form = $('#wcs4-progress-form');
        $form.find('#wcs4-submit-form').click(function (e) {
            e.preventDefault();
            let entry = {
                action: 'wcs_add_or_update_progress_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.form_field_value($form, 'teacher'),
                student_id: WCS4_LIB.form_field_value($form, 'student'),
                start_date: WCS4_LIB.form_field_value($form, 'start_date'),
                end_date: WCS4_LIB.form_field_value($form, 'end_date'),
                improvements: WCS4_LIB.form_field_value($form, 'improvements'),
                indications: WCS4_LIB.form_field_value($form, 'indications'),
                type: WCS4_LIB.form_field_value($form, 'type'),
            };
            WCS4_LIB.submit_entry(entry, function (data, status) {
                if (200 <= status && status < 300) {
                    // Let's refresh the day
                    let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID))
                    reload_html_view(search_form_data, 'fade',
                        $('.sortable.sorted').data('order-current-field'),
                        $('.sortable.sorted').data('order-current-direction')
                    );
                    // Clear improvements and indications.
                    WCS4_LIB.reset_to_add_mode('progress');
                }
            });
        });
    }
    /**
     * Updates dynamically a specific progress vi.
     */
    let reload_html_view = function (search_form_data, action, order_field, order_direction) {
        let entry = {
            action: 'wcs_get_progresses_html',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            teacher: search_form_data.teacher ? '#' + search_form_data.teacher : null,
            student: search_form_data.student ? '#' + search_form_data.student : null,
            subject: search_form_data.subject ? '#' + search_form_data.subject : null,
            date_from: search_form_data.date_from,
            date_upto: search_form_data.date_upto,
            type: search_form_data.type,
            created_at_from: search_form_data.created_at_from,
            created_at_upto: search_form_data.created_at_upto,
            order_field: order_field,
            order_direction: order_direction,
        };
        let $parent = $('#wcs4-progresses-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action);
        update_create_button();
    }

    /**
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        let $form = $('#wcs4-progress-form');
        if (Array.isArray(entry)) {
            $form.find('[name="type"][value="' + 'type.periodic' + '"]').prop('checked', true).change();
            let teacher_ids = [];
            let student_id = null;
            let improvements = '';
            let indications = '';
            entry.forEach(function (item) {
                if (Array.isArray(item.teacher_id)) {
                    item.teacher_id.forEach(function (id) {
                        teacher_ids.push(id);
                    });
                } else {
                    teacher_ids.push(item.teacher_id);
                }
                student_id = item.student_id;
                improvements += item.improvements + "\n";
                indications += item.indications + "\n";
            });
            $form.find('[name="teacher"]').val(teacher_ids);
            $form.find('[name="student"]').val(student_id);
            $form.find('[name="start_date"]').val(null);
            $form.find('[name="end_date"]').val(null);
            $form.find('[name="improvements"]').val(improvements);
            $form.find('[name="indications"]').val(indications);
        } else if (entry.hasOwnProperty('id')) {
            $form.find('[name="type"][value="' + entry.type + '"]').prop('checked', true).change();
            $form.find('[name="subject"]').val(entry.subject_id);
            $form.find('[name="teacher"]').val(entry.teacher_id);
            $form.find('[name="student"]').val(entry.student_id);
            $form.find('[name="start_date"]').val(entry.start_date);
            $form.find('[name="end_date"]').val(entry.end_date);
            $form.find('[name="improvements"]').val(entry.improvements);
            $form.find('[name="indications"]').val(entry.indications);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };
    /**
     * Handles the search button click event.
     */
    let bind_form_handler = function () {
        let $form = $('#wcs4-progress-form');
        switch ($form.find('[name="type"]:checked').val()) {
            default:
                $('#wcs4_progress_subject').closest('fieldset').hide();
                $('#wcs4_progress_student').closest('fieldset').hide();
                $('#wcs4_progress_teacher').closest('fieldset').hide();
                $('#wcs4_progress_teacher').attr('multiple', false).attr('size', null);
                $('#wcs4_progress_start_date').closest('fieldset').hide();
                $('#wcs4_progress_end_date').closest('fieldset').hide();
                $('#wcs4_progress_improvements').closest('fieldset').hide();
                $('#wcs4_progress_indications').closest('fieldset').hide();
                $('#wcs4_progress_buttons-wrapper button').attr('disabled', true).change();
                break;
            case 'type.partial':
                $('#wcs4_progress_subject').closest('fieldset').show();
                $('#wcs4_progress_student').closest('fieldset').show();
                $('#wcs4_progress_teacher').closest('fieldset').show();
                $('#wcs4_progress_teacher').attr('multiple', false).attr('size', null);
                $('#wcs4_progress_start_date').closest('fieldset').hide();
                $('#wcs4_progress_end_date').closest('fieldset').hide();
                $('#wcs4_progress_improvements').closest('fieldset').show();
                $('#wcs4_progress_indications').closest('fieldset').show();
                $('#wcs4_progress_buttons-wrapper button').attr('disabled', false).change();
                break;
            case 'type.periodic':
                $('#wcs4_progress_subject').closest('fieldset').hide();
                $('#wcs4_progress_student').closest('fieldset').show();
                $('#wcs4_progress_teacher').closest('fieldset').show();
                $('#wcs4_progress_teacher').attr('multiple', true).attr('size', 10);
                $('#wcs4_progress_start_date').closest('fieldset').show();
                $('#wcs4_progress_end_date').closest('fieldset').show();
                $('#wcs4_progress_improvements').closest('fieldset').show();
                $('#wcs4_progress_indications').closest('fieldset').show();
                $('#wcs4_progress_buttons-wrapper button').attr('disabled', false).change();
                break;
        }
    };

    let bind_create_handler = function () {
        update_create_button();
        $(document).on('click.wcs4-filter-toggle-student', '.search-filter', function (e) {
            setTimeout(function () {
                update_create_button();
            }, 300);
        });
        $(document).on('click.wcs4-reset-progress-button', '#wcs4-progresses-filter [type="reset"]', function (e) {
            setTimeout(function () {
                update_create_button();
            }, 300);
        });
        $(document).on('click.wcs4-create-progress-button', '#wcs4-progresses-filter  [data-action="generate"]', function (e) {
            let ids = [];
            jQuery('tr[id]').each(function (k, v) {
                ids.push(jQuery(v).data('id'));
            });
            WCS4_LIB.fetch_entry_data_to_form('progress', ids, set_entry_data_to_form, WCS4_LIB.reset_to_create_mode)
        });
    }

    let update_create_button = function () {
        if ('' === $('#search_wcs4_progress_student_id').val()) {
            $('#wcs4-progresses-filter [data-action="generate"]').attr('disabled', true)
        } else {
            $('#wcs4-progresses-filter [data-action="generate"]').attr('disabled', false)
        }
    }

})(jQuery);