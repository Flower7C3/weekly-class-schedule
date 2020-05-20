<?php

/**
 * Injecting our custom CSS
 */
add_action('wp_head', static function () {
    $wcs4_options = wcs4_load_settings();

    $base_color = $wcs4_options['color_base'];
    $details_box = $wcs4_options['color_details_box'];
    $text = $wcs4_options['color_text'];
    $border = $wcs4_options['color_border'];
    $heading_text = $wcs4_options['color_heading_text'];
    $heading_bg = $wcs4_options['color_headings_background'];
    $bg = $wcs4_options['color_background'];
    $qtip_bg = $wcs4_options['color_qtip_background'];
    $links = $wcs4_options['color_links'];

    echo '';

    /* ------------- CSS ------------ */
    $dynamic_css =

        <<<CSS
	.wcs4-grid-lesson {
	    background-color: #$base_color;
	    color: #$text;
	}
	.wcs4-grid-lesson a {
	    color: #$links;
	}
	.wcs4-details-box-container {
	    background-color: #$details_box;
	}
	body .wcs4-qtip-tip {
	    background-color: #$qtip_bg;
	    border: 1px solid #$border;
	    
	}
	.wcs4-schedule-wrapper table th {
	    background-color: #$heading_bg;
	    color: #$heading_text;
	}
	.wcs4-schedule-wrapper table {
		    background-color: #$bg;
	}
	.wcs4-schedule-wrapper table,
	.wcs4-schedule-wrapper table td,
	.wcs4-schedule-wrapper table th {
	    border: 1px solid #$border;
	}
CSS;

    /* ------------- END ------------ */

    echo '<style>';
    echo $dynamic_css;
    echo '</style>';

});
