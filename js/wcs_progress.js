/**
 * Javascript for WCS4 progress.
 */

(function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        bind_form_handler();
        bind_search_handler();
        bind_sort_handler();
        bind_submit_handler();
        bind_edit_handler();
        bind_copy_handler();
        bind_create_handler();
        bind_delete_handler();
    });

    /**
     * Handles the search button click event.
     */
    var bind_form_handler = function () {
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
    var bind_search_handler = function () {
        $(document).on('click.wcs-progresses-search', '#wcs-progresses-search', function (e) {
            e.preventDefault();
            reload_html_view(
                $('#search_wcs4_progress_teacher_id').val(),
                $('#search_wcs4_progress_student_id').val(),
                $('#search_wcs4_progress_subject_id').val(),
                $('#search_wcs4_progress_date_from').val(),
                $('#search_wcs4_progress_date_upto').val(),
                $('#search_wcs4_progress_type').val(),
                $('.sortable.sorted').data('order-current-field'),
                $('.sortable.sorted').data('order-current-direction'),
                'fade'
            );
        });
    };

    /**
     * Handles the Add Item button click event.
     */
    var bind_sort_handler = function () {
        $(document).on('click.wcs4-events-list-sort', '#wcs4-progress-events-list-wrapper [data-order-field][data-order-direction]', function (e) {
            reload_html_view(
                $('#search_wcs4_progress_teacher_id').val(),
                $('#search_wcs4_progress_student_id').val(),
                $('#search_wcs4_progress_subject_id').val(),
                $('#search_wcs4_progress_date_from').val(),
                $('#search_wcs4_progress_date_upto').val(),
                $('#search_wcs4_progress_type').val(),
                $(this).data('order-field'),
                $(this).data('order-direction'),
                'fade')
            ;
        });
    };

    /**
     * Handles the Add Item button click event.
     */
    var bind_submit_handler = function () {
        $('.wcs4-submit-progress-form').click(function (e) {
            var entry;
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
                    reload_html_view(
                        $('#search_wcs4_progress_teacher_id').val(),
                        $('#search_wcs4_progress_student_id').val(),
                        $('#search_wcs4_progress_subject_id').val(),
                        $('#search_wcs4_progress_date_from').val(),
                        $('#search_wcs4_progress_date_upto').val(),
                        $('#search_wcs4_progress_type').val(),
                        $('.sortable.sorted').data('order-current-field'),
                        $('.sortable.sorted').data('order-current-direction'),
                        'fade'
                    );
                    // Clear improvements and indications.
                    $('#wcs4_progress_improvements').val('');
                    $('#wcs4_progress_indications').val('');
                    WCS4_LIB.reset_to_add_mode('progress');
                }
            });
        });
    }

    /**
     * Handles the edit button click event.
     */
    var bind_edit_handler = function () {
        $(document).on('click.wcs4-edit-progress-button', '.wcs4-edit-progress-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form('progress', $(this).closest('tr').data('id'), set_entry_data_to_form, WCS4_LIB.reset_to_edit_mode);
        });
    }

    /**
     * Handles the copy button click event.
     */
    var bind_copy_handler = function () {
        $(document).on('click.wcs4-copy-progress-button', '.wcs4-copy-progress-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form('progress', $(this).closest('tr').data('id'), set_entry_data_to_form, WCS4_LIB.reset_to_copy_mode)
        });
    }

    var bind_create_handler = function () {
        $(document).on('click.wcs4-create-progress-button', '#wcs-progresses-create', function (e) {
            var ids = [];
            jQuery('tr[id]').each(function (k, v) {
                ids.push(jQuery(v).data('id'));
            });
            WCS4_LIB.fetch_entry_data_to_form('progress', ids, set_entry_data_to_form, WCS4_LIB.reset_to_create_mode)
        });
    }

    /**
     * Handles the delete button click event.
     */
    var bind_delete_handler = function () {
        $(document).on('click.wcs4-delete-progress-button', '.wcs4-delete-progress-button', function (e) {
            var entry = {
                action: 'wcs_delete_progress_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: $(this).closest('tr').data('id')
            };
            WCS4_LIB.modify_entry('progress', entry, function (data) {
                // Let's refresh the date
                reload_html_view(
                    $('#search_wcs4_progress_teacher_id').val(),
                    $('#search_wcs4_progress_student_id').val(),
                    $('#search_wcs4_progress_subject_id').val(),
                    $('#search_wcs4_progress_date_from').val(),
                    $('#search_wcs4_progress_date_upto').val(),
                    $('.sortable.sorted').data('order-current-field'),
                    $('.sortable.sorted').data('order-current-direction'),
                    'remove'
                );
            }, WCS4_AJAX_OBJECT['progress'].delete_warning);
        });
    }

    /**
     * Updates dynamically a specific progress vi.
     */
    var reload_html_view = function (teacher, student, subject, date_from, date_upto, type, order_field, order_direction, action) {
        var page = $('#search_wcs4_page').val();
        var state = {
            'page': page,
            'teacher': teacher,
            'student': student,
            'subject': subject,
            'date_from': date_from,
            'date_upto': date_upto,
            'type': type,
            'order_field': order_field,
            'order_direction': order_direction,
        };
        var url = $('#wcs-progresses-filter').attr('action')
            + '?page=' + page
            + '&teacher=' + teacher
            + '&student=' + student
            + '&subject=' + subject
            + '&date_from=' + date_from
            + '&date_upto=' + date_upto
            + '&type=' + type
            + '&order_field=' + order_field
            + '&order_direction=' + order_direction
        ;
        history.pushState(state, $('title').text(), url);
        entry = {
            action: 'wcs_get_progresses_html',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            teacher: teacher ? '#' + teacher : null,
            student: student ? '#' + student : null,
            subject: subject ? '#' + subject : null,
            date_from: date_from,
            date_upto: date_upto,
            type: type,
            order_field: order_field,
            order_direction: order_direction,
        };
        var $parent = $('#wcs4-progress-events-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action)
    }

    /**
     * Fill up form with entry data
     */
    var set_entry_data_to_form = function (entry) {
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

})(jQuery);