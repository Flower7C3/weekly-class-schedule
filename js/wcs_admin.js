/**
 * Javascript for WCS4 admin.
 */
let WCS4_ADMIN = (function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        bind_filter_handler();
        bind_show_hide_handler();
        bind_reset_settings();
        bind_import_cutoff_toggle();
        load_editor();
    });

    let search_form_process_and_push_history_state = function ($form) {
        let state = $form.serializeArray();
        let parts = [];
        let data = [];
        state.forEach(function (item) {
            parts.push(encodeURIComponent(item.name) + '=' + encodeURIComponent(item.value));
            data[item.name] = item.value;
        });
        let url = $form.attr('action') + (parts.length ? '?' + parts.join('&') : '');
        history.pushState(state, $('title').text(), url);
        return data;
    }


    let bind_search_handler = function (element, reload_html_view) {
        $(document).on('click.' + element + 'filter-submit-button-primary', element + ' [type="submit"].button-primary', function (e) {
            e.preventDefault();
            let search_form_data = search_form_process_and_push_history_state($(this).closest('form'))
            let $sortable = $('.sortable.sorted');
            reload_html_view(search_form_data, 'fade',
                $sortable.data('order-current-field'),
                $sortable.data('order-current-direction')
            );
        });
    };


    /**
     * Handles the Add Item button click event.
     */
    let bind_sort_handler = function (list_id, filter_id, reload_html_view) {
        $(document).on('click.' + list_id + 'wcs4-list-sort', list_id + ' [data-order-field][data-order-direction]', function (e) {
            let search_form_data = search_form_process_and_push_history_state($(filter_id))
            reload_html_view(search_form_data, 'fade',
                $(this).data('order-field'),
                $(this).data('order-direction')
            );
        });
    };

    let bind_edit_handler = function (scope, set_entry_data_to_form) {
        $(document).on('click.wcs4-edit-' + scope + '-button', 'tr[data-scope="' + scope + '"] .wcs4-edit-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form(scope, $(this).closest('tr').data('id'), set_entry_data_to_form, WCS4_LIB.reset_to_edit_mode);
        });
    };

    let bind_copy_handler = function (scope, set_entry_data_to_form) {
        $(document).on('click.wcs4-copy-' + scope + '-button', 'tr[data-scope="' + scope + '"] .wcs4-copy-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form(scope, $(this).closest('tr').data('id'), set_entry_data_to_form, WCS4_LIB.reset_to_copy_mode)
        });
    };

    let bind_delete_handler = function (scope, callback) {
        $(document).on('click.wcs4-delete-' + scope + '-button', 'tr[data-scope="' + scope + '"] .wcs4-delete-button', function (e) {
            let row_id = $(this).closest('tr').data('id');
            let entry = {
                action: 'wcs_delete_' + scope + '_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: row_id
            };
            WCS4_LIB.lock_tr(scope, row_id)
            WCS4_LIB.modify_entry(scope, entry, callback, WCS4_AJAX_OBJECT[scope].delete_warning);
        });
    }

    let bind_filter_handler = function () {
        let $resultsFilter = $('.results-filter');
        let $filterSubmit = $resultsFilter.find('.button-primary');
        $(document).on('change.wcs4-filter-select', 'select.search-filter', function (e) {
            let $selected = $(this).find('option:selected');
            let select_subject_id = $(this).data('select-subject-id');
            let value_subject = $selected.data('option-subject-val');
            $('#' + select_subject_id).val(value_subject).change();
            let select_teacher_id = $(this).data('select-teacher-id');
            let value_teacher = $selected.data('option-teacher-val');
            $('#' + select_teacher_id).val(value_teacher).change();
            $filterSubmit.click();
        });
        $(document).on('click.wcs4-filter-toggle', 'a.search-filter', function (e) {
            let select_id = $(this).data('select-id');
            let value = $(this).data('option-val');
            $('#' + select_id).val(value).change();
            $filterSubmit.click();
        });
        $(document).on('click.wcs4-filter-reset', '.results-filter [type=reset]', function (e) {
            $resultsFilter.find('select option:selected').prop('selected', false);
            setTimeout(function () {
                $filterSubmit.click();
            }, 500);
        });
    }

    /**
     * Handles the Show form button click event.
     */
    let bind_show_hide_handler = function () {
        let $wrapper = $('.wcs4-management-form-wrapper');
        let $form = $wrapper.find('form');
        let $resetForm = $('[data-wcs4="reset-form"]');
        $('#wcs4-show-form').on('click', function () {
            $wrapper.toggleClass('is-open');
        });
        $resetForm.on('click', function () {
            $wrapper.removeClass('is-open');
            WCS4_LIB.remove_message();
            $resetForm.hide();
            $form.one('change.reset', function () {
                $resetForm.show();
            });
            $(this).closest('form').find('input,select').trigger('change');
        });
        $form.one('change.reset', function () {
            $resetForm.show();
        });
    };

    let bind_reset_settings = function () {
        let $resetDb = $('#wcs4-reset-database');
        let $spinner = $resetDb.find('.spinner');
        let maintenanceDestructiveActions = {
            wcs_clear_taxonomy: true,
            wcs_clear_post_type: true,
            wcs_clear_schedules: true,
            wcs_clear_journals: true,
            wcs_clear_work_plans: true,
            wcs_clear_progresses: true,
            wcs_clear_snapshots: true,
            wcs_reset_settings: true,
            wcs_delete_everything: true,
        };
        $resetDb.find('button').on('click', function (e) {
            e.preventDefault();
            let $btn = $(this);
            let entry = {
                action: $btn.data('wcs-action') || $btn.prop('name'),
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                dry_run: $('#wcs4_dry_run').is(':checked') ? 1 : 0,
            };
            let wcsTarget = $btn.data('wcs-target');
            if (wcsTarget) {
                entry.target = wcsTarget;
            }
            if (entry.action === 'wcs_import_from_prefix') {
                entry.source_prefix = $('#wcs4_import_source_prefix').val();
                entry.cutoff_date = $('#wcs4_import_cutoff_date').val();
                entry.run_cutoff = $('#wcs4_import_run_cutoff').is(':checked') ? 1 : 0;
            }
            if (!window.confirm(WCS4_AJAX_OBJECT.reset_warning)) {
                return;
            }
            if (
                entry.dry_run === 0 &&
                Object.prototype.hasOwnProperty.call(maintenanceDestructiveActions, entry.action)
            ) {
                let finalMsg = WCS4_AJAX_OBJECT.reset_warning_final ||
                    'Dry run is off. This will permanently change or delete WCS4 data. Are you absolutely sure?';
                if (!window.confirm(finalMsg)) {
                    return;
                }
            }
            $spinner.addClass('is-active');
            $.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data, textStatus, jqXHR) {
                WCS4_LIB.show_message(data.response, jqXHR.status);
                let $details = $('#wcs4-ajax-text-wrapper');
                if ($details.length) {
                    $details.html('');
                    if (data.details) {
                        if (data.details.plan && Array.isArray(data.details.plan) && data.details.plan.length) {
                            let $plan = $('<div class="notice notice-info" style="margin-top:10px; padding:10px;">');
                            $plan.append($('<strong>').text(WCS4_AJAX_OBJECT.dry_run_plan_title || 'Dry run plan'));
                            let $ul = $('<ul style="margin: 8px 0 0 18px;">');
                            data.details.plan.forEach(function (line) {
                                $ul.append($('<li>').text(line));
                            });
                            $plan.append($ul);
                            $details.append($plan);
                        } else if (
                            typeof data.details.message === 'string'
                            && data.details.message !== ''
                            && String(data.response) === data.details.message
                        ) {
                            /* Human summary already in banner (e.g. import success); omit raw JSON. */
                        } else {
                            let $pre = $('<pre style="margin-top:10px; white-space:pre-wrap;">');
                            $pre.text(JSON.stringify(data.details, null, 2));
                            $details.append($pre);
                        }
                    }
                }
            }).fail(function (err) {
                // Show API error in the same UI as other WCS4 actions.
                let json = err.responseJSON || {};
                let msg = json.response;
                if (!msg && err.responseText) {
                    let t = String(err.responseText).trim().replace(/\s+/g, ' ');
                    if (t.length > 600) {
                        t = t.slice(0, 600) + '…';
                    }
                    msg = t || WCS4_AJAX_OBJECT.ajax_error;
                }
                if (!msg) {
                    msg = WCS4_AJAX_OBJECT.ajax_error;
                }
                let st = typeof err.status === 'number' ? err.status : 'error';
                WCS4_LIB.show_message(msg, st, json.errors || []);

                let $details = $('#wcs4-ajax-text-wrapper');
                if ($details.length) {
                    $details.html('');
                    if (json.details && json.details.plan && Array.isArray(json.details.plan) && json.details.plan.length) {
                        let $plan = $('<div class="notice notice-error" style="margin-top:10px; padding:10px;">');
                        $plan.append($('<strong>').text(WCS4_AJAX_OBJECT.dry_run_error_details_title || 'Dry run plan / error details'));
                        let $ul = $('<ul style="margin: 8px 0 0 18px;">');
                        json.details.plan.forEach(function (line) {
                            $ul.append($('<li>').text(line));
                        });
                        $plan.append($ul);
                        $details.append($plan);
                    } else if (
                        json.details
                        && typeof json.details.message === 'string'
                        && json.details.message !== ''
                        && String(msg) === json.details.message
                    ) {
                        /* Same as success: banner already shows the summary. */
                    } else if (json.details) {
                        let $pre = $('<pre style="margin-top:10px; white-space:pre-wrap;">');
                        $pre.text(JSON.stringify(json.details, null, 2));
                        $details.append($pre);
                    }
                }
            }).always(function () {
                $spinner.removeClass('is-active');
            });
        });
    };

    let bind_import_cutoff_toggle = function () {
        let $runCutoff = $('#wcs4_import_run_cutoff');
        let $cutoffDate = $('#wcs4_import_cutoff_date');
        if (!$runCutoff.length || !$cutoffDate.length) {
            return;
        }
        let update = function () {
            let enabled = $runCutoff.is(':checked');
            $cutoffDate.prop('disabled', !enabled);
        };
        update();
        $runCutoff.on('change', update);
    };


    let load_editor = function () {
        let $editors = $('.code_editor');
        if ($editors.length) {
            let editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    indentUnit: 2,
                    tabSize: 2,
                    mode: 'css',
                }
            );
            $editors.each(function (k) {
                wp.codeEditor.initialize($editors.eq(k), editorSettings);
            });
        }
    };

    return {
        bind_search_handler,
        bind_sort_handler,
        bind_edit_handler,
        bind_copy_handler,
        bind_delete_handler,
        search_form_process_and_push_history_state,
    }
})(jQuery);