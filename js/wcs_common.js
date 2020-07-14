/**
 * The Weekly Class Schedule 4 common JavaScript library.
 */

var WCS4_LIB = {
    /**
     * Applies hover and qtip to table layouts.
     */
    apply_qtip: function () {
        jQuery('.wcs4-qtip-box').each(function () {
            var html = jQuery('.wcs4-qtip-data', this).html();

            jQuery('a.wcs4-qtip', this).qtip({
                content: {
                    text: html
                },
                show: {
                    event: 'click',
                },
                style: {lessons: 'wcs4-qtip-tip'}
            })
        });
    }
};