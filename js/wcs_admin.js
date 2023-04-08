/**
 * Javascript for WCS4 admin.
 */
(function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        bind_filter_handler();
        bind_show_hide_handler();
        bind_colorpickers();
        bind_reset_settings();
        load_editor();
    });

    var bind_filter_handler = function () {
        $(document).on('click.wcs-filter-toggle', '.search-filter', function (e) {
            var select_id = $(this).data('select-id');
            var value = $(this).data('option-val');
            $('#' + select_id).val(value).change();
            $('.results-filter .button-primary').click();
        });
        $(document).on('click.wcs-filter-reset', '.results-filter [type=reset]', function (e) {
            $('.results-filter select option:selected').attr('selected', false);
            setTimeout(function () {
                $('.results-filter .button-primary').click();
            }, 500);
        });
    }

    /**
     * Handles the Show form button click event.
     */
    var bind_show_hide_handler = function () {
        $('#wcs4-show-form').click(function () {
            $('#wcs4-management-form-wrapper').toggleClass('is-open');
        });
        $('#wcs4-reset-form').click(function () {
            $('#wcs4-management-form-wrapper').removeClass('is-open');
            WCS4_LIB.remove_message();
            $('#wcs4-reset-form').hide();
            $('#wcs4-management-form-wrapper form').one('change.reset', function () {
                $('#wcs4-reset-form').show();
            });
            $(this).closest('form').find('input,select').change();
        });
        $('#wcs4-management-form-wrapper form').one('change.reset', function () {
            $('#wcs4-reset-form').show();
        });
    };

    /**
     * Binds the colorpicker plugin to the selectors
     */
    var bind_colorpickers = function () {
        $(document).on('click.wcs_colorpicker', '.wcs_colorpicker', function (index) {
            var elementName = $(this).attr('id');
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

    var bind_reset_settings = function () {
        $('#wcs4-reset-database input').click(function (e) {
            e.preventDefault();
            entry = {
                action: $(this).attr('name'),
                security: WCS4_AJAX_OBJECT.ajax_nonce,
            };
            var confirm = window.confirm(WCS4_AJAX_OBJECT.reset_warning);
            if (!confirm) {
                return;
            }
            $('#wcs4-reset-database .spinner').addClass('is-active');
            $.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
                WCS4_LIB.show_message(data.response, data.result);
            }).fail(function (err) {
                console.error(err);
            }).always(function () {
                $('#wcs4-reset-database .spinner').removeClass('is-active');
            });
        });
    };


    var load_editor = function () {
        if ($('.code_editor').length) {
            var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    indentUnit: 2,
                    tabSize: 2,
                    mode: 'css',
                }
            );
            $('.code_editor').each(function (k, v) {
                wp.codeEditor.initialize($('.code_editor:eq('+k+')'), editorSettings);
            });
        }
    };

})(jQuery);