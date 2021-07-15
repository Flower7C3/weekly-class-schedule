<?php
/**
 * Loads all the required modules for the WCS4 plugin.
 */
include_once(ABSPATH . 'wp-includes/pluggable.php');
require_once WCS4_PLUGIN_DIR . '/includes/wcs_init.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_content.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_post_hook.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_db.php';
require_once WCS4_PLUGIN_DIR . '/includes/db/lesson.php';
require_once WCS4_PLUGIN_DIR . '/includes/db/report.php';
require_once WCS4_PLUGIN_DIR . '/includes/db/item.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_utils.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_schedule.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_report.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_ajax.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_admin.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_settings.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_output.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_css.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_widgets.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_shortcode.php';
