<?php
/*
Plugin Name: Weekly Class Schedule
Description: Weekly Class Schedule generates a weekly schedule of lessons. It provides you with an easy way to manage and update the schedule as well as the subjects, teachers, students and classrooms database.
Version: 3.45
Text Domain: wcs4
Author: Kwiatek.pro, Pulsar Web Design
Author URI: https://kwiatek.pro
License: GPL2

Copyright 2011 Pulsar Web Design (email : info@pulsarwebdesign.com)
Copyright 2020 Kwiatek.pro

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

const APP_DEVEL = true;

if (APP_DEVEL) {
    opcache_reset();
    if (isset($_GET['debug'])) {
        error_reporting(E_ALL);
        ini_set('display_errors', true);
    }
    define('WCS4_VERSION', '4.devel.' . time());
} else {
    define('WCS4_VERSION', '4.58' . time());
}


//if (in_array($_SERVER['REMOTE_ADDR'], ['185.157.14.77', '188.68.229.109'], true)) {
//    dd(__LINE__);
//}
//if($_SERVER['REMOTE_ADDR'] === '185.157.14.77') {
//    error_reporting(E_ALL);
//    ini_set('display_errors', 1);
//    echo $settings;
//    dd(unserialize($settings, ['allowed_classes'=>true]));
//}

define('WCS4_REQUIRED_WP_VERSION', '4.0');

if (!defined('WCS4_PLUGIN_BASENAME')) {
    define('WCS4_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

if (!defined('WCS4_PLUGIN_NAME')) {
    define('WCS4_PLUGIN_NAME', trim(dirname(WCS4_PLUGIN_BASENAME), '/'));
}

if (!defined('WCS4_PLUGIN_DIR')) {
    define('WCS4_PLUGIN_DIR', untrailingslashit(__DIR__));
}

if (!defined('WCS4_PLUGIN_URL')) {
    define('WCS4_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));
}


if (!defined('WCS4_DB_VERSION')) {
    define('WCS4_DB_VERSION', '2.0');
}

if (!defined('WCS4_BASE_DATE')) {
    define('WCS4_BASE_DATE', '2001-01-01');
}

if (!defined('WCS4_POST_TYPE_SUBJECT')) {
    define('WCS4_POST_TYPE_SUBJECT', 'wcs4_subject');
}
if (!defined('WCS4_TAXONOMY_TYPE_BRANCH')) {
    define('WCS4_TAXONOMY_TYPE_BRANCH', 'wcs4_branch');
}
if (!defined('WCS4_POST_TYPE_TEACHER')) {
    define('WCS4_POST_TYPE_TEACHER', 'wcs4_teacher');
}
if (!defined('WCS4_TAXONOMY_TYPE_SPECIALIZATION')) {
    define('WCS4_TAXONOMY_TYPE_SPECIALIZATION', 'wcs4_specialization');
}
if (!defined('WCS4_POST_TYPE_STUDENT')) {
    define('WCS4_POST_TYPE_STUDENT', 'wcs4_student');
}
if (!defined('WCS4_TAXONOMY_TYPE_GROUP')) {
    define('WCS4_TAXONOMY_TYPE_GROUP', 'wcs4_group');
}
if (!defined('WCS4_POST_TYPE_CLASSROOM')) {
    define('WCS4_POST_TYPE_CLASSROOM', 'wcs4_classroom');
}
if (!defined('WCS4_TAXONOMY_TYPE_LOCATION')) {
    define('WCS4_TAXONOMY_TYPE_LOCATION', 'wcs4_location');
}
if (!defined('WCS4_TAXONOMY_TYPES_WHITELIST')) {
    define('WCS4_TAXONOMY_TYPES_WHITELIST', [
        WCS4_TAXONOMY_TYPE_BRANCH => [WCS4_POST_TYPE_SUBJECT],
        WCS4_TAXONOMY_TYPE_SPECIALIZATION => [WCS4_POST_TYPE_TEACHER],
        WCS4_TAXONOMY_TYPE_GROUP => [WCS4_POST_TYPE_STUDENT],
        WCS4_TAXONOMY_TYPE_LOCATION => [WCS4_POST_TYPE_CLASSROOM, WCS4_POST_TYPE_TEACHER],
    ]);
}
if (!defined('WCS4_POST_TYPES')) {
    define('WCS4_POST_TYPES', [
        WCS4_POST_TYPE_SUBJECT,
        WCS4_POST_TYPE_TEACHER,
        WCS4_POST_TYPE_STUDENT,
        WCS4_POST_TYPE_CLASSROOM,
    ]);
}
$_WCS4_POST_TYPES_WHITELIST = [];
foreach (WCS4_TAXONOMY_TYPES_WHITELIST as $_tax => $_types) {
    $_WCS4_POST_TYPES_WHITELIST[$_types[0]] = $_tax;
}
if (!defined('WCS4_POST_TYPES_WHITELIST')) {
    define('WCS4_POST_TYPES_WHITELIST', $_WCS4_POST_TYPES_WHITELIST);
    unset($_WCS4_POST_TYPES_WHITELIST);
}

/**
 * List of allowed HTML tags for the notes field (if enabled).
 *
 * @see http://codex.wordpress.org/Function_Reference/wp_kses
 */
$wcs4_allowed_html = array(
    'a' => array(
        'href' => true,
        'title' => true,
    ),
    'abbr' => array(
        'title' => true,
    ),
    'acronym' => array(
        'title' => true,
    ),
    'b' => [],
    'blockquote' => array(
        'cite' => true,
    ),
    'cite' => [],
    'code' => [],
    'del' => array(
        'datetime' => true,
    ),
    'small' => [],
    'br' => [],
    'em' => [],
    'i' => [],
    'q' => array(
        'cite' => true,
    ),
    'strike' => [],
    'strong' => [],
);

/**
 * Load modules.
 */
require_once WCS4_PLUGIN_DIR . '/wcs_modules.php';
