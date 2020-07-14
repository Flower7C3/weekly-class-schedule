<?php
/*
Plugin Name: Weekly Class Schedule
Description: Weekly Class Schedule generates a weekly schedule of lessons. It provides you with an easy way to manage and update the schedule as well as the subjects, teachers, students and classrooms database.
Version: 3.20
Text Domain: wcs4
Author: Kwiatek.pro, Pulsar Web Design
Author URI: https://kwiatek.pro
License: GPL2

Copyright 2011  Pulsar Web Design  (email : info@pulsarwebdesign.com)
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

define('WCS4_VERSION', '4.03');

define('WCS4_REQUIRED_WP_VERSION', '4.0');

if (!defined('WCS4_PLUGIN_BASENAME')) {
    define('WCS4_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

if (!defined('WCS4_PLUGIN_NAME')) {
    define('WCS4_PLUGIN_NAME', trim(dirname(WCS4_PLUGIN_BASENAME), '/'));
}

if (!defined('WCS4_PLUGIN_DIR')) {
    define('WCS4_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
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

if (!defined('WCS4_POST_TYPES_WHITELIST')) {
    define('WCS4_POST_TYPE_SUBJECT', 'wcs4_subject');
}
if (!defined('WCS4_POST_TYPES_WHITELIST')) {
    define('WCS4_POST_TYPE_TEACHER', 'wcs4_teacher');
}
if (!defined('WCS4_POST_TYPES_WHITELIST')) {
    define('WCS4_POST_TYPE_STUDENT', 'wcs4_student');
}
if (!defined('WCS4_POST_TYPES_WHITELIST')) {
    define('WCS4_POST_TYPE_CLASSROOM', 'wcs4_classroom');
}
if (!defined('WCS4_POST_TYPES_WHITELIST')) {
    define('WCS4_POST_TYPES_WHITELIST', [WCS4_POST_TYPE_SUBJECT, WCS4_POST_TYPE_TEACHER, WCS4_POST_TYPE_STUDENT, WCS4_POST_TYPE_CLASSROOM]);
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
    'b' => array(),
    'blockquote' => array(
        'cite' => true,
    ),
    'cite' => array(),
    'code' => array(),
    'del' => array(
        'datetime' => true,
    ),
    'small' => array(),
    'br' => array(),
    'em' => array(),
    'i' => array(),
    'q' => array(
        'cite' => true,
    ),
    'strike' => array(),
    'strong' => array(),
);

/**
 * Load modules.
 */
require_once WCS4_PLUGIN_DIR . '/wcs_modules.php';

/**
 * Create the subject, teacher, student, and classroom post types.
 */
add_action('init', static function () {
    $wcs4_settings = wcs4_load_settings();

    # Register class
    register_post_type(WCS4_POST_TYPE_SUBJECT,
        array(
            'labels' => array(
                'name' => _x('Subjects', 'Post Type General Name', 'wcs4'),
                'singular_name' => _x('Subject', 'Post Type Singular Name', 'wcs4'),
                'menu_name' => _x('Subjects', 'menu', 'wcs4'),
                'all_items' => _x('All Subjects', 'page title', 'wcs4'),
                'view_item' => _x('View Subject', 'page title', 'wcs4'),
                'add_new_item' => _x('Add New Subject', 'page title', 'wcs4'),
                'add_new' => _x('Add New Subject', 'menu', 'wcs4'),
                'edit_item' => _x('Edit Subject', 'page title', 'wcs4'),
                'search_items' => __('Search Subject', 'wcs4'),
                'not_found' => __('Not Found', 'wcs4'),
                'not_found_in_trash' => __('Not found in Trash', 'wcs4'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'public' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => $wcs4_settings['subject_archive_slug'] ? true : false,
            'show_in_nav_menus' => $wcs4_settings['subject_item_slug'] ? true : false,
            'has_archive' => $wcs4_settings['subject_archive_slug'] ?: false,
            'rewrite' => array(
                'slug' => $wcs4_settings['subject_item_slug'] ?: false,
                'with_front' => true,
                'feeds' => false,
                'pages' => true,
            ),
            'supports' => array(
                'title', 'editor',
                'thumbnail',
                'author',
            ),
            'menu_icon' => 'dashicons-welcome-learn-more',
        )
    );

    # Register teacher
    register_post_type(WCS4_POST_TYPE_TEACHER,
        array(
            'labels' => array(
                'name' => _x('Teachers', 'Post Type General Name', 'wcs4'),
                'singular_name' => _x('Teacher', 'Post Type Singular Name', 'wcs4'),
                'menu_name' => _x('Teachers', 'menu', 'wcs4'),
                'all_items' => _x('All Teachers', 'page title', 'wcs4'),
                'view_item' => _x('View Teacher', 'page title', 'wcs4'),
                'add_new_item' => _x('Add New Teacher', 'page title', 'wcs4'),
                'add_new' => _x('Add New Teacher', 'menu', 'wcs4'),
                'edit_item' => _x('Edit Teacher', 'page title', 'wcs4'),
                'search_items' => __('Search Teacher', 'wcs4'),
                'not_found' => __('Not Found', 'wcs4'),
                'not_found_in_trash' => __('Not found in Trash', 'wcs4'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'public' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => $wcs4_settings['teacher_archive_slug'] ? true : false,
            'show_in_nav_menus' => $wcs4_settings['teacher_item_slug'] ? true : false,
            'has_archive' => $wcs4_settings['teacher_archive_slug'] ?: false,
            'rewrite' => array(
                'slug' => $wcs4_settings['teacher_item_slug'] ?: false,
                'with_front' => true,
                'feeds' => false,
                'pages' => true,
            ),
            'supports' => array(
                'title', 'editor',
                'thumbnail',
                'author',
            ),
            'menu_icon' => 'dashicons-businessperson',
        )
    );

    # Register student
    register_post_type(WCS4_POST_TYPE_STUDENT,
        array(
            'labels' => array(
                'name' => _x('Students', 'Post Type General Name', 'wcs4'),
                'singular_name' => _x('Student', 'Post Type Singular Name', 'wcs4'),
                'menu_name' => _x('Students', 'menu', 'wcs4'),
                'all_items' => _x('All Students', 'page title', 'wcs4'),
                'view_item' => _x('View Student', 'page title', 'wcs4'),
                'add_new_item' => _x('Add New Student', 'page title', 'wcs4'),
                'add_new' => _x('Add New Student', 'menu', 'wcs4'),
                'edit_item' => _x('Edit Student', 'page title', 'wcs4'),
                'search_items' => __('Search Student', 'wcs4'),
                'not_found' => __('Not Found', 'wcs4'),
                'not_found_in_trash' => __('Not found in Trash', 'wcs4'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'public' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => $wcs4_settings['student_archive_slug'] ? true : false,
            'show_in_nav_menus' => $wcs4_settings['student_item_slug'] ? true : false,
            'has_archive' => $wcs4_settings['student_archive_slug'] ?: false,
            'rewrite' => array(
                'slug' => $wcs4_settings['student_item_slug'] ?: false,
                'with_front' => true,
                'feeds' => false,
                'pages' => true,
            ),
            'supports' => array(
                'title', 'editor',
                'thumbnail',
                'author',
            ),
            'menu_icon' => 'dashicons-groups',
        )
    );

    # Register classroom
    register_post_type(WCS4_POST_TYPE_CLASSROOM,
        array(
            'labels' => array(
                'name' => _x('Classrooms', 'Post Type General Name', 'wcs4'),
                'singular_name' => _x('Classroom', 'Post Type Singular Name', 'wcs4'),
                'menu_name' => _x('Classrooms', 'menu', 'wcs4'),
                'all_items' => _x('All Classrooms', 'page title', 'wcs4'),
                'view_item' => _x('View Classroom', 'page title', 'wcs4'),
                'add_new_item' => _x('Add New Classroom', 'page title', 'wcs4'),
                'add_new' => _x('Add New Classroom', 'menu', 'wcs4'),
                'edit_item' => _x('Edit Classroom', 'page title', 'wcs4'),
                'search_items' => __('Search Classroom', 'wcs4'),
                'not_found' => __('Not Found', 'wcs4'),
                'not_found_in_trash' => __('Not found in Trash', 'wcs4'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'public' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => $wcs4_settings['classroom_archive_slug'] ? true : false,
            'show_in_nav_menus' => $wcs4_settings['classroom_item_slug'] ? true : false,
            'has_archive' => $wcs4_settings['classroom_archive_slug'] ?: false,
            'rewrite' => array(
                'slug' => $wcs4_settings['classroom_item_slug'] ?: false,
                'with_front' => true,
                'feeds' => false,
                'pages' => true,
            ),
            'supports' => array(
                'title', 'editor',
                'thumbnail',
                'author',
            ),
            'menu_icon' => 'dashicons-building',
        )
    );
    add_theme_support('post-thumbnails');
    add_post_type_support(WCS4_POST_TYPE_SUBJECT, 'thumbnail');
    add_post_type_support(WCS4_POST_TYPE_TEACHER, 'thumbnail');
    add_post_type_support(WCS4_POST_TYPE_STUDENT, 'thumbnail');
    add_post_type_support(WCS4_POST_TYPE_CLASSROOM, 'thumbnail');
});

/**
 * Append schedule to single page
 */
add_filter('the_content', static function ($content) {
    $post_type = get_post_type();
    if (is_single() && in_array($post_type, WCS4_POST_TYPES_WHITELIST, true)) {
        $postId = get_the_id();
        $post_type_key = str_replace('wcs4_', '', $post_type);
        $wcs4_settings = wcs4_load_settings();
        $layout = $wcs4_settings[$post_type_key . '_schedule_layout'];
        if ('none' !== $layout && NULL !== $layout) {
            $content .= '<h3>' . __('Schedule', 'wcs4') . '</h3>';
            $template_table_short = $wcs4_settings[$post_type_key . '_schedule_template_table_short'];
            $template_table_details = $wcs4_settings[$post_type_key . '_schedule_template_table_details'];
            $template_list = $wcs4_settings[$post_type_key . '_schedule_template_list'];
            $params = [];
            $params[] = '' . $post_type_key . '="#' . $postId . '"';
            $params[] = 'layout="' . $layout . '"';
            $params[] = 'template_table_short="' . $template_table_short . '"';
            $params[] = 'template_table_details="' . $template_table_details . '"';
            $params[] = 'template_list="' . $template_list . '"';
            $content .= '[wcs  ' . implode(' ', $params) . ']';
        }
    }
    return $content;
});

/**
 * Order custom types by title
 */
add_action('pre_get_posts', static function ($query) {
    if (is_admin()) {
        return $query;
    }
    if (isset($query->query_vars['post_type']) && in_array($query->query_vars['post_type'], WCS4_POST_TYPES_WHITELIST, true)) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
    }
    return $query;
});

/**
 * Register admin pages (schedule management, settings, etc...).
 */
if (!defined('WCS4_SCHEDULE_VIEW_CAPABILITY')) {
    define('WCS4_SCHEDULE_VIEW_CAPABILITY', 'wcs4_schedule_view');
}
if (!defined('WCS4_SCHEDULE_MANAGE_CAPABILITY')) {
    define('WCS4_SCHEDULE_MANAGE_CAPABILITY', 'wcs4_schedule_manage');
}
if (!defined('WCS4_STANDARD_OPTIONS_CAPABILITY')) {
    define('WCS4_STANDARD_OPTIONS_CAPABILITY', 'wcs4_standard_options');
}
if (!defined('WCS4_ADVANCED_OPTIONS_CAPABILITY')) {
    define('WCS4_ADVANCED_OPTIONS_CAPABILITY', 'wcs4_advanced_options');
}
add_action('admin_menu', static function () {
    $page_management = add_menu_page(__('Schedule Management', 'wcs4'),
        __('Schedule', 'wcs4'),
        WCS4_SCHEDULE_VIEW_CAPABILITY,
        'wcs4-schedule',
        'wcs4_schedule_management_page_callback',
        'dashicons-schedule', 50);

    $page_standard_options = add_submenu_page('wcs4-schedule',
        __('Standard Options', 'wcs4'),
        __('Standard Options', 'wcs4'),
        WCS4_STANDARD_OPTIONS_CAPABILITY,
        'wcs4-standard-options',
        'wcs4_standard_options_page_callback');

    add_submenu_page('wcs4-schedule',
        __('Advanced Options', 'wcs4'),
        __('Advanced Options', 'wcs4'),
        WCS4_ADVANCED_OPTIONS_CAPABILITY,
        'wcs4-advanced',
        'wcs4_advanced_options_page_callback');

    $help_tabs = [];
    $help_tabs[] = [
        'id' => 'wcs4_help_shortcode',
        'title' => _x('Using shortcode', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_shortcode_callback',
    ];
    $help_tabs[] = [
        'id' => 'wcs4_help_placeholders',
        'title' => _x('Placeholders', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_placeholders_callback',
    ];
    $help_tabs[] = [
        'id' => 'wcs4_help_allowed_html',
        'title' => _x('HTML tags in template', 'help title', 'wcs4'),
        'callback' => 'wcs4_help_allowed_html_callback',
    ];
    add_action('load-' . $page_management, static function () use ($help_tabs) {
        $screen = get_current_screen();
        foreach ($help_tabs as $tab) {
            $screen->add_help_tab($tab);
        }
    });
    add_action('load-' . $page_standard_options, static function () use ($help_tabs) {
        $screen = get_current_screen();
        foreach ($help_tabs as $tab) {
            $screen->add_help_tab($tab);
        }
    });
});

/**
 * Loads plugin text domain
 */
add_action('init', static function () {
    load_plugin_textdomain('wcs4');
    $role = get_role('administrator');
    $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
    $role->add_cap(WCS4_SCHEDULE_MANAGE_CAPABILITY, true);
    $role->add_cap(WCS4_STANDARD_OPTIONS_CAPABILITY, true);
    $role->add_cap(WCS4_ADVANCED_OPTIONS_CAPABILITY, true);
    $role = get_role('editor');
    $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
    $role->add_cap(WCS4_SCHEDULE_MANAGE_CAPABILITY, true);
    $role->add_cap(WCS4_STANDARD_OPTIONS_CAPABILITY, true);
    $role = get_role('author');
    $role->add_cap(WCS4_SCHEDULE_VIEW_CAPABILITY, true);
});

/**
 * Updates the version in the options table.
 */
add_action('admin_init', static function () {
    $version = get_option('wcs4_version');
    if (is_admin() && $version < WCS4_VERSION) {
        update_option('wcs4_version', WCS4_VERSION);
    }
});

/**
 * Hashed post slug
 */
add_filter("wp_unique_post_slug", static function ($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
    if (isset($post_type) && in_array($post_type, WCS4_POST_TYPES_WHITELIST, true)) {
        $post_type_key = str_replace('wcs4_', '', $post_type);
        $wcs4_settings = wcs4_load_settings();
        $hashed_slug = $wcs4_settings[$post_type_key . '_hashed_slug'];
        if ('yes' === $hashed_slug) {
            $post_title = get_the_title($post_ID);
            $slug = md5($post_ID . '-' . $post_title);
        }
    }
    return $slug;
}, 10, 6);

/**
 * Register activation hook
 */
register_activation_hook(__FILE__, static function () {
    do_action('wcs4_activate_action');
});

/**
 * Activation
 */
add_action('wcs4_activate_action', static function () {
    $version = get_option('wcs4_version');
    if (FALSE === $version) {
        wcs4_create_schema();
    }
});
