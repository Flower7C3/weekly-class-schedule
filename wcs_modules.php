<?php
/**
 * Loads all the required modules for the WCS4 plugin.
 */
include_once(ABSPATH . 'wp-includes/pluggable.php');
require_once WCS4_PLUGIN_DIR . '/includes/wcs_db.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_utils.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_schedule.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_ajax.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_admin.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_settings.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_output.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_css.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_widgets.php';
