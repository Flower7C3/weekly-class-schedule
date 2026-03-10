/**
 * Javascript for WCS4 admin.
 */
let WCS4_ADMIN = (function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        bind_filter_handler();
        bind_show_hide_handler();
        bind_colorpickers();
        bind_reset_settings();
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

    /**
     * Binds the colorpicker plugin to the selectors
     */
    let bind_colorpickers = function () {
        $(document).on('click.wcs_colorpicker', '.wcs_colorpicker', function (index) {
            let elementName = $(this).prop('id');
            $(this).ColorPicker({
                onChange: function (hsb, hex, rgb) {
                    $('#' + elementName).val(hex);
                    $('.' + elementName).css('background', '#' + hex);
                },
                onBeforeShow: function (hsb, hex, rgb) {
                    $(this).ColorPickerSetColor(this.value);
                }
            });
        });
    };

    let bind_reset_settings = function () {
        let $resetDb = $('#wcs4-reset-database');
        let $spinner = $resetDb.find('.spinner');
        $resetDb.find('button').on('click', function (e) {
            e.preventDefault();
            entry = {
                action: $(this).prop('name'),
                security: WCS4_AJAX_OBJECT.ajax_nonce,
            };
            if (!window.confirm(WCS4_AJAX_OBJECT.reset_warning)) {
                return;
            }
            $spinner.addClass('is-active');
            $.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
                WCS4_LIB.show_message(data.response, data.result);
            }).fail(function (err) {
                console.error(err);
            }).always(function () {
                $spinner.removeClass('is-active');
            });
        });
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