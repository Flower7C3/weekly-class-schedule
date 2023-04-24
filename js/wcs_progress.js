/**
 * Javascript for WCS4 progress.
 */

(function ($) {

    let SCOPE = 'progress';
    let FILTER_ID = '#wcs4-progresses-filter';

    $(document).ready(function () {
        WCS4_ADMIN.bind_search_handler(FILTER_ID, reload_html_view);
        WCS4_ADMIN.bind_sort_handler(FILTER_ID, reload_html_view);
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
        bind_form_handler();
        bind_create_handler();
    });

    /**
     * Handles the Add Item button click event.
     */
    let bind_submit_handler = function () {
        $('.wcs4-submit-progress-form').click(function (e) {
            let entry;
            e.preventDefault();
            entry = {
                action: 'wcs_add_or_update_progress_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value('wcs4_progress_subject'),
                teacher_id: WCS4_LIB.form_field_value('wcs4_progress_teacher'),
                student_id: WCS4_LIB.form_field_value('wcs4_progress_student'),
                start_date: $('#wcs4_progress_start_date').val(),
                end_date: $('#wcs4_progress_end_date').val(),
                improvements: $('#wcs4_progress_improvements').val(),
                indications: $('#wcs4_progress_indications').val(),
                type: $('#wcs4_progress_type').val(),
            };
            WCS4_LIB.submit_entry(entry, function (data) {
                if (data.result === 'updated') {
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
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        if (Array.isArray(entry)) {
            $('#wcs4_progress_type').val('type.periodic').change();
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
            $('#wcs4_progress_teacher').val(teacher_ids);
            $('#wcs4_progress_student').val(student_id);
            $('#wcs4_progress_start_date').val(null);
            $('#wcs4_progress_end_date').val(null);
            $('#wcs4_progress_improvements').val(improvements);
            $('#wcs4_progress_indications').val(indications);
        } else if (entry.hasOwnProperty('id')) {
            $('#wcs4_progress_type').val(entry.type).change();
            $('#wcs4_progress_subject').val(entry.subject_id);
            $('#wcs4_progress_teacher').val(entry.teacher_id);
            $('#wcs4_progress_student').val(entry.student_id);
            $('#wcs4_progress_start_date').val(entry.start_date);
            $('#wcs4_progress_end_date').val(entry.end_date);
            $('#wcs4_progress_improvements').val(entry.improvements);
            $('#wcs4_progress_indications').val(entry.indications);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };

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
        let $parent = $('#wcs4-progress-events-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action);
        update_create_button();
    }
    /**
     * Handles the search button click event.
     */
    let bind_form_handler = function () {
        $(document).on('change.wcs4_progress_type', '#wcs4_progress_type', function () {
            switch ($(this).val()) {
                default:
                    $('#wcs4_progress_subject').closest('fieldset').show();
                    $('#wcs4_progress_teacher').attr('multiple', false).attr('size', null);
                    $('#wcs4_progress_start_date').closest('fieldset').show();
                    $('#wcs4_progress_end_date').closest('fieldset').show();
                    break;
                case 'type.partial':
                    $('#wcs4_progress_subject').closest('fieldset').show();
                    $('#wcs4_progress_teacher').attr('multiple', false).attr('size', null);
                    $('#wcs4_progress_start_date').closest('fieldset').hide();
                    $('#wcs4_progress_end_date').closest('fieldset').hide();
                    break;
                case 'type.periodic':
                    $('#wcs4_progress_subject').closest('fieldset').hide();
                    $('#wcs4_progress_teacher').attr('multiple', true).attr('size', 10);
                    $('#wcs4_progress_start_date').closest('fieldset').show();
                    $('#wcs4_progress_end_date').closest('fieldset').show();
                    break;
            }
        })
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
            $('#wcs4-progresses-filter  [data-action="generate"]').attr('disabled', true)
        } else {
            $('#wcs4-progresses-filter  [data-action="generate"]').attr('disabled', false)
        }
    }

})(jQuery);