/**
 * Javascript for WCS4 work_plan.
 */

(function ($) {

    let SCOPE = 'work-plan';
    let FILTER_ID = '#wcs4-work-plans-filter';
    let LIST_ID = '#wcs4-work-plans-list-wrapper';

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
        $(document).on('change.wcs4_work_plan_type', '#wcs4-work-plan-form [name="type"]', function () {
            bind_form_handler();
        });
        $(document).on('reset.wcs4_work_plan_type', '#wcs4-work-plan-form', function () {
            bind_form_handler();
        });
        bind_form_handler();
        bind_create_handler();
    });

    /**
     * Handles the Add Item button click event.
     */
    let bind_submit_handler = function () {
        let $form = $('#wcs4-work-plan-form');
        $form.find('[data-wcs4="submit-form"]').click(function (e) {
            e.preventDefault();
            let entry = {
                action: 'wcs_add_or_update_work_plan_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.get_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.get_field_value($form, 'teacher'),
                student_id: WCS4_LIB.get_field_value($form, 'student'),
                start_date: WCS4_LIB.get_field_value($form, 'start_date'),
                end_date: WCS4_LIB.get_field_value($form, 'end_date'),
                diagnosis: WCS4_LIB.get_field_value($form, 'diagnosis'),
                strengths: WCS4_LIB.get_field_value($form, 'strengths'),
                goals: WCS4_LIB.get_field_value($form, 'goals'),
                methods: WCS4_LIB.get_field_value($form, 'methods'),
                type: WCS4_LIB.get_field_value($form, 'type'),
            };
            WCS4_LIB.submit_entry(entry, function (data, status) {
                if (200 <= status && status < 300) {
                    let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID));
                    let $sortable = $('.sortable.sorted');
                    reload_html_view(search_form_data, 'fade',
                        $sortable.data('order-current-field'),
                        $sortable.data('order-current-direction'));
                    // Clear diagnosis, strengths, goals, methods
                    WCS4_LIB.reset_to_add_mode('work-plan');
                }
            });
        });
    }

    /**
     * Updates dynamically a specific work-plan vi.
     */
    let reload_html_view = function (search_form_data, action, order_field, order_direction) {
        let entry = {
            action: 'wcs_get_work_plans_html',
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
        let $parent = $('#wcs4-work-plans-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action)
        update_create_button();
    }

    /**
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        let $form = $('#wcs4-work-plan-form');
        if (Array.isArray(entry)) {
            $form.find('[name="type"][value="type.cumulative"]').prop('checked', true).change();
            let teacher_ids = [];
            let student_id = null;
            let diagnosis = '';
            let strengths = '';
            let goals = '';
            let methods = '';
            entry.forEach(function (item) {
                if (Array.isArray(item.teacher_id)) {
                    item.teacher_id.forEach(function (id) {
                        teacher_ids.push(id);
                    });
                } else {
                    teacher_ids.push(item.teacher_id);
                }
                student_id = item.student_id;
                diagnosis += item.diagnosis + "\n";
                strengths += item.strengths + "\n";
                goals += item.goals + "\n";
                methods += item.methods + "\n";
            });
            WCS4_LIB.set_select_value($form, 'teacher', teacher_ids);
            WCS4_LIB.set_select_value($form, 'student', student_id);
            WCS4_LIB.set_input_value($form, 'start_date', null);
            WCS4_LIB.set_input_value($form, 'end_date', null);
            WCS4_LIB.set_input_value($form, 'diagnosis', diagnosis);
            WCS4_LIB.set_input_value($form, 'strengths', strengths);
            WCS4_LIB.set_input_value($form, 'goals', goals);
            WCS4_LIB.set_input_value($form, 'methods', methods);
        } else if (entry.hasOwnProperty('id')) {
            WCS4_LIB.set_radio_value($form, 'type', entry.type, true);
            WCS4_LIB.set_select_value($form, 'subject', entry.subject_id);
            WCS4_LIB.set_select_value($form, 'teacher', entry.teacher_id);
            WCS4_LIB.set_select_value($form, 'student', entry.student_id);
            WCS4_LIB.set_input_value($form, 'start_date', entry.start_date);
            WCS4_LIB.set_input_value($form, 'end_date', entry.end_date);
            WCS4_LIB.set_input_value($form, 'diagnosis', entry.diagnosis);
            WCS4_LIB.set_input_value($form, 'strengths', entry.strengths);
            WCS4_LIB.set_input_value($form, 'goals', entry.goals);
            WCS4_LIB.set_input_value($form, 'methods', entry.methods);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };
    /**
     * Handles the search button click event.
     */
    let bind_form_handler = function () {
        let $form = $('#wcs4-work-plan-form');
        let $subject = $('#wcs4_work_plan_subject'), $teacher = $('#wcs4_work_plan_teacher'), $student = $('#wcs4_work_plan_student');
        let $startDate = $('#wcs4_work_plan_start_date'), $endDate = $('#wcs4_work_plan_end_date');
        let $diagnosis = $('#wcs4_work_plan_diagnosis'), $strengths = $('#wcs4_work_plan_strengths'), $goals = $('#wcs4_work_plan_goals'), $methods = $('#wcs4_work_plan_methods');
        let $buttons = $('#wcs4_work_plan_buttons-wrapper button');
        switch ($form.find('[name="type"]:checked').val()) {
            default:
                $subject.closest('fieldset').hide();
                $teacher.closest('fieldset').hide();
                $teacher.prop('multiple', false).removeAttr('size');
                $student.closest('fieldset').hide();
                $startDate.closest('fieldset').hide();
                $endDate.closest('fieldset').hide();
                $diagnosis.closest('fieldset').hide();
                $strengths.closest('fieldset').hide();
                $goals.closest('fieldset').hide();
                $methods.closest('fieldset').hide();
                $buttons.prop('disabled', true).change();
                break;
            case 'type.partial':
                $subject.closest('fieldset').show();
                $teacher.closest('fieldset').show();
                $teacher.prop('multiple', false).removeAttr('size');
                $student.closest('fieldset').show();
                $startDate.closest('fieldset').show();
                $endDate.closest('fieldset').show();
                $diagnosis.closest('fieldset').show();
                $strengths.closest('fieldset').show();
                $goals.closest('fieldset').show();
                $methods.closest('fieldset').show();
                $buttons.prop('disabled', false).change();
                break;
            case 'type.cumulative':
                $subject.closest('fieldset').hide();
                $teacher.closest('fieldset').show();
                $teacher.prop('multiple', true).attr('size', 10);
                $student.closest('fieldset').show();
                $startDate.closest('fieldset').show();
                $endDate.closest('fieldset').show();
                $diagnosis.closest('fieldset').show();
                $strengths.closest('fieldset').show();
                $goals.closest('fieldset').show();
                $methods.closest('fieldset').show();
                $buttons.prop('disabled', false).change();
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
        $(document).on('click.wcs4-reset-work-plan-button', '#wcs4-work-plans-filter [type="reset"]', function (e) {
            setTimeout(function () {
                update_create_button();
            }, 300);
        });
        $(document).on('click.wcs4-create-work-plan-button', '#wcs4-work-plans-filter [data-action="generate"]', function (e) {
            let ids = [];
            jQuery('tr[data-id]').each(function (k, v) {
                ids.push(jQuery(v).data('id'));
            });
            WCS4_LIB.fetch_entry_data_to_form('work-plan', ids, set_entry_data_to_form, WCS4_LIB.reset_to_create_mode)
        });
    }

    let update_create_button = function () {
        let $generateBtn = $(FILTER_ID + ' [data-action="generate"]');
        $generateBtn.prop('disabled', '' === $('#search_wcs4_work_plan_student_id').val());
    }

})(jQuery);