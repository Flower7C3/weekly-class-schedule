(function ($) {
    'use strict';

    function setPreview($input, attachmentId, selectedJson) {
        var $wrap = $('.wcs4-print-header-preview[data-for="' + $input.attr('id') + '"]');
        $wrap.empty();
        if (!attachmentId) {
            return;
        }
        function showUrl(url) {
            if (url) {
                $wrap.append($('<img>', {
                    src: url,
                    alt: '',
                    class: 'attachment-thumbnail size-thumbnail',
                    loading: 'lazy',
                    decoding: 'async'
                }));
            }
        }
        if (selectedJson && selectedJson.url) {
            var u = selectedJson.sizes && selectedJson.sizes.thumbnail
                ? selectedJson.sizes.thumbnail.url
                : selectedJson.url;
            showUrl(u);
            return;
        }
        if (typeof wp === 'undefined' || !wp.media || !wp.media.attachment) {
            return;
        }
        var att = wp.media.attachment(attachmentId);
        att.fetch().done(function () {
            var url = att.get('sizes') && att.get('sizes').thumbnail
                ? att.get('sizes').thumbnail.url
                : att.get('url');
            showUrl(url);
        });
    }

    $(document).on('click', '.wcs4-pick-print-header-image', function (e) {
        e.preventDefault();
        if (typeof wp === 'undefined' || !wp.media) {
            return;
        }
        var $btn = $(this);
        var targetId = $btn.data('target');
        var $input = $('#' + targetId);
        if (!$input.length) {
            return;
        }
        var title = (typeof WCS4_BASIC_OPTIONS !== 'undefined' && WCS4_BASIC_OPTIONS.frameTitle)
            ? WCS4_BASIC_OPTIONS.frameTitle
            : 'Select image';
        var frame = wp.media({
            title: title,
            library: {type: 'image'},
            multiple: false
        });
        frame.on('select', function () {
            var model = frame.state().get('selection').first();
            if (!model) {
                return;
            }
            var attachment = model.toJSON ? model.toJSON() : model;
            var id = attachment.id != null ? attachment.id : model.get('id');
            id = parseInt(String(id), 10) || 0;
            $input.val(String(id));
            setPreview($input, id, attachment);
        });
        frame.open();
    });

    $(document).on('click', '.wcs4-clear-print-header-image', function (e) {
        e.preventDefault();
        var $input = $('#' + $(this).data('target'));
        $input.val('0');
        setPreview($input, 0, null);
    });
})(jQuery);
