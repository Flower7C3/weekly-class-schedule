<?php

/**
 * Create the subject, teacher, student, and classroom post types.
 */

use WCS4\Controller\Settings;
use WCS4\Helper\DB;

add_action('init', static function () {
    $wcs4_settings = Settings::load_settings();

    # Register subject
    if (!empty($wcs4_settings['subject_taxonomy_slug'])) {
        register_taxonomy(WCS4_TAXONOMY_TYPE_BRANCH, WCS4_TAXONOMY_TYPES_WHITELIST[WCS4_TAXONOMY_TYPE_BRANCH], array(
            'labels' => array(
                'name' => _x('Branches', 'taxonomy general name', 'wcs4'),
                'singular_name' => _x('Branch', 'taxonomy singular name', 'wcs4'),
                'menu_name' => __('Branches', 'wcs4'),
                'all_items' => __('All Branches', 'wcs4'),
                'parent_item' => null,
                'parent_item_colon' => null,
                'add_new_item' => __('Add New Branch', 'wcs4'),
                'edit_item' => __('Edit Branch', 'wcs4'),
                'new_item_name' => __('New Branch Name', 'wcs4'),
                'separate_items_with_commas' => __('Separate branches with commas', 'wcs4'),
                'add_or_remove_items' => __('Add or remove branches', 'wcs4'),
                'choose_from_most_used' => __('Choose from the most used branches', 'wcs4'),
                'search_items' => __('Search Branches', 'wcs4'),
                'not_found' => __('No branches found.', 'wcs4'),
                'no_terms' => __('No branches.', 'wcs4'),
            ),
            'hierarchical' => ('yes' === $wcs4_settings['subject_taxonomy_hierarchical']),
            'public' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array('slug' => $wcs4_settings['subject_taxonomy_slug']),
        ));
    }
    register_post_type(WCS4_POST_TYPE_SUBJECT, array(
        'labels' => array(
            'name' => _x('Subjects', 'post type general name', 'wcs4'),
            'singular_name' => _x('Subject', 'post type singular name', 'wcs4'),
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
        'hierarchical' => false,
        'public' => true, # also: publicly_queryable, show_ui, show_in_admin_bar
        'exclude_from_search' => (bool)$wcs4_settings['subject_archive_slug'],
        'show_in_nav_menus' => (bool)$wcs4_settings['subject_post_slug'],
        'has_archive' => $wcs4_settings['subject_archive_slug'] ?: false,
        'rewrite' => array(
            'slug' => $wcs4_settings['subject_post_slug'] ?: false,
            'with_front' => true,
            'feeds' => false,
            'pages' => true,
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
        ),
        'menu_icon' => 'dashicons-welcome-learn-more',
    ));

    # Register teacher
    if (!empty($wcs4_settings['teacher_taxonomy_slug'])) {
        register_taxonomy(
            WCS4_TAXONOMY_TYPE_SPECIALIZATION,
            WCS4_TAXONOMY_TYPES_WHITELIST[WCS4_TAXONOMY_TYPE_SPECIALIZATION],
            array(
                'labels' => array(
                    'name' => _x('Specializations', 'taxonomy general name', 'wcs4'),
                    'singular_name' => _x('Specialization', 'taxonomy singular name', 'wcs4'),
                    'menu_name' => __('Specializations', 'wcs4'),
                    'all_items' => __('All Specializations', 'wcs4'),
                    'parent_item' => null,
                    'parent_item_colon' => null,
                    'add_new_item' => __('Add New Specialization', 'wcs4'),
                    'edit_item' => __('Edit Specialization', 'wcs4'),
                    'new_item_name' => __('New Specialization Name', 'wcs4'),
                    'separate_items_with_commas' => __('Separate specializations with commas', 'wcs4'),
                    'add_or_remove_items' => __('Add or remove specializations', 'wcs4'),
                    'choose_from_most_used' => __('Choose from the most used specializations', 'wcs4'),
                    'search_items' => __('Search Specializations', 'wcs4'),
                    'not_found' => __('No specializations found.', 'wcs4'),
                    'no_terms' => __('No specializations.', 'wcs4'),
                ),
                'hierarchical' => ('yes' === $wcs4_settings['teacher_taxonomy_hierarchical']),
                'public' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => $wcs4_settings['teacher_taxonomy_slug']),
            )
        );
    }
    register_post_type(WCS4_POST_TYPE_TEACHER, array(
        'labels' => array(
            'name' => _x('Teachers', 'post type general name', 'wcs4'),
            'singular_name' => _x('Teacher', 'post type singular name', 'wcs4'),
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
        'hierarchical' => false,
        'public' => true, # also: publicly_queryable, show_ui, show_in_admin_bar
        'exclude_from_search' => (bool)$wcs4_settings['teacher_archive_slug'],
        'show_in_nav_menus' => (bool)$wcs4_settings['teacher_post_slug'],
        'has_archive' => $wcs4_settings['teacher_archive_slug'] ?: false,
        'rewrite' => array(
            'slug' => $wcs4_settings['teacher_post_slug'] ?: false,
            'with_front' => true,
            'feeds' => false,
            'pages' => true,
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
        ),
        'menu_icon' => 'dashicons-businessperson',
    ));

    # Register student
    if (!empty($wcs4_settings['student_taxonomy_slug'])) {
        register_taxonomy(WCS4_TAXONOMY_TYPE_GROUP, WCS4_TAXONOMY_TYPES_WHITELIST[WCS4_TAXONOMY_TYPE_GROUP], array(
            'labels' => array(
                'name' => _x('Groups', 'taxonomy general name', 'wcs4'),
                'singular_name' => _x('Group', 'taxonomy singular name', 'wcs4'),
                'menu_name' => __('Groups', 'wcs4'),
                'all_items' => __('All Groups', 'wcs4'),
                'parent_item' => null,
                'parent_item_colon' => null,
                'add_new_item' => __('Add New Group', 'wcs4'),
                'edit_item' => __('Edit Group', 'wcs4'),
                'new_item_name' => __('New Group Name', 'wcs4'),
                'separate_items_with_commas' => __('Separate groups with commas', 'wcs4'),
                'add_or_remove_items' => __('Add or remove groups', 'wcs4'),
                'choose_from_most_used' => __('Choose from the most used groups', 'wcs4'),
                'search_items' => __('Search Groups', 'wcs4'),
                'not_found' => __('No groups found.', 'wcs4'),
                'no_terms' => __('No groups.', 'wcs4'),
            ),
            'hierarchical' => ('yes' === $wcs4_settings['student_taxonomy_hierarchical']),
            'public' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array('slug' => $wcs4_settings['student_taxonomy_slug']),
        ));
    }
    register_post_type(WCS4_POST_TYPE_STUDENT, array(
        'labels' => array(
            'name' => _x('Students', 'post type general name', 'wcs4'),
            'singular_name' => _x('Student', 'post type singular name', 'wcs4'),
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
        'hierarchical' => false,
        'public' => true, # also: publicly_queryable, show_ui, show_in_admin_bar
        'exclude_from_search' => (bool)$wcs4_settings['student_archive_slug'],
        'show_in_nav_menus' => (bool)$wcs4_settings['student_post_slug'],
        'has_archive' => $wcs4_settings['student_archive_slug'] ?: false,
        'rewrite' => array(
            'slug' => $wcs4_settings['student_post_slug'] ?: false,
            'with_front' => true,
            'feeds' => false,
            'pages' => true,
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
        ),
        'menu_icon' => 'dashicons-groups',
    ));

    # Register classroom
    if (!empty($wcs4_settings['classroom_taxonomy_slug'])) {
        register_taxonomy(
            WCS4_TAXONOMY_TYPE_LOCATION,
            WCS4_TAXONOMY_TYPES_WHITELIST[WCS4_TAXONOMY_TYPE_LOCATION],
            array(
                'labels' => array(
                    'name' => _x('Locations', 'taxonomy general name', 'wcs4'),
                    'singular_name' => _x('Location', 'taxonomy singular name', 'wcs4'),
                    'menu_name' => __('Locations', 'wcs4'),
                    'all_items' => __('All Locations', 'wcs4'),
                    'parent_item' => null,
                    'parent_item_colon' => null,
                    'add_new_item' => __('Add New Location', 'wcs4'),
                    'edit_item' => __('Edit Location', 'wcs4'),
                    'new_item_name' => __('New Location Name', 'wcs4'),
                    'separate_items_with_commas' => __('Separate locations with commas', 'wcs4'),
                    'add_or_remove_items' => __('Add or remove locations', 'wcs4'),
                    'choose_from_most_used' => __('Choose from the most used locations', 'wcs4'),
                    'search_items' => __('Search Locations', 'wcs4'),
                    'not_found' => __('No locations found.', 'wcs4'),
                    'no_terms' => __('No locations.', 'wcs4'),
                ),
                'hierarchical' => ('yes' === $wcs4_settings['classroom_taxonomy_hierarchical']),
                'public' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => $wcs4_settings['classroom_taxonomy_slug']),
            )
        );
    }
    register_post_type(WCS4_POST_TYPE_CLASSROOM, array(
        'labels' => array(
            'name' => _x('Classrooms', 'post type general name', 'wcs4'),
            'singular_name' => _x('Classroom', 'post type singular name', 'wcs4'),
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
        'hierarchical' => false,
        'public' => true, # also: publicly_queryable, show_ui, show_in_admin_bar
        'exclude_from_search' => (bool)$wcs4_settings['classroom_archive_slug'],
        'show_in_nav_menus' => (bool)$wcs4_settings['classroom_post_slug'],
        'has_archive' => $wcs4_settings['classroom_archive_slug'] ?: false,
        'rewrite' => array(
            'slug' => $wcs4_settings['classroom_post_slug'] ?: false,
            'with_front' => true,
            'feeds' => false,
            'pages' => true,
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
        ),
        'menu_icon' => 'dashicons-building',
    ));
    add_theme_support('post-thumbnails');
    foreach (WCS4_POST_TYPES as $post_type) {
        add_post_type_support($post_type, 'thumbnail');
    }
});

foreach (WCS4_POST_TYPES as $post_type) {
    add_filter('manage_' . $post_type . '_posts_columns', function ($columns) {
        $offset = array_search('date', array_keys($columns), true);
        $new_columns = [];
        $new_columns['password'] = __('Password');
        return array_merge(array_slice($columns, 0, $offset), $new_columns, array_slice($columns, $offset, null));
    });
    add_action('manage_' . $post_type . '_posts_custom_column', function ($column_key, $post_id) {
        if ('password' === $column_key) {
            $the_post = get_post($post_id);
            if ($the_post->post_password) {
                echo '<code>' . $the_post->post_password . '</code>';
            } else {
                echo '---';
            }
        }
    }, 10, 2);
}
add_action('add_meta_boxes', static function () {
    add_meta_box(
        'links',
        __('Links'),
        static function ($post) {
            echo '<ul>';
            foreach (wcs4_actions([], $post) as $action) {
                echo '<li>' . $action . '</li>';
            }
            echo '</ul>';
        },
        null,
        'side',
        'high'
    );
});
add_filter('post_row_actions', 'wcs4_actions', 10, 2);

function wcs4_actions($actions, $the_post)
{
    if (!in_array($the_post->post_type, WCS4_POST_TYPES, true)) {
        return $actions;
    }
    $type = str_replace('wcs4_', '', $the_post->post_type);
    if (current_user_can(WCS4_SCHEDULE_VIEW_CAPABILITY)) {
        $actions[] = sprintf(
            '<a href="%s">%s</a>',
            'admin.php?' . http_build_query([
                'page' => 'wcs4',
                $type => $the_post->ID,
            ]),
            __('Schedule', 'wcs4')
        );
    }
    if (current_user_can(WCS4_JOURNAL_VIEW_CAPABILITY)) {
        $actions[] = sprintf(
            '<a href="%s">%s</a>',
            'admin.php?' . http_build_query([
                'page' => 'wcs4-journal',
                $type => $the_post->ID,
                'date_from' => date('Y-m-01'),
                'date_upto' => date('Y-m-d'),
            ]),
            __('Journals', 'wcs4')
        );
    }
    if (current_user_can(WCS4_WORK_PLAN_VIEW_CAPABILITY)) {
        $actions[] = sprintf(
            '<a href="%s">%s</a>',
            'admin.php?' . http_build_query([
                'page' => 'wcs4-work-plan',
                $type => $the_post->ID,
            ]),
            __('Work Plans', 'wcs4')
        );
    }
    if (current_user_can(WCS4_PROGRESS_VIEW_CAPABILITY)) {
        $actions[] = sprintf(
            '<a href="%s">%s</a>',
            'admin.php?' . http_build_query([
                'page' => 'wcs4-progress',
                $type => $the_post->ID,
            ]),
            __('Progresses', 'wcs4')
        );
    }
    return $actions;
}


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
    if (false === $version) {
        DB::create_schema();
    }
});

