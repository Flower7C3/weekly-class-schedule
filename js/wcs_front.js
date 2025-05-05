/**
 * Scripts for the Weekly Class Schedule 4.0 front-end.
 */
(function ($) {
    $(document).ready(function () {
        WCS4_LIB.apply_qtip();

        $(document).on('change.form-invalid', '.form-invalid input,.form-invalid textarea,.form-invalid select', function () {
            $(this).closest('.form-invalid').find('.error').remove();
        });
        $('.wcs4_schedule_wrapper .toggle').on('click', function () {
            // if (document.fullscreenElement) {
            //     $('.toggle').removeClass('fa-window-minimize').addClass('fa-window-maximize')
            //     document.exitFullscreen();
            // } else {
                $('.wcs4_schedule_grid').get(0).requestFullscreen();
                // $('.toggle').removeClass('fa-window-maximize').addClass('fa-window-minimize')
            // }
        });

        html2canvas(document.querySelector(".wcs4_schedule_grid")).then((canvas) => {
            console.log('canvas')
            const data = canvas.toDataURL("image/png;base64");
            const downloadLink = document.querySelector(".wcs4_schedule_wrapper .download");
            downloadLink.download = $('#wcs_schedule-shortcode-wrapper h2').text() + ' ' + $('.entry-title').text();
            downloadLink.href = data;
            console.log(downloadLink)
        });
    });
})(jQuery);

let WCS4_FRONT = (function ($) {

    let bind_edit_handler = function (scope, set_entry_data_to_form) {
        $('#wcs4-' + scope + '-modal').on('hidden.bs.modal', function () {
            $('.wcs4-management-form-wrapper form').trigger("reset");
            $('.wcs4-management-form-wrapper form').find('input,textarea,select').trigger("change");
            $('[data-wcs4="management-form-title"]').text(WCS4_AJAX_OBJECT[scope].add_mode);
            $('[data-wcs4="submit-form"]').html(WCS4_AJAX_OBJECT[scope].add_item);
            $('[data-wcs4="cancel-form"]').hide();
        })
        $(document).on('click.wcs4-edit-' + scope + '-button', '[data-scope="' + scope + '"] .wcs4-edit-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form(scope, $(this).data('id'), set_entry_data_to_form, function () {
                $('#wcs4-' + scope + '-modal').modal('show');
                $('.wcs4-management-form-wrapper form').find('input,textarea,select').focus();
                $('.wcs4-management-form-wrapper form [data-readonly]').attr('readonly', true);
                $('.wcs4-management-form-wrapper form').unbind('change.reset');
                $('[data-wcs4="management-form-title"]').text(WCS4_AJAX_OBJECT[scope].edit_mode)
                $('[data-wcs4="submit-form"]').html(WCS4_AJAX_OBJECT[scope].save_item);
                $('[data-wcs4="cancel-form"]').show();
            });
            e.preventDefault();
        });
    };

    return {
        bind_edit_handler,
    }
})(jQuery);