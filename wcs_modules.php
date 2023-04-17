<?php
/**
 * Loads all the required modules for the WCS4 plugin.
 */
include_once(ABSPATH . 'wp-includes/pluggable.php');
require_once WCS4_PLUGIN_DIR . '/vendor/autoload.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_actions.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_admin_hooks.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_content.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_css.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_init.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_post_hook.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_shortcode.php';
require_once WCS4_PLUGIN_DIR . '/includes/wcs_utils.php';
