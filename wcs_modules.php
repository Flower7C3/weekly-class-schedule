<?php
/**
 * Loads all the required modules for the WCS4 plugin.
 */
include_once(ABSPATH . 'wp-includes/pluggable.php');
require_once WCS4_PLUGIN_DIR . '/includes/classes/db/WCS_Entity_Blameable_Trait.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/db/WCS_Entity_Timestampable_Trait.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/WCS_DB.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/db/WCS_DB_Item.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/db/WCS_DB_Lesson_Item.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/db/WCS_DB_Journal_Item.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/db/WCS_DB_Progress_Item.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/WCS_Schedule.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/WCS_Journal.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/WCS_Progress.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/WCS_Admin.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/WCS_Settings.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/WCS_Output.php';
require_once WCS4_PLUGIN_DIR . '/includes/classes/WCS4_TodayClassesWidget.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_init.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_content.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_post_hook.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_utils.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_admin_hooks.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_css.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_shortcode.php';
