<?php
/**
 * Loads all the required modules for the WCS4 plugin.
 */
include_once(ABSPATH . 'wp-includes/pluggable.php');
require_once WCS4_PLUGIN_DIR . '/includes/wcs_init.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_content.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_post_hook.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_db.php';
require_once WCS4_PLUGIN_DIR . '/includes/db/entity_timestampable_trait.php';
require_once WCS4_PLUGIN_DIR . '/includes/db/entity_blameable_trait.php';
require_once WCS4_PLUGIN_DIR . '/includes/db/entity_lesson.php';
require_once WCS4_PLUGIN_DIR . '/includes/db/entity_report.php';
require_once WCS4_PLUGIN_DIR . '/includes/db/entity_item.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_utils.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_schedule.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_report.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_admin.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_settings.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_output.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_css.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_widgets.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_shortcode.php';
